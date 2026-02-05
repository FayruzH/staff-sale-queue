<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->unique(['event_id', 'employee_id']);
        });
    }
    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropUnique(['event_id', 'employee_id']);
        });
    }

};
