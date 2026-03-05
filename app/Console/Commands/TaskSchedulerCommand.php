<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessImportFileJob;
use App\Models\ImportFile;
use Carbon\Carbon;

class TaskSchedulerCommand extends Command
{
    // IMPORTANT ✅
    protected $signature = 'task:scheduler';

    protected $description = 'Run scheduled import tasks';

    public function handle()
    {
        $files = ImportFile::where('status',0)
            ->where('run_at','<=',Carbon::now())
            ->get();

        foreach ($files as $file) {

            ProcessImportFileJob::dispatch($file);

            $this->info("Job dispatched ID ".$file->id);
        }
    }
}
