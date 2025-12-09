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
    Schema::table('overtimes', function (Blueprint $table) {
        $table->integer('rate_per_hour')->default(0)->after('user_id');
    });
}

public function down()
{
    Schema::table('overtimes', function (Blueprint $table) {
        $table->dropColumn('rate_per_hour');
    });
}


    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
};
