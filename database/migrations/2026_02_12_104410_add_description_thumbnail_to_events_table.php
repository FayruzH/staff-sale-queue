<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionThumbnailToEventsTable extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {

            if (!Schema::hasColumn('events', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            if (!Schema::hasColumn('events', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('description');
            }

        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {

            if (Schema::hasColumn('events', 'thumbnail')) {
                $table->dropColumn('thumbnail');
            }

            if (Schema::hasColumn('events', 'description')) {
                $table->dropColumn('description');
            }

        });
    }
}
