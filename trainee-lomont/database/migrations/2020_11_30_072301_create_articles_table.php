<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id')->comment('文章id');  // `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '文章id',
            $table->integer('uid')->unsigned()->default(0)->comment('作者'); // `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '作者',
            $table->string('title',255)->default('')->coment('文章标题'); //  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题',
            $table->text('content')->comment('文章内容');
            $table->string('poster_url',255)->default('')->comment('图片url');
            $table->integer('status')->default(0)->comment('文章状态');
            $table->integer('censor_sid')->unsigned()->default(0)->comment('审核sid');
            $table->integer('created_at')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_at')->unsigned()->default(0)->comment('更新时间');

//            $table->primary('id');
            $table->index('created_at','idx_created_at');
            $table->index('updated_at','idx_updated_at');
            $table->index('uid','idx_uid');
            $table->index('censor_sid','idx_censor_sid');
            $table->index('status','idx_status');
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
        Schema::dropIfExists('articles');
    }
}
