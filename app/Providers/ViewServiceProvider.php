<?php

namespace App\Providers;

use App\Http\ViewComposers\CompanyFooterComposer;
use App\Http\ViewComposers\HomeBannersComposer;
use App\Http\ViewComposers\SidebarComposer;
use App\Http\ViewComposers\SidebarProfileComposer;
use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\HeaderComposer;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(
            ['partials.header', 'partials.footer'], HeaderComposer::class
        );
        view()->composer(
            ['partials.sidebar', 'partials.sidebar_small'], SidebarComposer::class
        );
    }
}
