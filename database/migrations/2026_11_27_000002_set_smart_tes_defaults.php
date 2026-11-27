<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $setting = DB::table('app_settings')->first();

        if ($setting) {
            DB::table('app_settings')->where('id', $setting->id)->update([
                'app_name' => 'SMART-TES',
                'school_name' => 'RS PKU Muhammadiyah Boja',
                'updated_at' => now(),
            ]);
        } else {
            DB::table('app_settings')->insert([
                'app_name' => 'SMART-TES',
                'school_name' => 'RS PKU Muhammadiyah Boja',
                'school_address' => null,
                'school_logo' => null,
                'cheat_limit' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Leave branding values unchanged when rolling back.
    }
};
