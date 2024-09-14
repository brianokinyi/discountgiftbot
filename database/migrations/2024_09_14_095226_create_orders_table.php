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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 16)->unique();
            $table->string('chat_id');
            $table->string('username');
            $table->unsignedBigInteger('gift_card_id');
            $table->foreign('gift_card_id')->references('id')->on('gift_cards');
            $table->float('price', 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
