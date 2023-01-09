<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('team_request_id')->unsigned()->index();
            $table->foreign('team_request_id')
            ->references('id')->on('team_requests');

            $table->string('first_name');

            $table->string('last_name');

            $table->tinyInteger('phone_number');

            $table->string('email', 100);

            $table->integer('ccv_id');

            $table->float('deposit_amount', 10,2);

            $table->float('remaining_amount', 10,2);

            $table->integer('refunded');

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
        Schema::dropIfExists('customers');
    }
}
