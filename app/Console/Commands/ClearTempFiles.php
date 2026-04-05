<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp-files:clear {--hours=24 : Delete files older than this many hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear orphaned temporary files from storage to save space';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');

        // Ensure the directory exists to avoid errors
        if (! Storage::exists('temp_files')) {
            $this->info("Directory 'temp_files' does not exist. Nothing to clear.");

            return;
        }

        $files = Storage::files('temp_files');
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = Storage::lastModified($file);
            if (Carbon::createFromTimestamp($lastModified)->diffInHours() >= $hours) {
                Storage::delete($file);
                $deletedCount++;
            }
        }

        $this->info("Successfully deleted {$deletedCount} orphaned temporary file(s) older than {$hours} hours.");
    }
}
