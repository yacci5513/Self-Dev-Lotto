<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('draws', function (Blueprint $table) {
            $table->id();
            $table->integer('draw_number');
            $table->date('draw_date');
            $table->integer('number1');
            $table->integer('number2');
            $table->integer('number3');
            $table->integer('number4');
            $table->integer('number5');
            $table->integer('number6');
            $table->integer('bonus_number');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('draws');
    }
}; 