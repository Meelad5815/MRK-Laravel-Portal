<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('company', 160)->nullable();
            $table->string('source', 50)->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->string('priority', 20)->default('normal')->index();
            $table->text('notes')->nullable();
            $table->timestamp('last_contacted_at')->nullable()->index();
            $table->timestamp('next_follow_up_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};