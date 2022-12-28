<?php

namespace App\Console;

use App\Library\Jobs\CollectionReportJob;
use App\Library\Reports\ChargedBreakdownsReport;
use App\Models\ChargedBreakdown;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        Log::info('Cron Job Started');

        // start queue
        $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();

        // Total collection report
        $schedule->call(function (CollectionReportJob $job) {
        })->everySixHours();
        // Inventory report
        $schedule->call(function (ChargedBreakdownsReport $job) {
            $job->saveToDatabase(52);
        })->everySixHours();

        Log::info('Cron Job Ended');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
