<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->string('rep_phone', 20)->after('rep_name')->default('');
            $table->dropColumn('rep_age');
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->unsignedTinyInteger('rep_age')->after('rep_name')->default(0);
            $table->dropColumn('rep_phone');
        });
    }
};
