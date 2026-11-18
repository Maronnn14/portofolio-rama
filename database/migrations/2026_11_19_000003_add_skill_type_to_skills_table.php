<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            // "tech"  = coding / tech stack skills (Frontend, Backend, Tools, ...)
            // "other" = non-coding hard skills (Excel, Design, Office, ...)
            $table->string('skill_type', 20)->default('tech')->after('category')->index();
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropIndex(['skill_type']);
            $table->dropColumn('skill_type');
        });
    }
};
