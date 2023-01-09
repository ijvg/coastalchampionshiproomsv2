<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamRequestRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_request_rooms', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('team_request_id')->unsigned()->index();
            $table->foreign('team_request_id')->references('id')->on('team_requests')->onDelete('cascade');

            $table->integer('quantity');

            $table->integer('available');

            $table->integer('allocated');

            $table->bigInteger('hotel_room_type_id');

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
        Schema::dropIfExists('team_request_rooms');
    }
}
