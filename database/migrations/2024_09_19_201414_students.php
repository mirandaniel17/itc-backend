<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('last_name', 100);
            $table->string('second_last_name', 100);
            $table->string('name', 100);
            $table->string('ci', 15)->nullable();
            $table->string('image', 255)->nullable();
            $table->date('dateofbirth');
            $table->string('placeofbirth', 200)->nullable();
            $table->string('phone', 10);
            $table->enum('gender', ['MASCULINO', 'FEMENINO', 'OTRO']);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
