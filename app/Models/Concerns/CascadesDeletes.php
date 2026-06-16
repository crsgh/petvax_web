<?php

namespace App\Models\Concerns;

trait CascadesDeletes
{
    public static function bootCascadesDeletes(): void
    {
        static::deleting(function ($model) {
            foreach (static::$cascades as [$foreignKey, $relatedClass]) {
                $relatedClass::where($foreignKey, $model->getKey())->get()->each(
                    fn ($related) => $related->delete()
                );
            }
        });
    }
}
