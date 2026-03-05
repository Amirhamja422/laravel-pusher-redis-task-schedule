<?php

namespace App\Jobs;

use App\Models\ImportFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessImportFileJob implements ShouldQueue
{
    use Queueable;

    public $import;

    public function __construct(ImportFile $import)
    {
        $this->import = $import;
    }

    public function handle()
    {
        $path = storage_path('app/public/'.$this->import->file_path);

        if (!file_exists($path)) {
            Log::error("File not found");
            return;
        }

        $file = fopen($path, 'r');

        while (($row = fgetcsv($file)) !== false) {

            // Example process
            Log::info("Processing: ".$row[0]);
        }

        fclose($file);

        // mark completed
        $this->import->update([
            'status' => 1
        ]);

        Log::info("File Processing Completed");
    }
}
