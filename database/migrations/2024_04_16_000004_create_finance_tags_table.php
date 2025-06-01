<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('finance_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#6B7280'); // Default gray color
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create pivot table for finance_transactions and finance_tags
        Schema::create('finance_transaction_finance_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finance_transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('finance_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('finance_transaction_finance_tag');
        Schema::dropIfExists('finance_tags');
    }
};
