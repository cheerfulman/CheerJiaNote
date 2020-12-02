<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleUserRelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_user_rels', function (Blueprint $table) {
            $table->increments('id')->comment('主键id');
            $table->integer('article_id')->default(0)->unsigned()->comment('文章id');
            $table->integer('uid')->default(0)->unsigned()->comment('牛牛号');
            $table->integer('created_at')->default(0)->unsigned()->comment('创建时间');
            $table->integer('updated_at')->default(0)->unsigned()->comment('更新时间');
            $table->unique(['article_id','uid'],'unq_artice_uid');
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
        Schema::dropIfExists('article_user_rels');
    }
}
