<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('birth_certificate_cases', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 32)->unique();
            $table->string('child_name', 160);
            $table->date('date_of_birth');
            $table->string('gender', 20);
            $table->string('father_name', 160);
            $table->text('father_cnic')->nullable();
            $table->string('mother_name', 160);
            $table->text('mother_cnic')->nullable();
            $table->string('marital_record', 40)->default('nikah_nama');
            $table->date('nikah_date')->nullable();
            $table->string('birth_place_type', 40)->default('hospital');
            $table->string('hospital_name', 180)->nullable();
            $table->string('birth_district', 100)->nullable();
            $table->string('union_council', 160);
            $table->string('registration_type', 40)->default('normal');
            $table->string('birth_evidence', 100)->nullable();
            $table->json('documents')->nullable();
            $table->string('status', 32)->default('inquiry')->index();
            $table->decimal('government_fee', 12, 2)->default(0);
            $table->decimal('mrk_service_fee', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birth_certificate_cases');
    }
};
