<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TaskCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $task;

    public function __construct($title,$description)
    {
        $this->title = $title;
        $this->description = $description;

        \Log::info('TaskCompleted Mailable Constructed');

    }
    public function build()
    {
        return $this->view('emails.tasks.completed')
                    ->subject('Task Completed')
                    ->with([
                        'taskTitle' => $this->title,
                        'taskDescription' => $this->description,
                    ]);
    }
    
}
