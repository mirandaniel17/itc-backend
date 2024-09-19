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
            $table->string('first_name', 100)->nullable()->default('');
            $table->string('second_name', 100)->nullable()->default('');
            $table->date('dateofbirth');
            $table->string('placeofbirth', 200)->nullable()->default('');
            $table->string('phone', 10);
            $table->enum('gender', ['MASCULINO', 'FEMENINO', 'OTRO']);
            $table->boolean('status')->default(true);
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
