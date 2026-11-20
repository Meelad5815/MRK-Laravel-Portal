<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vu_study_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('course_code', 30)->nullable()->index();
            $table->string('course_title')->nullable();
            $table->string('item_type', 30)->index(); // announcement, assignment, quiz, gdb, exam, grade
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->timestamp('opens_at')->nullable();
            $table->timestamp('due_at')->nullable()->index();
            $table->string('status', 30)->default('detected')->index();
            $table->string('source_key', 191)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'source_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vu_study_items');
    }
};