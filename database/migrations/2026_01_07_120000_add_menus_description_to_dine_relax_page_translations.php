<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dine_relax_page_translations', function (Blueprint $table) {
            $table->text('menus_description')->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('dine_relax_page_translations', function (Blueprint $table) {
            $table->dropColumn('menus_description');
        });
    }
};
