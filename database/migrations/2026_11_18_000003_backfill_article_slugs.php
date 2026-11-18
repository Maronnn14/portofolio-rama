<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->backfill('projects', 'name');
        $this->backfill('experiences', 'role');
        $this->backfill('contributions', 'name');
    }

    public function down(): void
    {
        // Slugs are part of the public URL. No destructive rollback.
    }

    private function backfill(string $table, string $sourceColumn): void
    {
        $rows = DB::table($table)
            ->where(fn ($query) => $query->whereNull('slug')->orWhere('slug', ''))
            ->orderBy('id')
            ->get(['id', $sourceColumn]);

        foreach ($rows as $row) {
            $base = Str::slug((string) $row->{$sourceColumn}) ?: Str::slug($table).'-'.$row->id;
            $slug = $base;
            $suffix = 2;

            while (DB::table($table)->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                $slug = $base.'-'.$suffix++;
            }

            DB::table($table)->where('id', $row->id)->update(['slug' => $slug]);
        }
    }
};
