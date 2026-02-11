<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    //
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            $slugColumn = property_exists($model, 'slugFrom') ? $model->slugFrom : 'name';

            if (empty($model->slug) && isset($model->$slugColumn)) {
                $model->slug = $model->generateSlug($model->$slugColumn);
            }
        });

        static::updating(function ($model) {
            $slugColumn = property_exists($model, 'slugFrom') ? $model->slugFrom : 'name';

            if ($model->isDirty($slugColumn)) {
                $model->slug = $model->generateSlug($model->$slugColumn);
            }
        });
    }
    public function generateSlug($value)
    {
        if (is_array($value)) {
            // خذ الإنجليزي إذا موجود ومش فاضي، وإلا خذ أي لغة ثانية غير عربية
            $value = (isset($value['en']) && !empty(trim($value['en'])))
                ? $value['en']
                : collect($value)
                ->filter(fn($v) => !empty(trim($v ?? '')))
                ->first() ?? '';
        }

        $baseSlug = Str::slug($value);

        // إذا الـ slug فاضي (مثلاً النص كله عربي)
        if (empty($baseSlug)) {
            $baseSlug = 'item'; // أو أي default تحبه
        }

        $slug = $baseSlug;
        $count = 1;

        while (
            static::where('slug', $slug)
            ->where('id', '!=', $this->id ?? 0)
            ->exists()
        ) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
