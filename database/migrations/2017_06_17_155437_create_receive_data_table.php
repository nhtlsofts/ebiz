<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiveDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receive_data', function (Blueprint $table) {
            $table->string('parent_id',100);
            $table->string('oid',100);
            $table->string('type',100);
            $table->text('comments')->nullable();
            $table->integer('Isroot');
            $table->string('sender_id',100);
            $table->string('sender_name',100)->nullable();
            $table->string('receive_id',100);
            $table->string('receive_name',100)->nullable();
            $table->string('facebookuser',100);
            $table->string('post_id',100);
            $table->string('page',100);
            $table->string('attackment',500)->nullable();
            $table->boolean('like');
            $table->boolean('hidden');
            $table->boolean('is_read');
            $table->json('data');
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
        Schema::dropIfExists('receive_data');
    }
}
