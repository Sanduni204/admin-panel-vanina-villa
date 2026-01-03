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
        Schema::table('villa_translations', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['bedrooms', 'bathrooms']);

            // Add new column
            $table->integer('min_guests')->nullable()->after('max_guests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('villa_translations', function (Blueprint $table) {
            // Remove new column
            $table->dropColumn('min_guests');

            // Restore old columns
            $table->integer('bedrooms')->nullable()->after('max_guests');
            $table->decimal('bathrooms', 3, 1)->nullable()->after('bedrooms');
        });
    }
};
