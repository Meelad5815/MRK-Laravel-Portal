<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('madrasa_timetable', function (Blueprint $table) {
            $table->id();
            $table->string('madrasa_id', 80)->index();
            $table->unsignedBigInteger('qari_user_id')->index();
            $table->unsignedTinyInteger('weekday')->comment('1=Monday ... 7=Sunday');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('class_name', 120);
            $table->string('subject', 120);
            $table->string('room', 80)->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();
            $table->index(['madrasa_id', 'weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('madrasa_timetable');
    }
};
