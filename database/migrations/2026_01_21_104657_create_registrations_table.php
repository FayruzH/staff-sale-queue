<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();

            // MVP: belum SSO, jadi simpan employee_name dulu
            $table->string('employee_identifier'); // nanti bisa NIP/employee_id
            $table->string('employee_name');

            $table->string('queue_number'); // contoh B07-012
            $table->timestamps();

            $table->unique(['event_id','employee_identifier']); // 1 employee 1 batch per event
            $table->unique(['batch_id','queue_number']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
