<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCancelOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancel_orders', function (Blueprint $table) {
            $table->id();
            $table->string('confirmation_number', 23);
            $table->longText('cancellation_description');
            $table->integer('tournament_id');
            $table->integer('hotel_id');
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
        Schema::dropIfExists('cancel_orders');
    }
}
