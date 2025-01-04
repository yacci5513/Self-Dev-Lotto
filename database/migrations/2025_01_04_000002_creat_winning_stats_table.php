<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('winning_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained('draws')->onDelete('cascade');
            $table->integer('rank1_winners')->default(0);
            $table->bigInteger('rank1_prize_amount')->default(0);
            $table->integer('rank2_winners')->default(0);
            $table->bigInteger('rank2_prize_amount')->default(0);
            $table->integer('rank3_winners')->default(0);
            $table->bigInteger('rank3_prize_amount')->default(0);
            $table->integer('rank4_winners')->default(0);
            $table->bigInteger('rank4_prize_amount')->default(0);
            $table->integer('rank5_winners')->default(0);
            $table->bigInteger('rank5_prize_amount')->default(0);
            $table->bigInteger('total_prize_amount')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('winning_stats');
    }
}; 