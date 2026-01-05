<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('dine_relax_blocks', 'dine_relax_page_id')) {
            Schema::table('dine_relax_blocks', function (Blueprint $table) {
                $table->unsignedBigInteger('dine_relax_page_id')->nullable()->after('id');
            });

            $pageId = DB::table('dine_relax_pages')->value('id');
            if (! $pageId) {
                $now = now();
                $pageId = DB::table('dine_relax_pages')->insertGetId([
                    'hero_image_path' => null,
                    'hero_image_alt' => null,
                    'is_published' => false,
                    'meta_image_path' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('dine_relax_blocks')->update(['dine_relax_page_id' => $pageId]);

            Schema::table('dine_relax_blocks', function (Blueprint $table) {
                $table->foreign('dine_relax_page_id')
                    ->references('id')
                    ->on('dine_relax_pages')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('dine_relax_blocks', 'dine_relax_page_id')) {
            Schema::table('dine_relax_blocks', function (Blueprint $table) {
                $table->dropForeign(['dine_relax_page_id']);
                $table->dropColumn('dine_relax_page_id');
            });
        }
    }
};
