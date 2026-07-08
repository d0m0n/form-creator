<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->date('game_date');
            $table->string('name', 100);
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('capacity')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['event_id', 'game_date', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
