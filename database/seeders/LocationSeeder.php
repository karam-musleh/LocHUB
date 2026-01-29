<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // Governorates
        // =========================

        $north = Location::create([
            'name' => [
                'ar' => 'شمال غزة',
                'en' => 'North Gaza',
            ],
            'type' => 'governorate',
        ]);

        $middle = Location::create([
            'name' => [
                'ar' => 'الوسطى',
                'en' => 'Middle Area',
            ],
            'type' => 'governorate',
        ]);

        $south = Location::create([
            'name' => [
                'ar' => 'جنوب غزة',
                'en' => 'South Gaza',
            ],
            'type' => 'governorate',
        ]);

        // =========================
        // Cities - Middle Area
        // =========================

        $deirAlBalah = Location::create([
            'name' => [
                'ar' => 'دير البلح',
                'en' => 'Deir Al-Balah',
            ],
            'type' => 'city',
            'parent_id' => $middle->id,
        ]);

        $nuseirat = Location::create([
            'name' => [
                'ar' => 'النصيرات',
                'en' => 'Nuseirat',
            ],
            'type' => 'city',
            'parent_id' => $middle->id,
        ]);

        $maghazi = Location::create([
            'name' => [
                'ar' => 'المغازي',
                'en' => 'Al-Maghazi',
            ],
            'type' => 'city',
            'parent_id' => $middle->id,
        ]);

        // =========================
        // Areas - Deir Al-Balah
        // =========================

        Location::create([
            'name' => [
                'ar' => 'البلد',
                'en' => 'Al-Balad',
            ],
            'type' => 'area',
            'parent_id' => $deirAlBalah->id,
        ]);

        Location::create([
            'name' => [
                'ar' => 'البركة',
                'en' => 'Al-Baraka',
            ],
            'type' => 'area',
            'parent_id' => $deirAlBalah->id,
        ]);

        // =========================
        // Cities - South Gaza
        // =========================

        $khanYounis = Location::create([
            'name' => [
                'ar' => 'خانيونس',
                'en' => 'Khan Younis',
            ],
            'type' => 'city',
            'parent_id' => $south->id,
        ]);

        // =========================
        // Areas - Khan Younis
        // =========================

        Location::create([
            'name' => [
                'ar' => 'البلد',
                'en' => 'Downtown',
            ],
            'type' => 'area',
            'parent_id' => $khanYounis->id,
        ]);

        Location::create([
            'name' => [
                'ar' => 'المواصي',
                'en' => 'Al-Mawasi',
            ],
            'type' => 'area',
            'parent_id' => $khanYounis->id,
        ]);

        Location::create([
            'name' => [
                'ar' => 'القرارة',
                'en' => 'Al-Qarara',
            ],
            'type' => 'area',
            'parent_id' => $khanYounis->id,
        ]);
    }
}
