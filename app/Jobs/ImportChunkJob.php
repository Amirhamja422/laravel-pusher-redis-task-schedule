<?php

namespace App\Jobs;

use App\Models\ImportUser;
use App\Models\ImportSummary;
use App\Models\FailedImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportChunkJob implements ShouldQueue
{
    use Queueable;

    public $chunk;
    public $uploadId;

    public function __construct($chunk, $uploadId)
    {
        $this->chunk = $chunk;
        $this->uploadId = $uploadId;
    }

    public function handle()
    {
        $success = 0;
        $duplicate = 0;
        $failed = 0;

        $failedRows = [];

        foreach ($this->chunk as $row) {

            try {

                // if (empty($row['email'])) {
                //     throw new \Exception('Email Empty');
                // }

                // if (
                //     ImportUser::where(
                //         'email',
                //         $row['email']
                //     )->exists()
                // ) {
                //     $duplicate++;
                //     continue;
                // }

                ImportUser::create($row);

                $success++;

            } catch (\Exception $e) {

                $failed++;

                $failedRows[] = [
                    'upload_history_id'=>$this->uploadId,
                    'name'=>$row['name'],
                    'email'=>$row['email'],
                    'reason'=>$e->getMessage(),
                    'created_at'=>now(),
                    'updated_at'=>now()
                ];
            }
        }

        if ($failedRows) {
            FailedImport::insert($failedRows);
        }

        ImportSummary::where(
            'upload_history_id',
            $this->uploadId
        )->increment('total_success', $success);

        // ImportSummary::where(
        //     'upload_history_id',
        //     $this->uploadId
        // )->increment('total_duplicate', $duplicate);

        ImportSummary::where(
            'upload_history_id',
            $this->uploadId
        )->increment('total_failed', $failed);
    }
}
