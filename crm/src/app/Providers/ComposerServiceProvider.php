<?php

namespace App\Providers;

use App\Http\Composer\AppSideBarComposer;
use App\Http\Composer\BrandSideBarComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Class ComposerServiceProvider.
 */
class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function boot()
    {
        View::composer('layout.app', AppSideBarComposer::class);
        View::composer('layout.brand', BrandSideBarComposer::class);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        //
    }
}
