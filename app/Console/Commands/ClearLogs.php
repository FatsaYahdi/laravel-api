<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Laravel log';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)):
            unlink($logPath);
            $this->info('Log Congratulation Deleted.');
        else:
            $this->info("File find'nt");
        endif;
    }
}
