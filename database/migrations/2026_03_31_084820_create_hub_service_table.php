<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hub_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hub_id')->constrained('hubs')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->json('additional_info')->nullable(); // معلومات إضافية عن الخدمة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hub_service');
    }
};
