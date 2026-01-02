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
            $table->decimal('price_shoulder_season', 10, 2)->nullable()->after('price');
            $table->decimal('price_high_season', 10, 2)->nullable()->after('price_shoulder_season');
            $table->decimal('price_peak_season', 10, 2)->nullable()->after('price_high_season');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('villa_translations', function (Blueprint $table) {
            $table->dropColumn(['price_shoulder_season', 'price_high_season', 'price_peak_season']);
        });
    }
};
