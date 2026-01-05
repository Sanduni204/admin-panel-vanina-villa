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
        Schema::create('dine_relax_pages', function (Blueprint $table) {
            $table->id();
            $table->string('hero_image_path')->nullable();
            $table->string('hero_image_alt')->nullable();
            $table->boolean('is_published')->default(false);
            $table->string('meta_image_path')->nullable();
            $table->timestamps();
        });

        Schema::create('dine_relax_page_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dine_relax_page_id');
            $table->string('locale', 5);
            $table->string('hero_tagline')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_lead')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->foreign('dine_relax_page_id')
                ->references('id')
                ->on('dine_relax_pages')
                ->onDelete('cascade');
            $table->unique(['dine_relax_page_id', 'locale']);
            $table->index('locale');
        });

        Schema::create('dine_relax_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dine_relax_page_id');
            $table->string('slug', 50);
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('cta_label', 100)->nullable();
            $table->string('cta_url')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique('slug');
            $table->index('display_order');
            $table->foreign('dine_relax_page_id')
                ->references('id')
                ->on('dine_relax_pages')
                ->onDelete('cascade');
        });

        Schema::create('dine_relax_block_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dine_relax_block_id');
            $table->string('locale', 5);
            $table->string('heading');
            $table->longText('body')->nullable();
            $table->string('hours')->nullable();
            $table->json('highlights')->nullable();
            $table->timestamps();

            $table->foreign('dine_relax_block_id')
                ->references('id')
                ->on('dine_relax_blocks')
                ->onDelete('cascade');
            $table->unique(['dine_relax_block_id', 'locale']);
            $table->index('locale');
        });

        Schema::create('dine_relax_galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dine_relax_block_id');
            $table->string('image_path');
            $table->string('image_alt');
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->foreign('dine_relax_block_id')
                ->references('id')
                ->on('dine_relax_blocks')
                ->onDelete('cascade');
            $table->index(['dine_relax_block_id', 'display_order']);
        });

        Schema::create('dine_relax_menus', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['beverage', 'snacking', 'today', 'breakfast']);
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_mime', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique('type');
            $table->index('display_order');
            $table->index('is_active');
        });

        Schema::create('dine_relax_menu_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dine_relax_menu_id');
            $table->string('locale', 5);
            $table->string('title');
            $table->string('button_label', 100);
            $table->string('version_note', 100)->nullable();
            $table->timestamps();

            $table->foreign('dine_relax_menu_id')
                ->references('id')
                ->on('dine_relax_menus')
                ->onDelete('cascade');
            $table->unique(['dine_relax_menu_id', 'locale']);
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dine_relax_menu_translations');
        Schema::dropIfExists('dine_relax_menus');
        Schema::dropIfExists('dine_relax_galleries');
        Schema::dropIfExists('dine_relax_block_translations');
        Schema::dropIfExists('dine_relax_blocks');
        Schema::dropIfExists('dine_relax_page_translations');
        Schema::dropIfExists('dine_relax_pages');
    }
};
