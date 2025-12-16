<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom baru untuk menyimpan nominal lembur per jam
            // Menggunakan decimal agar bisa menyimpan nilai mata uang yang detail
            $table->decimal('overtime_hourly_rate', 10, 2)->nullable()->after('role'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $table->dropColumn('overtime_hourly_rate');
        });
    }
};
