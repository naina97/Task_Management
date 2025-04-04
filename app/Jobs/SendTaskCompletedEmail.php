<?php

namespace App\Jobs;

use App\Mail\TaskCompleted;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTaskCompletedEmail implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

     // The handle method that will be called when the job is processed
     public function handle()
     {
         // You can adjust the email content and structure based on your need
         Mail::to($this->task->user->email)
             ->send(new TaskCompleted($this->task));
     }
}
