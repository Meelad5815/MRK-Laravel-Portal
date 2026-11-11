<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cloud_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 20);
            $table->string('provider_account_id', 191);
            $table->string('display_name')->nullable();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->json('scopes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'provider']);
            $table->index(['provider', 'provider_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cloud_connections');
    }
};
