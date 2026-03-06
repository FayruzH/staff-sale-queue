<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Support beberapa nama index dari migration lama.
        $indexes = [
            'registrations_event_id_employee_identifier_unique',
            'uniq_event_employee',
            'registrations_event_id_employee_id_unique',
        ];

        foreach ($indexes as $index) {
            try {
                DB::statement("ALTER TABLE `registrations` DROP INDEX `{$index}`");
            } catch (\Throwable $e) {
                // index tidak ada, lanjut.
            }
        }
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->unique(['event_id', 'employee_identifier']);
        });
    }
};

