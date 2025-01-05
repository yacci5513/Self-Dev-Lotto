<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('recommended_numbers');  // 추천된 번호 세트들을 JSON으로 저장
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_histories');
    }
}; 