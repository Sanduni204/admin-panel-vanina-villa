<?php

namespace App\Console\Commands;

use App\Models\VillaMedia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateVillaImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'villa:migrate-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate villa images from storage/app/public to public/uploads directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting villa images migration...');

        $images = VillaMedia::all();

        if ($images->isEmpty()) {
            $this->info('No images found in database.');
            return 0;
        }

        $migrated = 0;
        $errors = 0;

        foreach ($images as $media) {
            // Check if path already starts with 'uploads/'
            if (str_starts_with($media->image_path, 'uploads/')) {
                $this->line("Skipping {$media->id}: Already migrated");
                continue;
            }

            // Old path: villas/{id}/{filename}
            // New path: uploads/villas/{id}/{filename}
            $oldPath = storage_path('app/public/' . $media->image_path);
            $newRelativePath = 'uploads/' . $media->image_path;
            $newPath = public_path($newRelativePath);

            // Check if old file exists
            if (!file_exists($oldPath)) {
                $this->warn("File not found: {$oldPath}");
                $errors++;
                continue;
            }

            // Create new directory if it doesn't exist
            $newDir = dirname($newPath);
            if (!file_exists($newDir)) {
                mkdir($newDir, 0755, true);
            }

            // Copy file to new location
            if (copy($oldPath, $newPath)) {
                // Update database
                $media->image_path = $newRelativePath;
                $media->save();

                $this->info("Migrated: {$media->image_path}");
                $migrated++;

                // Optionally delete old file
                // unlink($oldPath);
            } else {
                $this->error("Failed to copy: {$oldPath}");
                $errors++;
            }
        }

        $this->info("\nMigration completed!");
        $this->info("Migrated: {$migrated} images");
        if ($errors > 0) {
            $this->warn("Errors: {$errors}");
        }

        return 0;
    }
}
