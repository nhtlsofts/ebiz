<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Customer', function (Blueprint $table) {
            $table->increments('oid',100);
            $table->string('customer_code',100);
            $table->string('fbid',100)->nullable();
            $table->string('facebookuser',100)->nullable();                       
            $table->string('name',100);
            $table->string('email',100);
            $table->string('address',100);
            $table->string('tel',100); 
            $table->string('province',100);
            $table->string('district',100);
            $table->boolean('banned');
            $table->timestamp('banned_date')->nullable();
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
        Schema::dropIfExists('Customer');
    }
}
