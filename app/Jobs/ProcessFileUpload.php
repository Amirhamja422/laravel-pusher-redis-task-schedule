<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\ImportSummary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessFileUpload implements ShouldQueue
{
    use Queueable;

    public $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function handle()
    {
        // Update status
        $this->file->update([
            'status' => 'processing'
        ]);

        $filePath = storage_path(
            'app/public/' . $this->file->file_path
        );

        if (!file_exists($filePath)) {
            $this->file->update(['status' => 'failed']);
            return;
        }

        // Create summary row
        $summary = ImportSummary::create([
            'upload_history_id' => $this->file->id
        ]);

        $handle = fopen($filePath, 'r');

        $header = fgetcsv($handle);

        $chunkSize = 1000;
        $chunk = [];

        while (($row = fgetcsv($handle)) !== false) {

            $chunk[] = array_combine($header, $row);

            if (count($chunk) == $chunkSize) {

                ImportChunkJob::dispatch(
                    $chunk,
                    $this->file->id
                );

                $chunk = [];
            }
        }

        // Remaining rows
        if (!empty($chunk)) {
            ImportChunkJob::dispatch(
                $chunk,
                $this->file->id
            );
        }

        fclose($handle);

        $this->file->update([
            'status' => 'completed'
        ]);
    }
}
