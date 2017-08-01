<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiptDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->int('receipt');            
            $table->string('product',100);
            $table->string('product_name',100);
            $table->string('unit',100);
            $table->float('price',100); 
            $table->float('quanlity',100);
            $table->float('discount',100);
            $table->float('amount',100);
            $table->string('facebookuser',100);
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
        Schema::dropIfExists('receipt_detail');
    }
}
