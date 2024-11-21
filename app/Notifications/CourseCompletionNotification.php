<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CourseCompletionNotification extends Notification
{
    use Queueable;

    protected $course;

    public function __construct($course)
    {
        $this->course = $course;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'El curso "' . $this->course->name . '" ha finalizado.',
            'course_name' => $this->course->name,
            'end_date' => $this->course->end_date,
        ];
    }
}
