<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::table('projects', function(Blueprint $table){ $table->foreignId('customer_id')->nullable()->after('id')->constrained('customers')->nullOnDelete(); $table->date('start_date')->nullable()->after('completed_at'); $table->date('due_date')->nullable()->after('start_date'); $table->unsignedTinyInteger('progress')->default(0)->after('due_date'); }); }
 public function down(): void { Schema::table('projects', function(Blueprint $table){ $table->dropConstrainedForeignId('customer_id'); $table->dropColumn(['start_date','due_date','progress']); }); }
};