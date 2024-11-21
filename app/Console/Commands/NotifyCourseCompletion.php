<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CourseController;

class NotifyCourseCompletion extends Command
{
    protected $signature = 'notify:course-completion';
    protected $description = 'Notifica la finalización de cursos que terminan hoy';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $controller = new CourseController();
        $controller->notifyCourseCompletion();

        $this->info('Notificaciones de finalización de cursos enviadas.');
    }
}
