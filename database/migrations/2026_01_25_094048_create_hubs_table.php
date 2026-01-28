<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hubs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->json('name');
            $table->foreignId('location_id')
                ->constrained('locations')
                ->onDelete('cascade');
            $table->json('description')->nullable();
            $table->json('address_details');
            $table->string('status')->default('pending');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubs');
    }
};
