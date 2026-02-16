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
        Schema::create('initiatives', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description');

            $table->foreignId('hub_id')
                ->nullable()
                ->constrained('hubs')
                ->nullOnDelete();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('initiative_categories')
                ->nullOnDelete();
            $table->string('location_type')->default('online'); // online, onsite, hybrid
            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();
            $table->foreignId('social_account_id')
                ->nullable()
                ->constrained('social_accounts')
                ->nullOnDelete();

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('registration_deadline')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('initiatives');
    }
};
