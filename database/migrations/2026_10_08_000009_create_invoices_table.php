<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('invoices', function(Blueprint $table){ $table->id(); $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete(); $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete(); $table->string('number',40)->unique(); $table->string('title',180); $table->json('items'); $table->decimal('subtotal',12,2)->default(0); $table->decimal('discount',12,2)->default(0); $table->decimal('tax',12,2)->default(0); $table->decimal('total',12,2)->default(0); $table->decimal('paid',12,2)->default(0); $table->string('status',30)->default('unpaid')->index(); $table->date('due_date')->nullable(); $table->text('notes')->nullable(); $table->timestamps(); }); }
 public function down(): void { Schema::dropIfExists('invoices'); }
};