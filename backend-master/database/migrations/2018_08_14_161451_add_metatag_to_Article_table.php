<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetatagToArticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table("articles", function (Blueprint $table) {
            $table->string("meta_id")->after("unique_name")->default(null)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table("articles", function (Blueprint $table) {
            $table->dropColumn("meta_id");
        });
    }
}
