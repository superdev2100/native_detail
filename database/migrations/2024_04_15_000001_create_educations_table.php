<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('education_level')->nullable();
            $table->string('school_name')->nullable();
            $table->string('college_name')->nullable();
            $table->string('course_name')->nullable();
            $table->integer('year_of_passing')->nullable();
            $table->float('percentage')->nullable();
            $table->boolean('is_currently_studying')->default(false);
            $table->string('current_class')->nullable();
            $table->string('current_school')->nullable();
            $table->string('scholarship_status')->nullable();
            $table->text('extra_curricular_activities')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};
