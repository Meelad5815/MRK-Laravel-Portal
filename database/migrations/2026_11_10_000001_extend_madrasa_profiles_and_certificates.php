<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('madrasa_students', function (Blueprint $table) {
            $table->string('previous_institution', 180)->nullable();
        });

        Schema::table('madrasa_certificates', function (Blueprint $table) {
            $table->string('external_institution', 180)->nullable();
            $table->string('pdf_path', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('madrasa_certificates', function (Blueprint $table) {
            $table->dropColumn(['external_institution', 'pdf_path']);
        });

        Schema::table('madrasa_students', function (Blueprint $table) {
            $table->dropColumn('previous_institution');
        });
    }
};
