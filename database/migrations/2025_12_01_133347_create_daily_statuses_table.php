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
            $table->date('date');
            $table->string('status')->default('hadir'); // hadir, izin, sakit, alpha
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_statuses');
    }
}
