<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessImportFileJob;
use App\Models\ImportFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;

class TaskSchedulerCommand extends Command
{
    protected $signature = 'task:scheduler';
    protected $description = 'Run scheduled import tasks';

    public function handle()
    {

        $files = ImportFile::where('status',0)
            ->where('run_at','<=',Carbon::now())
            ->get();

        foreach ($files as $file) {

            $file->update([
                'status' => 2
            ]);

            $path = storage_path('app/public/'.$file->file_path);

            if (!file_exists($path)) {

                $this->error("File not found: ".$file->id);

                $file->update([
                    'status'=>0
                ]);

                continue;
            }

            $extension = pathinfo($path, PATHINFO_EXTENSION);

            $rows = [];

            // CSV FILE
            if($extension == 'csv' || $extension == 'txt'){

                $handle = fopen($path,'r');

                while(($row = fgetcsv($handle)) !== false){
                    $rows[] = $row;
                }

                fclose($handle);

            }

            // XLSX FILE
            if($extension == 'xlsx' || $extension == 'xls'){

                $data = Excel::toArray([], $path);

                foreach($data[0] as $row){
                    $rows[] = $row;
                }

            }

            $chunkSize = 1000;
            $chunks = array_chunk($rows,$chunkSize);

            $jobs = [];

            foreach($chunks as $chunkRows){

                $jobs[] = new ProcessImportFileJob($file->id,$chunkRows);

            }

            Bus::batch($jobs)
                ->then(function (Batch $batch) use ($file) {

                    $file->update([
                        'status' => 1
                    ]);

                    \Log::info("Import Completed File ID ".$file->id);

                })
                ->catch(function (Batch $batch, Throwable $e) use ($file) {

                    $file->update([
                        'status' => 0
                    ]);

                    \Log::error("Import Failed File ID ".$file->id);

                })
                ->dispatch();

            $this->info("Batch dispatched for file ".$file->id);

        }

    }
}



// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use App\Jobs\ProcessImportFileJob;
// use App\Models\ImportFile;
// use Carbon\Carbon;

// class TaskSchedulerCommand extends Command
// {
//     // IMPORTANT ✅
//     protected $signature = 'task:scheduler';

//     protected $description = 'Run scheduled import tasks';

//     public function handle()
//     {
//         $files = ImportFile::where('status',0)
//             ->where('run_at','<=',Carbon::now())
//             ->get();

//         foreach ($files as $file) {

//             ProcessImportFileJob::dispatch($file);

//             $this->info("Job dispatched ID ".$file->id);
//         }
//     }
// }
