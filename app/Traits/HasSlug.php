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

            // Don't try to generate slug from translatable attributes during creating
            // Wait for the model to be fully saved first
            if (empty($model->slug)) {
                $model->slug = $model->generateSlug('temporary-' . uniqid());
            }
        });

        static::created(function ($model) {
            // After creation, generate proper slug from the name attribute
            $slugColumn = property_exists($model, 'slugFrom') ? $model->slugFrom : 'name';
            
            // Check if there's a translatable name attribute
            if (isset($model->attributes[$slugColumn])) {
                $value = $model->attributes[$slugColumn];
                $newSlug = $model->generateSlug($value);
                
                if ($newSlug !== $model->slug) {
                    $model->update(['slug' => $newSlug]);
                }
            }
        });

        static::updating(function ($model) {
            $slugColumn = property_exists($model, 'slugFrom') ? $model->slugFrom : 'name';

            if ($model->isDirty($slugColumn)) {
                $value = $model->attributes[$slugColumn] ?? null;
                if ($value) {
                    $model->slug = $model->generateSlug($value);
                }
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
