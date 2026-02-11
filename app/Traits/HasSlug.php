<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateSlugFromModel();
            }
        });

        static::updating(function ($model) {
            $slugColumn = $model->getSlugSourceColumn();

            if ($model->isDirty($slugColumn)) {
                $model->slug = $model->generateSlugFromModel();
            }
        });
    }

    // يرجع اسم الكولوم اللي الـ slug بيتولّد منه
    protected function getSlugSourceColumn(): string
    {
        return property_exists($this, 'slugFrom') ? $this->slugFrom : 'name';
    }

    // يجيب القيمة الفعلية من الكولوم (سواء كان translatable أو لا)
    protected function getSlugSourceValue(): string
    {
        $column = $this->getSlugSourceColumn();

        // لو في translatable وهاد الكولوم منها
        if (
            method_exists($this, 'getTranslations') &&
            in_array($column, $this->translatable ?? [])
        ) {
            $translations = $this->getTranslations($column);

            // أولوية: en → أول لغة موجودة وفيها قيمة
            if (!empty(trim($translations['en'] ?? ''))) {
                return $translations['en'];
            }

            return collect($translations)
                ->filter(fn($v) => !empty(trim($v ?? '')))
                ->first() ?? '';
        }

        // لو مش translatable، رجّع القيمة مباشرة
        return $this->$column ?? '';
    }

    public function generateSlugFromModel(): string
    {
        return $this->generateSlug($this->getSlugSourceValue());
    }

    public function generateSlug(string $value): string
    {
        // لو القيمة JSON string (Spatie بتخزنها هيك)
        if (!empty($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = !empty(trim($decoded['en'] ?? ''))
                    ? $decoded['en']
                    : collect($decoded)
                        ->filter(fn($v) => !empty(trim($v ?? '')))
                        ->first() ?? '';
            }
        }

        $baseSlug = Str::slug($value);

        if (empty($baseSlug)) {
            $baseSlug = 'item';
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
