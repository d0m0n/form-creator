<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entry_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order');
            $table->string('name', 100);
            $table->unsignedTinyInteger('age');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->timestamps();
            $table->unique(['entry_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_members');
    }
};
