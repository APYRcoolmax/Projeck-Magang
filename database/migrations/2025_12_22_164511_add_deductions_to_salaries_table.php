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
    Schema::table('salaries', function (Blueprint $table) { // Ganti 'Blade' menjadi 'Blueprint'
        $table->decimal('late_deduction', 15, 2)->default(0)->after('basic_salary');
        $table->decimal('alpha_deduction', 15, 2)->default(0)->after('late_deduction');
    });
}

public function down()
{
    Schema::table('salaries', function (Blueprint $table) {
        $table->dropColumn(['late_deduction', 'alpha_deduction']);
    });
}
};
