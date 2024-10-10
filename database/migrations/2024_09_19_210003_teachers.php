<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('last_name', 100)->nullable();
            $table->string('second_last_name', 100)->nullable();
            $table->string('name', 100)->nullable();
            $table->date('dateofbirth');
            $table->string('placeofbirth', 200)->nullable();
            $table->string('phone', 10);
            $table->enum('gender', ['MASCULINO', 'FEMENINO', 'OTRO']);
            $table->string('specialty')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
