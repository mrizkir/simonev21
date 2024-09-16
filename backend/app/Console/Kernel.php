<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * The Artisan commands provided by your application.
   *
   * @var array
   */
  protected $commands = [
    //
  ];

  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    //running queue default
    $schedule->command('queue:work --stop-when-empty --queue=default')
    ->everyMinute()
    ->withoutOverlapping();

    //job system - update realiasi
    $schedule->job(new \App\Jobs\UpdateRealisasiMurniJob, 'update-realiasi-murni');
    
    $schedule->command('queue:work --stop-when-empty --queue=update-realiasi-murnil')
    ->everyMinute();
  }
}
