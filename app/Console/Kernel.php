<?php

namespace App\Console;

use App\Models\Task;
use Illuminate\Support\Carbon;
use App\Notifications\TaskDue;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        // Check for tasks that are due in less than two days, and send a notification to the user if any are found
        $schedule->call(function () {
            $tasks = Task::whereDate('due_date', '<=', Carbon::now()->addDays(2))->get();

            foreach ($tasks as $task) {
                $task->user->notify(new TaskDue($task));
            }
        })->daily();
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
