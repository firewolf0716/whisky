<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\ScrapeAuctions',      
        'App\Console\Commands\ScrapeDistillery',        
        'App\Console\Commands\ScrapeBrand',        
        'App\Console\Commands\ScrapeBottler',       
        'App\Console\Commands\ScrapeProducts',        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        // $schedule->command('scrape:auctions')
        //          ->monthlyOn(7, '9:00');
        // $schedule->command('scrape:brands')
        //          ->monthly();
        // $schedule->command('scrape:bottlers')
        //          ->monthly();
        // $schedule->command('scrape:distilleries')
        //          ->monthly();
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
