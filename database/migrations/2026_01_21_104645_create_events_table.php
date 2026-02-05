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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                 // autonumber-ish (kita generate)
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('event_date');                       // MVP: single day dulu
            $table->time('start_time');                       // 09:00
            $table->time('end_time');                         // 15:00
            $table->time('break_start')->nullable();          // 12:00
            $table->time('break_end')->nullable();            // 13:00
            $table->unsignedSmallInteger('batch_duration_min')->default(20);
            $table->unsignedSmallInteger('gap_min')->default(0); // break antar batch (opsional)
            $table->unsignedSmallInteger('capacity_per_batch')->default(15);
            $table->enum('status', ['draft','active','ended'])->default('draft'); // start/turn off
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
