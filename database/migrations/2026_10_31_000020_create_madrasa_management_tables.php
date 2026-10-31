<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('madrasa_role', 20)->nullable()->index();
            $table->string('madrasa_id', 64)->nullable()->index();
        });

        Schema::create('madrasa_students', function (Blueprint $table) {
            $table->id();
            $table->string('madrasa_id', 64)->default('mrk-madrasa-58')->index();
            $table->string('student_code', 32)->unique();
            $table->string('full_name', 150);
            $table->string('guardian_name', 150);
            $table->string('guardian_phone', 32)->nullable();
            $table->string('program', 20)->default('nazra')->index();
            $table->string('status', 20)->default('active')->index();
            $table->date('admission_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['madrasa_id', 'status', 'program']);
        });

        Schema::create('madrasa_qari_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('madrasa_students')->cascadeOnDelete();
            $table->foreignId('qari_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('active')->index();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('unassigned_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'qari_user_id']);
            $table->index(['qari_user_id', 'status']);
        });

        Schema::create('madrasa_parent_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('madrasa_students')->cascadeOnDelete();
            $table->foreignId('parent_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_verified')->default(false)->index();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'parent_user_id']);
            $table->index(['parent_user_id', 'is_verified']);
        });

        Schema::create('madrasa_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('madrasa_students')->cascadeOnDelete();
            $table->foreignId('marked_by')->constrained('users')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->string('session', 20)->default('morning');
            $table->string('status', 20)->default('present');
            $table->string('remarks', 500)->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'attendance_date', 'session']);
            $table->index(['attendance_date', 'status']);
        });

        Schema::create('madrasa_daily_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('madrasa_students')->cascadeOnDelete();
            $table->foreignId('qari_user_id')->constrained('users')->cascadeOnDelete();
            $table->date('progress_date');
            $table->string('session', 20)->default('morning');
            $table->string('sabaq', 100)->nullable();
            $table->string('sabqi', 100)->nullable();
            $table->string('manzil', 100)->nullable();
            $table->string('quality', 30)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('parent_visible')->default(false)->index();
            $table->timestamps();
            $table->index(['student_id', 'progress_date']);
            $table->index(['qari_user_id', 'progress_date']);
        });

        Schema::create('madrasa_course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('madrasa_students')->cascadeOnDelete();
            $table->string('program', 20);
            $table->unsignedTinyInteger('completed_paras')->default(0);
            $table->unsignedTinyInteger('current_para')->default(1);
            $table->text('notes')->nullable();
            $table->boolean('parent_visible')->default(false);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'program']);
        });

        Schema::create('madrasa_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('madrasa_students')->cascadeOnDelete();
            $table->foreignId('examiner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('exam_type', 40);
            $table->date('exam_date');
            $table->decimal('total_marks', 7, 2)->default(100);
            $table->decimal('obtained_marks', 7, 2);
            $table->string('grade', 20)->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('parent_visible')->default(false);
            $table->timestamps();
            $table->index(['student_id', 'exam_date']);
        });

        Schema::create('madrasa_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('madrasa_students')->cascadeOnDelete();
            $table->string('certificate_number', 40)->unique();
            $table->string('certificate_type', 40);
            $table->string('verification_token', 64)->unique();
            $table->date('issued_on');
            $table->string('issued_by_name', 150)->nullable();
            $table->string('status', 20)->default('valid')->index();
            $table->timestamps();
        });

        Schema::create('madrasa_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->text('body');
            $table->string('audience', 20)->default('all')->index();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('madrasa_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->string('record_type', 80);
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['record_type', 'record_id']);
            $table->index(['actor_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('madrasa_audit_logs');
        Schema::dropIfExists('madrasa_announcements');
        Schema::dropIfExists('madrasa_certificates');
        Schema::dropIfExists('madrasa_exams');
        Schema::dropIfExists('madrasa_course_progress');
        Schema::dropIfExists('madrasa_daily_progress');
        Schema::dropIfExists('madrasa_attendance');
        Schema::dropIfExists('madrasa_parent_links');
        Schema::dropIfExists('madrasa_qari_assignments');
        Schema::dropIfExists('madrasa_students');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['madrasa_role', 'madrasa_id']);
        });
    }
};
