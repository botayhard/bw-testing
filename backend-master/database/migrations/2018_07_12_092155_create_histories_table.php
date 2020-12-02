<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoriesTable extends Migration
{

    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->increments('id');
            /*
             * status = 0 - this status means that proposal created
             * status = 1 - this status means that proposal viewed
             * status = 2 - this status means that proposal answered
             */
            $table->integer('status');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->string('title')->nullable();
            $table->integer('proposal_id');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('histories');
    }
}
