<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_rooms', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('customer_id')->unsigned()->index();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            $table->bigInteger('tournament_hotel_room_type_id')->unsigned()->index();
            $table->foreign('tournament_hotel_room_type_id')->references('id')->on('tournament_hotel_rooms')->onDelete('cascade');

            $table->integer('quantity');

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
        Schema::dropIfExists('customer_rooms');
    }
}
