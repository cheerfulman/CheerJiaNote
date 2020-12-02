<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id')->comment('主键id');
            $table->string('title',255)->default('');
            $table->text('content');
            $table->integer('created_at')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_at')->unsigned()->default(0)->comment('更新时间');

            $table->index('created_at','idx_created_at');
            $table->index('updated_at','idx_updated_at');
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
        Schema::dropIfExists('comments');
    }
}
