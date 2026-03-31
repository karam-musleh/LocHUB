<?php

namespace App\Models;

use App\Enum\HubStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Hub extends Model
{

    use HasTranslations, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'owner_id',
        'location_id',
        'description',
        'address_details',
        'contact',
        'status',
        'rejection_reason',
        // 'images',

    ];

    protected $translatable = [
        'name',
        'description',
        'address_details'
    ];

    protected $casts = [
        'status' => HubStatus::class ,
            'contact' => 'string',

    ];
    protected $appends = ['url'];

    //
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // موقع الهب
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // خدمات الهب
    public function services()
    {
        return $this->belongsToMany(Service::class)->withTimestamps();
    }

    // عروض الهب
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    // حجوزات الهب
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // تقييمات الهب
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function hubSocialAccounts()
    {
        return $this->morphMany(SocialAccount::class, 'accountable');
    }
    // صور الهب (Morph)
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function mainImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'main');
    }

    public function galleryImages()
    {
        return $this->morphMany(Image::class, 'imageable')->where('type', 'gallery');
    }
    protected function mainImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() =>
            $this->mainImage
                ? Storage::disk('custom')->url($this->mainImage->path)
                : null
        );
    }

    protected function galleryImagesUrls(): Attribute
    {
        return Attribute::make(
            get: fn() =>
            $this->galleryImages->map(
                fn($img) => Storage::disk('custom')->url($img->path)
            )
        );
    }

    protected function imagesUrls(): Attribute
    {
        return Attribute::make(
            get: fn() =>
            $this->images->map(
                fn($img) => Storage::disk('custom')->url($img->path)
            )
        );
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
    // في app/Models/Hub.php
    public function updateGallery($newFiles = [], $deleteIds = [])
    {
        // حذف
        if (!empty($deleteIds)) {
            $this->images()
                ->whereIn('id', $deleteIds)
                ->where('type', 'gallery')
                ->each(function ($image) {
                    if (Storage::disk($image->disk ?? 'custom')->exists($image->path)) {
                        Storage::disk($image->disk ?? 'custom')->delete($image->path);
                    }
                    $image->delete();
                });
        }

        // إضافة
        if (!empty($newFiles)) {
            foreach ($newFiles as $file) {
                $path = $file->store('hubs/gallery', 'custom');
                $this->images()->create([
                    'path' => $path,
                    'type' => 'gallery',
                    'disk' => 'custom',
                ]);
            }
        }
    }
}
