<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('registrations', 'employee_email')) {
                $table->string('employee_email')->nullable()->after('employee_name');
            }

            if (!Schema::hasColumn('registrations', 'employee_phone')) {
                $table->string('employee_phone', 30)->nullable()->after('employee_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (Schema::hasColumn('registrations', 'employee_phone')) {
                $table->dropColumn('employee_phone');
            }

            if (Schema::hasColumn('registrations', 'employee_email')) {
                $table->dropColumn('employee_email');
            }
        });
    }
};

