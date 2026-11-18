<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->string('category')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->longText('cover_image')->nullable();
            $table->json('tags')->nullable();
            $table->string('status')->default('published');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
