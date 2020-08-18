<?php

namespace ezavalishin\LaravelJsonApiPaginator;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

/**
 * @codeCoverageIgnore
 */
class LaravelJsonApiPaginatorServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $request = app(Request::class);

        $page = $request->query('page', []);

        if (isset($page['number']) || isset($page['size'])) {
            $macro = $this->pageBasedMacro($request);
        } else {
            $macro = $this->offsetBasedMacro($request);
        }

        // Register macros
        QueryBuilder::macro('jsonApiPaginate', $macro);
        EloquentBuilder::macro('jsonApiPaginate', $macro);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laraveljsonapipaginator.php', 'laraveljsonapipaginator');
    }

    private function offsetBasedMacro(Request $request): callable
    {
        return function ($perPage = null, $columns = ['*'], array $options = []) use ($request) {
            $page = $request->query('page', []);

            if (! $perPage) {
                $perPage = ($page['limit'] ?? config('laraveljsonapipaginator.default_limit'));
            }

            $perPage = (int) ($perPage > config('laraveljsonapipaginator.max_limit') ? config('laraveljsonapipaginator.max_limit') : $perPage);

            $offset = (int) ($page['offset'] ?? 0);

            /** @var EloquentBuilder|QueryBuilder $this */
            $this->skip($offset)
                ->limit($perPage);

            $total = $this->toBase()->getCountForPagination();

            return new OffsetBasedPaginator($this->get($columns), $perPage, $offset, $total, $request, $options);
        };
    }

    private function pageBasedMacro(Request $request): callable
    {
        return function ($perPage = null, $columns = ['*'], array $options = []) use ($request) {
            $page = $request->query('page', []);

            if (! $perPage) {
                $perPage = $page['size'] ?? config('laraveljsonapipaginator.default_limit');
            }

            $perPage = (int) ($perPage > config('laraveljsonapipaginator.max_limit') ? config('laraveljsonapipaginator.max_limit') : $perPage);

            $number = max((int) ($page['number'] ?? 1), 1);

            /** @var EloquentBuilder|QueryBuilder $this */
            $this->skip(($number - 1) * $perPage)
                ->limit($perPage);

            $total = $this->toBase()->getCountForPagination();

            return new PageBasedPaginator($this->get($columns), $total, $perPage, $request, $number, $options);
        };
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        $configPath = __DIR__ . '/../config/laraveljsonapipaginator.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('laraveljsonapipaginator.php');
        } else {
            $publishPath = base_path('config/laraveljsonapipaginator.php');
        }

        $this->publishes([
            $configPath => $publishPath,
        ], 'config');
    }
}
