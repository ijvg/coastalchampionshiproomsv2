<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->integer('tournament_id');
            $table->integer('hotel_id');
            $table->bigInteger('team_request_id');
            $table->integer('refunded')->default(0);
            $table->string('confirmation_number', 23);
            $table->decimal('total', 2);
            $table->decimal('remaining', 2);
            $table->decimal('hotel_taxes', 2);
            $table->decimal('hotel_flat_fee', 2);
            $table->decimal('booking_fee', 2);
            $table->decimal('transaction_flat_fee', 2);
            $table->decimal('transaction_percent_fee', 2);
            $table->date('order_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_orders');
    }
}
