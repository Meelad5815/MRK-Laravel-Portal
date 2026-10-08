<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up():void { Schema::create('leads', function(Blueprint $table){ $table->id(); $table->string('name',100); $table->string('email')->nullable(); $table->string('phone',30)->nullable(); $table->string('project_type',80); $table->text('message'); $table->string('status',30)->default('new')->index(); $table->timestamps(); }); }
    public function down():void { Schema::dropIfExists('leads'); }
};
