<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->restrictOnDelete();
            $table->foreignId('slot_id')->constrained()->restrictOnDelete();
            $table->foreignId('guest_user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('entry_no', 20)->unique();
            $table->string('edit_token', 64)->unique()->nullable();
            $table->string('rep_name', 100);
            $table->unsignedTinyInteger('rep_age');
            $table->string('email', 255);
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
