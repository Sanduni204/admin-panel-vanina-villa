<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // For MySQL/PostgreSQL
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE dine_relax_menus MODIFY COLUMN type VARCHAR(100) NOT NULL');
        } else {
            // For SQLite, we need to recreate the table
            Schema::table('dine_relax_menus', function (Blueprint $table) {
                $table->string('type', 100)->change();
            });
        }
    }

    public function down(): void
    {
        // For MySQL/PostgreSQL
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE dine_relax_menus MODIFY COLUMN type ENUM('beverage','snacking','today','breakfast') NOT NULL");
        } else {
            // For SQLite, just change back to string (no ENUM support)
            Schema::table('dine_relax_menus', function (Blueprint $table) {
                $table->string('type', 100)->change();
            });
        }
    }
};

