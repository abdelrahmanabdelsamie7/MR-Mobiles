<?php
namespace App\traits;
use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            $source = method_exists($model, 'getSlugSource') ? $model->getSlugSource() : 'name';
            if (empty($model->slug) && !empty($model->$source)) {
                $model->slug = \Str::slug($model->$source);
            }
        });
        static::updating(function ($model) {
            $source = method_exists($model, 'getSlugSource') ? $model->getSlugSource() : 'name';
            if ($model->isDirty($source)) {
                $model->slug = \Str::slug($model->$source);
            }
        });
    }
}