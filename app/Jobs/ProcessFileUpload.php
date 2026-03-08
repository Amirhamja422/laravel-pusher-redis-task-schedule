<?php
// namespace App\Jobs;

// use App\Models\File;
// use App\Models\ImportSummary;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Queue\Queueable;

// class ProcessFileUpload implements ShouldQueue
// {
//     use Queueable;

//     public $file;

//     public function __construct(File $file)
//     {
//         $this->file = $file;
//     }

//     public function handle()
//     {

//         $this->file->update([
//             'status'=>'processing'
//         ]);

//         $filePath = storage_path('app/public/'.$this->file->file_path);

//         if(!file_exists($filePath)){

//             $this->file->update([
//                 'status'=>'failed'
//             ]);

//             return;
//         }

//         $summary = ImportSummary::create([
//             'upload_history_id'=>$this->file->id
//         ]);

//         $handle = fopen($filePath,'r');

//         $header = fgetcsv($handle);

//         $chunkSize = 500;
//         $chunk = [];
//         $totalRows = 0;

//         while(($row = fgetcsv($handle)) !== false){

//             $totalRows++;

//             $chunk[] = array_combine($header,$row);

//             if(count($chunk) == $chunkSize){

//                 ImportChunkJob::dispatch(
//                     $chunk,
//                     $this->file->id
//                 );

//                 $chunk = [];
//             }
//         }

//         if(!empty($chunk)){

//             ImportChunkJob::dispatch(
//                 $chunk,
//                 $this->file->id
//             );
//         }

//         fclose($handle);

//         ImportSummary::where(
//             'upload_history_id',
//             $this->file->id
//         )->update([
//             'total_rows'=>$totalRows
//         ]);

//         $this->file->update([
//             'status'=>'completed'
//         ]);
//     }
// }



// for excell job


// namespace App\Jobs;

use App\Jobs\ImportChunkJob;
use App\Models\File;
use App\Models\ImportSummary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;

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

        $this->file->update([
            'status'=>'processing'
        ]);

        ImportSummary::create([
            'upload_history_id'=>$this->file->id
        ]);

        $filePath = storage_path(
            'app/public/'.$this->file->file_path
        );

        $data = Excel::toArray([], $filePath);

        $rows = $data[0];

        $header = array_shift($rows);

        $chunkSize = 1000;

        foreach(array_chunk($rows,$chunkSize) as $chunk){

            $formatted=[];

            foreach($chunk as $row){

                $formatted[]=[
                    'name'=>$row[0] ?? null,
                    'email'=>$row[1] ?? null
                ];

            }

            ImportChunkJob::dispatch(
                $formatted,
                $this->file->id
            );

        }

        $this->file->update([
            'status'=>'completed'
        ]);

    }
}

