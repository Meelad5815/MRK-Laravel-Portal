<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('blog_posts', function(Blueprint $table){ $table->id(); $table->string('title',180); $table->string('slug',200)->unique(); $table->string('category',80)->nullable()->index(); $table->string('excerpt',320); $table->longText('content'); $table->string('status',20)->default('draft')->index(); $table->boolean('featured')->default(false); $table->string('seo_title',180)->nullable(); $table->string('seo_description',320)->nullable(); $table->timestamp('published_at')->nullable()->index(); $table->timestamps(); }); }
 public function down(): void { Schema::dropIfExists('blog_posts'); }
};