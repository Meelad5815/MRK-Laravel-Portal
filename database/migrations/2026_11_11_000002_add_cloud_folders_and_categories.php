<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cloud_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 60);
            $table->timestamps();
            $table->unique(['user_id', 'name']);
        });

        Schema::table('cloud_files', function (Blueprint $table) {
            $table->foreignId('cloud_folder_id')
                ->nullable()
                ->after('user_id')
                ->constrained('cloud_folders')
                ->nullOnDelete();
            $table->string('category', 20)->default('other')->after('cloud_folder_id');
            $table->index(['user_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::table('cloud_files', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'category']);
            $table->dropConstrainedForeignId('cloud_folder_id');
            $table->dropColumn('category');
        });

        Schema::dropIfExists('cloud_folders');
    }
};
