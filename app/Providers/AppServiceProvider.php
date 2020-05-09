<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'Comment'   => 'App\Models\Comment',
            'Data'      => 'App\Models\Data',
            'User'      => 'App\Models\User',
            'Student'   => 'App\Models\Student',
            'Center'    => 'App\Models\Center',
            'Event'     => 'App\Models\Event',
            'Class'     => 'App\Models\Class',
            'City'      => 'App\Models\City',
            'Batch'     => 'App\Models\Batch',
            'Level'     => 'App\Models\Level',
        ]);
    }
}
