<?php

namespace App\Jobs;

use App\Models\ImportUser;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessImportFileJob implements ShouldQueue
{

    use Dispatchable, Queueable, Batchable;

    public $fileId;
    public $rows;

    public function __construct($fileId,$rows)
    {
        $this->fileId = $fileId;
        $this->rows = $rows;
    }

    public function handle()
    {

        if ($this->batch()->cancelled()) {
            return;
        }

        $insertData = [];

        foreach ($this->rows as $row) {

            if(!isset($row[0],$row[1])){
                continue;
            }

            $insertData[] = [

                'name'=>$row[0],
                'email'=>$row[1],
                'created_at'=>now(),
                'updated_at'=>now()

            ];

        }

        if(!empty($insertData)){

            ImportUser::insertOrIgnore($insertData);

        }

    }

}




// namespace App\Jobs;

// use App\Models\ImportFile;
// use App\Models\ImportUser;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Queue\Queueable;
// use Illuminate\Support\Facades\Log;

// class ProcessImportFileJob implements ShouldQueue
// {
//     use Queueable;

//     public $import;

//     public function __construct(ImportFile $import)
//     {
//         $this->import = $import;
//     }

//     public function handle()
//     {

//         $path = storage_path('app/public/'.$this->import->file_path);

//         if (!file_exists($path)) {
//             Log::error("File not found");
//             return;
//         }

//         $file = fopen($path, 'r');

//         while (($row = fgetcsv($file)) !== false) {

//             ImportUser::create([
//                 'name'  => $row[0],
//                 'email' => $row[1]
//             ]);

//         }

//         fclose($file);

//         $this->import->update([
//             'status' => 1
//         ]);

//         Log::info("File Processing Completed");
//     }
// }
