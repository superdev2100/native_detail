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
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->change();
            $table->integer('age')->nullable()->change();
            $table->string('door_number')->nullable()->change();
            $table->string('aadhar_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable(false)->change();
            $table->integer('age')->nullable(false)->change();
            $table->string('door_number')->nullable(false)->change();
            $table->string('aadhar_number')->nullable(false)->change();
        });
    }
};
