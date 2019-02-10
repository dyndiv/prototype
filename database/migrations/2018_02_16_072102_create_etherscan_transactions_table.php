<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEtherscanTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etherscan_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('blockNumber')->nulable()->unsigned();
            $table->integer('timeStamp')->nulable()->unsigned();
            $table->string('hash')->nulable();
            $table->integer('nonce')->nulable()->unsigned();
            $table->string('blockHash')->nulable();
            $table->integer('transactionIndex')->nulable()->unsigned();
            $table->string('from')->nulable();
            $table->string('to')->nulable();
            $table->string('value')->nulable();
            $table->integer('gas')->nulable()->unsigned();
            $table->string('gasPrice')->nulable();
            $table->integer('isError')->nulable();
            $table->integer('txreceipt_status')->nulable();
            $table->text('input')->nulable();
            $table->string('contractAddress')->nulable();
            $table->integer('cumulativeGasUsed')->nulable();
            $table->integer('gasUsed')->nulable()->unsigned();
            $table->integer('confirmations')->nulable()->unsigned();
            $table->integer('status')->default(0)->unsigned();
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
        Schema::dropIfExists('"social_accounts"');
    }
}
