<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    if (!Schema::hasTable('user_sessions')) {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('browser_token');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id');
            $table->timestamps();

            $table->unique(['browser_token', 'user_id']);
        });
    }
}

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
