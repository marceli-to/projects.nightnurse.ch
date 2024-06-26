<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use App\Tasks\Notification;
use App\Tasks\Markup;
use App\Tasks\Feedback;
use App\Tasks\CleanUpFiles;
use App\Tasks\CreatePreviews;
use Gecche\Multidomain\Foundation\Console\Kernel as ConsoleKernel;

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
    $schedule->call(new Notification)->everyMinute();
    $schedule->call(new Markup)->everyMinute();
    $schedule->call(new Feedback)->everyMinute();
    $schedule->call(new CleanUpFiles)->everyMinute();
    $schedule->call(new CreatePreviews)->everyMinute();
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