<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('occupations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('occupation_type')->nullable();
            $table->string('company_name')->nullable();
            $table->string('job_title')->nullable();
            $table->float('monthly_income')->nullable();
            $table->string('work_location')->nullable();
            $table->integer('work_experience')->nullable();
            $table->text('skills')->nullable();
            $table->boolean('is_self_employed')->default(false);
            $table->string('business_type')->nullable();
            $table->text('business_address')->nullable();
            $table->float('business_income')->nullable();
            $table->text('government_scheme_benefits')->nullable();
            $table->string('pension_status')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('occupations');
    }
};
