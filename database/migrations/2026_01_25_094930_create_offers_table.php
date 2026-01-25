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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
                   $table->foreignId('hub_id')
              ->constrained('hubs')
              ->cascadeOnDelete();

        $table->string('title');
        $table->string('type', 20)->default('daily');

        $table->bigInteger('price');

        $table->integer('duration');
        // بالساعات (daily) أو الأيام (weekly / monthly)

        $table->text('description')->nullable();

        $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
