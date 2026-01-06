<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Change type column from ENUM to VARCHAR to support dynamic categories
        DB::statement('ALTER TABLE dine_relax_menus MODIFY COLUMN type VARCHAR(100) NOT NULL');
    }

    public function down(): void
    {
        // Revert back to ENUM with original values
        DB::statement("ALTER TABLE dine_relax_menus MODIFY COLUMN type ENUM('beverage','snacking','today','breakfast') NOT NULL");
    }
};
