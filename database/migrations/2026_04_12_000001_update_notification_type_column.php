<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the type column to support application notification types
        Schema::table('notifications', function (Blueprint $table) {
            // Change enum to varchar to support longer values
            $table->string('type', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('type', ['Application', 'Success', 'Message', 'Alert', 'Warning', 'Info'])->default('Info')->change();
        });
    }
};
