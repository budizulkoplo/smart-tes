<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->unique()->after('nisn');
            $table->string('no_hp', 20)->nullable()->after('gender');
            $table->text('alamat')->nullable()->after('no_hp');
        });

        DB::table('students')->whereNull('nik')->orderBy('id')->each(function ($student) {
            DB::table('students')->where('id', $student->id)->update([
                'nik' => (string) $student->nisn,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['nik']);
            $table->dropColumn(['nik', 'no_hp', 'alamat']);
        });
    }
};
