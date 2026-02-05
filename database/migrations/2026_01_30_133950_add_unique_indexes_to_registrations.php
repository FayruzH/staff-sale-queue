<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->unique(['event_id', 'employee_identifier'], 'uniq_event_employee');
            $table->unique(['event_id', 'queue_number'], 'uniq_event_queue');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropUnique('uniq_event_employee');
            $table->dropUnique('uniq_event_queue');
        });
    }

};
