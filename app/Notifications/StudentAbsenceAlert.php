<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class StudentAbsenceAlert extends Notification
{
    use Queueable;

    protected $student;

    public function __construct($student)
    {
        $this->student = $student;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'El estudiante ' . $this->student->name . ' tiene 5 o más faltas.',
            'student_name' => "{$this->student->name} {$this->student->last_name} {$this->student->second_last_name}",
            'absence_count' => $this->student->absences_count,
        ];
    }
}
