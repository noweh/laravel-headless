<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('question_types')) {
            Schema::create('questions', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->boolean('published')->nullable();
                $table->string('code');
            });
        }

        if (!Schema::hasTable('question_type_translations')) {
            Schema::create('question_type_translations', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('active')->nullable()->default(1);
                $table->integer('question_type_id')
                    ->unsigned()->nullable()->index('ndx_question_type_translations_question_type_id');
                $table->foreign('question_type_id', 'fk_question_type_translations_question_type_id')
                    ->references('id')->on('question_types')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->string('locale', 3)->nullable();
                $table->text('label')->nullable();
            });
        }

        if (!Schema::hasTable('questions')) {
            Schema::create('questions', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->boolean('published')->nullable();
                $table->integer('questionnaire_id')
                    ->unsigned()->nullable()->index('ndx_questions_questionnaire_id');
                $table->foreign('questionnaire_id', 'fk_questions_questionnaire_id')
                    ->references('id')->on('questionnaires')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->integer('question_type_id')
                    ->unsigned()->nullable()->index('ndx_questions_question_type_id');
                $table->foreign('question_type_id', 'fk_questions_question_type_id')
                    ->references('id')->on('question_types')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->text('format')->nullable();
                $table->integer('duration_min')->unsigned();
                $table->integer('duration_max')->unsigned();
                $table->integer("position")->unsigned();
            });
        }

        if (!Schema::hasTable('question_translations')) {
            Schema::create('question_translations', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('active')->nullable()->default(1);
                $table->integer('question_id')
                    ->unsigned()->nullable()->index('ndx_question_translations_question_id');
                $table->foreign('question_id', 'fk_question_translations_question_id')
                    ->references('id')->on('questions')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->string('locale', 3)->nullable();
                $table->text('title')->nullable();
                $table->text('description')->nullable();
            });
        }

        if (!Schema::hasTable('possible_answers')) {
            Schema::create('possible_answers', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->boolean('published')->nullable();
                $table->integer('question_id')
                    ->unsigned()->nullable()->index('ndx_possible_answers_question_id');
                $table->foreign('question_id', 'fk_possible_answers_question_id')
                    ->references('id')->on('questions')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->text('format')->nullable();
                $table->integer("position")->unsigned();
            });
        }

        if (!Schema::hasTable('possible_answer_translations')) {
            Schema::create('possible_answer_translations', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('active')->nullable()->default(1);
                $table->integer('possible_answer_id')
                    ->unsigned()->nullable()->index('ndx_possible_answer_translations_possibe_answer_id');
                $table->foreign('possible_answer_id', 'fk_possible_answer_translations_possible_answer_id')
                    ->references('id')->on('possible_answers')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->string('locale', 3)->nullable();
                $table->text('text')->nullable();
                $table->text('description')->nullable();
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
        Schema::dropIfExists('question_types');
        Schema::dropIfExists('question_type_translations');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_translations');
        Schema::dropIfExists('possible_answers');
        Schema::dropIfExists('possible_answer_translations');
    }
}
