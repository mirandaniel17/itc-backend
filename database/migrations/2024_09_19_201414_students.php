<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('last_name', 100)->nullable()->default('');
            $table->string('second_last_name', 100)->nullable()->default('');
            $table->string('name', 100)->nullable()->default('');
            $table->string('ci', 15)->nullable()->default('');
            $table->string('image', 255)->nullable()->default('');
            $table->enum('program_type', ['MODULAR', 'CARRERA']);
            $table->string('school_cycle', 50);
            $table->enum('shift', ['MAÑANA', 'TARDE']);
            $table->string('parallel', 10)->nullable();
            $table->date('dateofbirth');
            $table->string('placeofbirth', 200)->nullable()->default('');
            $table->string('phone', 10);
            $table->enum('gender', ['MASCULINO', 'FEMENINO', 'OTRO']);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
