<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            // Kunci asing ke tabel users (siapa yang memiliki gaji ini)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            // Gaji Pokok (nilai integer besar)
            $table->unsignedBigInteger('basic_salary'); 
            
            // Kolom unik untuk memastikan setiap user hanya punya satu record gaji
            $table->unique('user_id'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
