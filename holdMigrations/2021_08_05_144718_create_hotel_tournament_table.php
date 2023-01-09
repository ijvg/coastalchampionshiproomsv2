<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelTournamentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_tournament', function (Blueprint $table) {
            $table->integer('tournament_id')->unsigned()->index();
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade');

            $table->integer('hotel_id')->unsigned()->index();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');

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
        Schema::dropIfExists('hotel_tournament');
    }
}
