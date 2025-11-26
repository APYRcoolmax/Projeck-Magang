<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToAbsencesTable extends Migration
{
    public function up(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            if (!Schema::hasColumn('absences', 'status')) {
                $table->string('status')->default('hadir')->after('time_out');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            if (Schema::hasColumn('absences', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
}
