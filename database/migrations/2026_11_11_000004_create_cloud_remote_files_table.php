<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A previous MySQL attempt may have created the table before Laravel
        // recorded this migration. Preserve that table and its existing data.
        if (Schema::hasTable('cloud_remote_files')) {
            return;
        }

        Schema::create('cloud_remote_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cloud_connection_id')->constrained('cloud_connections')->cascadeOnDelete();
            $table->string('provider', 20);
            $table->string('provider_file_id', 191);
            $table->string('name', 255);
            $table->string('mime_type', 191)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('category', 30)->default('other');
            $table->timestamp('provider_modified_at')->nullable();
            $table->timestamps();
            $table->unique(['cloud_connection_id', 'provider_file_id'], 'cloud_remote_file_unique');
            $table->index(['user_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cloud_remote_files');
    }
};
