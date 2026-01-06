<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dine_relax_menus', function (Blueprint $table) {
            $table->string('card_image_alt_fr')->nullable()->after('card_image_alt');
        });
    }

    public function down(): void
    {
        Schema::table('dine_relax_menus', function (Blueprint $table) {
            $table->dropColumn('card_image_alt_fr');
        });
    }
};
