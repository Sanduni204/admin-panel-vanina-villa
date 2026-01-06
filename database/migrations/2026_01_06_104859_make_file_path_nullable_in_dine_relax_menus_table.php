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
        Schema::table('dine_relax_menus', function (Blueprint $table) {
            $table->string('file_path')->nullable()->change();
            $table->string('file_name')->nullable()->change();
            $table->string('file_mime')->nullable()->change();
            $table->string('card_image_path')->nullable()->change();
            $table->string('card_image_alt')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dine_relax_menus', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
            $table->string('file_name')->nullable(false)->change();
            $table->string('file_mime')->nullable(false)->change();
            $table->string('card_image_path')->nullable(false)->change();
            $table->string('card_image_alt')->nullable(false)->change();
        });
    }
};
