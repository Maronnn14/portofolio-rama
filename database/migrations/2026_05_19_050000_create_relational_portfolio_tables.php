<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop legacy tables
        Schema::dropIfExists('portfolio_data');
        Schema::dropIfExists('portfolio_settings');

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_desc');
            $table->text('full_desc')->nullable();
            $table->string('category')->default('Frontend');
            $table->string('status')->default('published');
            $table->text('thumbnail')->nullable();
            $table->json('tech')->nullable();
            $table->string('live_url')->nullable();
            $table->string('source_url')->nullable();
            $table->boolean('featured')->default(false);
            $table->json('gallery')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('Frontend');
            $table->string('icon')->nullable();
            $table->string('teaser')->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('proficiency')->default(50);
            $table->string('level')->default('Intermediate');
            $table->json('related_projects')->nullable();
            $table->json('gallery')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('company');
            $table->string('location')->nullable();
            $table->string('start_date');
            $table->string('end_date')->default('Present');
            $table->string('type')->default('Work');
            $table->text('description')->nullable();
            $table->json('tech')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('socials', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->string('url');
            $table->string('label')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('interests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->default('🎯');
            $table->string('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->string('alt')->nullable();
            $table->string('category')->nullable();
            $table->boolean('visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('personal_info', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_info');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('interests');
        Schema::dropIfExists('socials');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('projects');

        // Recreate legacy tables if rolling back
        Schema::create('portfolio_data', function (Blueprint $table) {
            $table->id();
            $table->string('section')->unique();
            $table->json('payload');
            $table->timestamps();
        });

        Schema::create('portfolio_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }
};
