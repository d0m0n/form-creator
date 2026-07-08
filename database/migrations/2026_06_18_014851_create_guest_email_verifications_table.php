<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_email_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_user_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('verify_token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_email_verifications');
    }
};
