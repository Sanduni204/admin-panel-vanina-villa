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
        Schema::table('dine_relax_page_translations', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('meta_description')->index();
        });

        Schema::table('dine_relax_menus', function (Blueprint $table) {
            $table->string('card_image_path')->nullable()->after('file_mime');
            $table->string('card_image_alt')->nullable()->after('card_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dine_relax_page_translations', function (Blueprint $table) {
            $table->dropColumn('is_published');
        });

        Schema::table('dine_relax_menus', function (Blueprint $table) {
            $table->dropColumn(['card_image_path', 'card_image_alt']);
        });
    }
};
