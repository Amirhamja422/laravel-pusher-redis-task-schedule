<?php

namespace App\Jobs;

use App\Models\UploadHistory;
use App\Models\ImportSummary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Jobs\ImportChunkJob;

class ProcessImportFile implements ShouldQueue
{
    use Queueable;

    public $upload;

    public function __construct(UploadHistory $upload)
    {
        $this->upload = $upload;
    }

    public function handle()
    {
        $file = storage_path(
            'app/public/' . $this->upload->file
        );

        $handle = fopen($file, 'r');

        fgetcsv($handle);

        $chunkSize = 5000;
        $chunk = [];
        $total = 0;

        while (($row = fgetcsv($handle)) !== false) {

            $total++;

            $chunk[] = [
                'name'  => $row[0] ?? null,
                'email' => $row[1] ?? null,
            ];

            if (count($chunk) == $chunkSize) {

                ImportChunkJob::dispatch(
                    $chunk,
                    $this->upload->id
                );

                $chunk = [];
            }
        }

        if ($chunk) {
            ImportChunkJob::dispatch(
                $chunk,
                $this->upload->id
            );
        }

        fclose($handle);

        ImportSummary::where(
            'upload_history_id',
            $this->upload->id
        )->update([
            'total_rows' => $total
        ]);

        $this->upload->update([
            'status' => 'processing'
        ]);
    }
}
