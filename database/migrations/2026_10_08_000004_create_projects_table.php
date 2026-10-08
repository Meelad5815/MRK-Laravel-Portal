<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->string('slug', 180)->unique();
            $table->string('summary', 300);
            $table->text('description');
            $table->string('category', 80)->index();
            $table->string('technologies', 500)->nullable();
            $table->string('project_url', 500)->nullable();
            $table->string('status', 30)->default('published')->index();
            $table->boolean('featured')->default(false)->index();
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
