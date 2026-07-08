<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_event_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['guest_user_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_event_owners');
    }
};
