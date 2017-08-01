<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt', function (Blueprint $table) {
            $table->increments('id',100);
            $table->string('code',100);
            $table->string('customer_id',5); 
            $table->string('page_id',30)->nullable();           
            $table->string('facebookuser',100);
            $table->string('address',100);
            $table->string('tel',100); 
            $table->float('detailamount',100);
            $table->float('detaildiscount',100);
            $table->float('discount',100);
            $table->float('vat',100);
            $table->float('shipcost',100);
            $table->float('total',100);       
            $table->float('paid',100);
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
        Schema::dropIfExists('receipt');
    }
}
