<?php

namespace App\Console\Commands;

use App\Jobs\ProcessUserJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestScheduler extends Command
{
    protected $signature = 'test:scheduler';
    protected $description = 'Test Scheduler Working';

    public function handle()
    {
        // Log check
        Log::info('Scheduler Run Time: '.now());

        // Database insert test
        DB::table('scheduler_logs')->insert([
            'message' => 'Scheduler executed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        ProcessUserJob::dispatch('Amir Hamja');
        $this->info('Scheduler Successfully Executed');
    }
}


