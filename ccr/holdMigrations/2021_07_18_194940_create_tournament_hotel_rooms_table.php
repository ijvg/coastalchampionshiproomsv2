<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentHotelRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_hotel_rooms', function (Blueprint $table) {
            $table->id();

            $table->integer('hotel_id')->unsigned()->index();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');

            $table->integer('tournament_id')->unsigned()->index();
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');

            $table->bigInteger('hotel_room_type_id')->unsigned()->index();
            $table->foreign('hotel_room_type_id')->references('id')->on('hotel_room_types')->onDelete('cascade');

            $table->float('price_per_night', 10,2);

            $table->integer('quantity');

            $table->integer('rooms_avaialable');

            $table->integer('held');

            $table->integer('allocated');

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
        Schema::dropIfExists('tournament_hotel_rooms');
    }
}
