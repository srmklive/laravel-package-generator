<?php

namespace Srmklive\PackageGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use Srmklive\PackageGenerator\Commands\PackageGeneratorCommand;

class PackageGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            PackageGeneratorCommand::class,
        ]);
    }
}
