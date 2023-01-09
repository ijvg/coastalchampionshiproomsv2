<?php
            /*$table->string('transaction_type');*/
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->integer('customer_request_id')->unsigned()->index();
            $table->foreign('customer_request_id')
            ->references('id')->on('customer_requests')
            ->onDelete('cascade');



            $table->bigInteger('transaction_type_id')->unsigned()->index();
            $table->foreign('transaction_type_id')
            ->references('id')->on('transaction_types')
            ->onDelete('cascade');

            $table->integer('ccv_transaction_id');

            $table->float('transaction_amount', 10,2);

            $table->dateTimeTz('transaction_datetime', 0);

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
        Schema::dropIfExists('transactions');
    }
}
