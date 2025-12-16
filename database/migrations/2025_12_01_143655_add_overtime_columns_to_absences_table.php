<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absences', function (Blueprint $table) {
            // Menggunakan decimal untuk durasi jam (lebih akurat dari float)
            $table->decimal('overtime_hours', 8, 2)->default(0)->after('time_out'); 
            
            // Menggunakan decimal untuk nominal uang (lebih akurat dari integer)
            $table->decimal('overtime_pay', 15, 2)->default(0)->after('overtime_hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 🟢 PERBAIKAN WAJIB: Hapus kolom yang ditambahkan
        Schema::table('absences', function (Blueprint $table) {
            $table->dropColumn(['overtime_hours', 'overtime_pay']);
        });
    }
};