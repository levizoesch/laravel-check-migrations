<?php
namespace levizoesch\checkmigrations;

use Illuminate\Support\ServiceProvider;
use levizoesch\checkmigrations\CheckMigrations;

class CheckMigrationsServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->commands([
            CheckMigrations::class
        ]);
    }
}
