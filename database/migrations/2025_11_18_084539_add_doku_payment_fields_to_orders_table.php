<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('invoice_number')->nullable();
            $table->string('payment_token')->nullable();
            $table->string('payment_url')->nullable();
            $table->timestamp('payment_expired_at')->nullable();
            $table->string('payment_status')->default('pending'); // pending | paid | failed | expired
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_number', 'payment_token', 'payment_url',
                'payment_expired_at', 'payment_status'
            ]);
        });
    }

};
