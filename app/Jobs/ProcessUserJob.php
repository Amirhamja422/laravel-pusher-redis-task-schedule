<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessUserJob implements ShouldQueue
{
    use Queueable;

    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function handle()
    {
        Log::info("Queue Job Running : ".$this->name);
    }
}
