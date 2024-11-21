<?php

use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\CourseController;

Artisan::command('notify:course-completion', function () {
    $controller = new CourseController();
    $controller->notifyCourseCompletion();
    $this->info('Notificaciones de finalización de cursos enviadas.');
})->describe('Notifica la finalización de cursos que terminan hoy');
