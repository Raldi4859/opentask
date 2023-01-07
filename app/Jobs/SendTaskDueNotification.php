<?php

namespace App\Jobs;

use App\Models\Task;
use App\Notifications\TaskDue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTaskDueNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tasks = Task::whereDate('due_date', '<=', now()->addDays(2))
            ->where('status', 'pending')
            ->get();

        foreach ($tasks as $task) {
            $task->user->notify(new TaskDue($task));
        }
    }
}
