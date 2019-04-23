<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
            });
        }*/

        if (!Schema::hasTable('user_questionnaire')) {
            Schema::create('user_questionnaire', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('questionnaire_id')
                    ->unsigned()->nullable()->index('ndx_user_questionnaire_questionnaire_id');
                $table->foreign('questionnaire_id', 'fk_user_questionnaire_questionnaire_id')
                    ->references('id')->on('questionnaires')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->float('note')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('users');
        Schema::dropIfExists('user_questionnaire');
    }
}
