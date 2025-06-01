<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable();
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable();
            }
            if (!Schema::hasColumn('users', 'age')) {
                $table->integer('age')->nullable();
            }
            if (!Schema::hasColumn('users', 'door_number')) {
                $table->string('door_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'aadhar_number')) {
                $table->string('aadhar_number')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_student')) {
                $table->boolean('is_student')->default(false);
            }
            if (!Schema::hasColumn('users', 'is_employed')) {
                $table->boolean('is_employed')->default(false);
            }
            if (!Schema::hasColumn('users', 'father_id')) {
                $table->unsignedBigInteger('father_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'mother_id')) {
                $table->unsignedBigInteger('mother_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'marital_status')) {
                $table->string('marital_status')->nullable();
            }
            if (!Schema::hasColumn('users', 'blood_group')) {
                $table->string('blood_group')->nullable();
            }
            if (!Schema::hasColumn('users', 'disability_status')) {
                $table->string('disability_status')->nullable();
            }
            if (!Schema::hasColumn('users', 'voter_id')) {
                $table->string('voter_id')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'ration_card_number')) {
                $table->string('ration_card_number')->nullable()->unique();
            }

            // Add foreign keys if they don't exist
            if (!Schema::hasColumn('users', 'father_id')) {
                $table->foreign('father_id')->references('id')->on('users');
            }
            if (!Schema::hasColumn('users', 'mother_id')) {
                $table->foreign('mother_id')->references('id')->on('users');
            }
        });
    }

    public function down(): void
    {
        // For SQLite, we need to create a new table and copy the data
        Schema::create('users_temp', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Copy data from old table to new table
        DB::statement('INSERT INTO users_temp (id, name, email, email_verified_at, password, remember_token, created_at, updated_at)
            SELECT id, name, email, email_verified_at, password, remember_token, created_at, updated_at FROM users');

        // Drop old table
        Schema::drop('users');

        // Rename new table to original name
        Schema::rename('users_temp', 'users');
    }
};
