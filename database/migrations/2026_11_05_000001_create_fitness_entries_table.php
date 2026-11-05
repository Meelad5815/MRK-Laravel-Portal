<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fitness_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['workout', 'run', 'weight']);
            $table->date('entry_date');
            $table->string('title', 120)->nullable();
            $table->decimal('distance_km', 7, 2)->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'type', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fitness_entries');
    }
};
