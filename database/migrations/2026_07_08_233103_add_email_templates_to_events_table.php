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
        Schema::table('events', function (Blueprint $table) {
            $table->text('email_header')->nullable()->after('notes');
            $table->text('email_body')->nullable()->after('email_header');
            $table->text('email_footer')->nullable()->after('email_body');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['email_header', 'email_body', 'email_footer']);
        });
    }
};
