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
        Schema::table('dine_relax_blocks', function (Blueprint $table) {
            // Add name field for custom block names
            $table->string('name')->nullable()->after('slug');
        });

        // Drop unique constraint on slug if it exists
        Schema::table('dine_relax_blocks', function (Blueprint $table) {
            try {
                $table->dropUnique(['slug']);
            } catch (\Exception $e) {
                // Constraint doesn't exist, continue
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dine_relax_blocks', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
