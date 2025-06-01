<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_monthly_saving_scheme_member')->default(false);
            $table->decimal('monthly_saving_amount', 10, 2)->nullable();
            $table->date('last_payment_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_monthly_saving_scheme_member',
                'monthly_saving_amount',
                'last_payment_date',
            ]);
        });
    }
};
