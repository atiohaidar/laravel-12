<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PrintSomething extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:print-something';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo "Hello Gays\n";
    }
}
