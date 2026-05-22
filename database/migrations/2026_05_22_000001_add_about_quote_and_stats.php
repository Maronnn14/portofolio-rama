<?php

use App\Models\PersonalInfo;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'about_quote'   => 'Every great developer you know got there by solving problems they were unqualified to solve until they actually did it.',
            'stat_1_value'  => '3+',
            'stat_1_label'  => 'Years Experience',
            'stat_2_value'  => '15+',
            'stat_2_label'  => 'Projects Delivered',
            'stat_3_value'  => '10+',
            'stat_3_label'  => 'Happy Clients',
        ];

        foreach ($defaults as $key => $value) {
            PersonalInfo::firstOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }
    }

    public function down(): void
    {
        PersonalInfo::whereIn('key', [
            'about_quote',
            'stat_1_value', 'stat_1_label',
            'stat_2_value', 'stat_2_label',
            'stat_3_value', 'stat_3_label',
        ])->delete();
    }
};
