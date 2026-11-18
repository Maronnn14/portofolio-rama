<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->longText('body')->nullable()->after('full_desc');
            $table->timestamp('published_at')->nullable()->after('status');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('role');
            $table->longText('body')->nullable()->after('description');
            $table->json('achievements')->nullable()->after('body');
            $table->longText('cover_image')->nullable()->after('achievements');
            $table->string('status')->default('published')->after('type');
            $table->timestamp('published_at')->nullable()->after('status');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->boolean('featured')->default(false)->after('level');
        });

        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('ip_address');
        });

        $now = now();

        $personalDefaults = [
            'branding_statement' => 'I build reliable, human-centred digital products — clear interfaces, maintainable code, and measurable outcomes.',
            'cv_file' => null,
            'cv_label' => 'Curriculum Vitae',
            'cv_version' => '2026.1',
            'cv_updated_at' => null,
        ];

        foreach ($personalDefaults as $key => $value) {
            if (! DB::table('personal_info')->where('key', $key)->exists()) {
                DB::table('personal_info')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if (! DB::table('site_settings')->where('key', 'navigation')->exists()) {
            DB::table('site_settings')->insert([
                'key' => 'navigation',
                'value' => json_encode([
                    'order' => ['home', 'about', 'skills', 'projects', 'contact'],
                    'labels' => [
                        'home' => 'Home',
                        'about' => 'About Me',
                        'skills' => 'Skills',
                        'projects' => 'Projects & Experience',
                        'contact' => 'Contact',
                    ],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (! DB::table('site_settings')->where('key', 'home')->exists()) {
            DB::table('site_settings')->insert([
                'key' => 'home',
                'value' => json_encode([
                    'hero_cta_primary_label' => 'View Projects',
                    'hero_cta_primary_url' => '/projects',
                    'hero_cta_secondary_label' => 'Contact Me',
                    'hero_cta_secondary_url' => '/contact',
                    'about_teaser' => null,
                    'contact_cta_heading' => "Let's Work Together",
                    'contact_cta_text' => 'Have a project in mind or want to collaborate? I would love to hear from you.',
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('featured');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn(['slug', 'body', 'achievements', 'cover_image', 'status', 'published_at']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['slug', 'body', 'published_at']);
        });
    }
};
