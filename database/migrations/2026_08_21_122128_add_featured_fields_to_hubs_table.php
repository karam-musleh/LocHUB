<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hubs', function (Blueprint $table) {
            $table->boolean('is_featured')
                ->default(false)
                ->index()
                ->after('status');

            $table->unsignedInteger('featured_priority')
                ->default(0)
                ->after('is_featured');

            $table->timestamp('featured_from')
                ->nullable()
                ->after('featured_priority');

            $table->timestamp('featured_until')
                ->nullable()
                ->after('featured_from');
        });
    }

    public function down(): void
    {
        Schema::table('hubs', function (Blueprint $table) {
            $table->dropIndex(['is_featured']);

            $table->dropColumn([
                'is_featured',
                'featured_priority',
                'featured_from',
                'featured_until',
            ]);
        });
    }
};
