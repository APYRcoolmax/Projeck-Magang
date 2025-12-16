<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyStatusesTable extends Migration
{
    public function up()
    {
        Schema::create('daily_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Kolom Inti
            $table->date('date');
            
            // 🟢 PERBAIKAN: Menggunakan 'type' untuk jenis pengajuan (izin/sakit/cuti)
            // Dan 'status' di tabel ini akan mencerminkan status approval
            $table->string('type')->comment('Tipe pengajuan: izin, sakit, cuti');
            
            // 🟢 TAMBAHAN: Detail Pengajuan dari Karyawan
            $table->text('reason')->nullable();
            $table->string('attachment')->nullable()->comment('Path file surat dokter/pendukung');

            // 🟢 TAMBAHAN: Status Persetujuan Admin
            $table->string('approval_status')->default('pending')->comment('Status persetujuan: pending, approved, declined');

            // Opsional: Siapa yang menyetujui (untuk audit)
            $table->foreignId('approved_by')->nullable()->constrained('users');
            
            $table->timestamps();

            // PENTING: Membuat constraint unik agar user hanya bisa mengajukan 1 status per hari
            $table->unique(['user_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_statuses');
    }
}