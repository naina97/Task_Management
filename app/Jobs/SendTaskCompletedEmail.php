<?php

namespace App\Jobs;

use App\Mail\TaskCompleted;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskCompletedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

     // The handle method that will be called when the job is processed
     public function handle()
     {
         Log::info('Sending task completion email to: ' . json_encode($this->task->user->email));

        // \Log::info('Sending task completion email to ' . $this->task->user->email);
         // You can adjust the email content and structure based on your need
         Mail::to($this->task->user->email)
             ->send(new TaskCompleted($this->task->title,$this->task->description));
     }
}
