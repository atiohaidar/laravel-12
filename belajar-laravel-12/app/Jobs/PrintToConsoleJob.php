<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PrintToConsoleJob implements ShouldQueue
{
    use Queueable;
    protected string $message;

    /**
     * Create a new job instance.
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }
  
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
     echo  $this->message;
        sleep(5); // Simulate a long-running process
    
     // Simulate a long-running process
    }
}
