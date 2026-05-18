<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique();
            $table->string('name', 100);
            $table->text('message');
            $table->unsignedTinyInteger('rating')->default(0);
            $table->unsignedBigInteger('posted_at_ms');
            $table->string('session_token')->nullable();
            $table->boolean('hidden')->default(false);
            $table->boolean('flagged')->default(false);
            $table->boolean('pinned')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_messages');
    }
};
