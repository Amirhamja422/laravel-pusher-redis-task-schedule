<?php
namespace App\Jobs;

use App\Models\File;
use App\Jobs\ImportChunkJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessFileUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function handle()
    {
        $this->file->update(['status' => 'processing']);
        $filePath = storage_path('app/public/' . $this->file->file_path);

        if (!file_exists($filePath)) {
            $this->file->update(['status' => 'failed']);
            return;
        }

        $data = Excel::toArray([], $filePath)[0]; // first sheet
        $header = array_shift($data); // remove header row

        $chunkSize = 1000;
        $chunk = [];

        foreach ($data as $row) {
            $chunk[] = array_combine($header, $row);

            if (count($chunk) === $chunkSize) {
                ImportChunkJob::dispatch($chunk, $this->file->id);
                $chunk = [];
            }
        }

        if (!empty($chunk)) {
            ImportChunkJob::dispatch($chunk, $this->file->id);
        }

        $this->file->update(['status' => 'completed']);
    }
}

// namespace App\Jobs;

// use App\Models\File;
// use App\Jobs\ImportChunkJob;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;

// class ProcessFileUpload implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, SerializesModels;

//     public $file;

//     public function __construct(File $file)
//     {
//         $this->file = $file;
//     }

//     public function handle()
//     {
//         $this->file->update(['status' => 'processing']);

//         $filePath = storage_path('app/public/' . $this->file->file_path);

//         if (!file_exists($filePath)) {
//             $this->file->update(['status' => 'failed']);
//             return;
//         }

//         $handle = fopen($filePath, 'r');
//         $header = fgetcsv($handle);
//         $chunkSize = 1000;
//         $chunk = [];

//         while (($row = fgetcsv($handle)) !== false) {
//             $chunk[] = array_combine($header, $row);

//             if (count($chunk) === $chunkSize) {
//                 ImportChunkJob::dispatch($chunk, $this->file->id);
//                 $chunk = [];
//             }
//         }

//         if (!empty($chunk)) {
//             ImportChunkJob::dispatch($chunk, $this->file->id);
//         }

//         fclose($handle);

//         $this->file->update(['status' => 'completed']);
//     }
// }
