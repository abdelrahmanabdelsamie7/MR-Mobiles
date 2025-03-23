<?php

namespace App\Providers;
use App\Models\Accessory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Mobile;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'mobile' => Mobile::class,
            'accessory' => Accessory::class,
        ]);
    }
}