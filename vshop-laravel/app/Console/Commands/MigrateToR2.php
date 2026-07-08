<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MigrateToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:migrate-to-r2 {--dir=* : Specific directories to sync} {--overwrite : Overwrite existing files on R2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing local public uploads to Cloudflare R2 bucket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of local public files to Cloudflare R2...');

        $directories = $this->option('dir');
        $overwrite = $this->option('overwrite');

        if (empty($directories)) {
            $directories = [
                'uploads',
                'ads',
                'seller',
                'seller-banners',
                'vtravelassets/uploads',
            ];
        }

        foreach ($directories as $dir) {
            $localPath = public_path($dir);

            if (!File::exists($localPath)) {
                $this->warn("Local directory does not exist: {$localPath}. Skipping.");
                continue;
            }

            $this->info("Scanning directory: {$dir}...");
            $files = File::allFiles($localPath);

            $this->info("Found " . count($files) . " files in {$dir}.");

            $bar = $this->output->createProgressBar(count($files));
            $bar->start();

            foreach ($files as $file) {
                $relativePath = $file->getRelativePathname();
                // R2 path format: folder/subfolder/filename.ext
                $r2Path = str_replace('\\', '/', $dir . '/' . $relativePath);

                // Verify R2 connection and existence if not overwriting
                $exists = false;
                try {
                    if (!$overwrite) {
                        $exists = Storage::disk('r2')->exists($r2Path);
                    }
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("R2 connection failed. Check your credentials in .env: " . $e->getMessage());
                    return Command::FAILURE;
                }

                if ($exists) {
                    $bar->advance();
                    continue;
                }

                try {
                    $stream = fopen($file->getRealPath(), 'r');
                    Storage::disk('r2')->put($r2Path, $stream, 'public');
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("Failed to upload {$relativePath} to R2: " . $e->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Completed sync for {$dir}!");
        }

        $this->info('Migration finished successfully!');
        return Command::SUCCESS;
    }
}
