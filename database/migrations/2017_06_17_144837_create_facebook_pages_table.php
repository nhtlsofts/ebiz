<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_pages', function (Blueprint $table) {  
            $table->primary('pagesid');          
            $table->string('pagesname',100);
            $table->text('access_token');
            $table->timestamps();
            $table->string('user_id',100);
            $table->boolean('isactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facebook_pages');
    }
}
