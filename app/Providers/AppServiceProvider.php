<?php

namespace App\Providers;
use App\Models\{Mobile,Accessory};
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'mobile' => Mobile::class,
            'accessory' => Accessory::class,
        ]);
    }
}