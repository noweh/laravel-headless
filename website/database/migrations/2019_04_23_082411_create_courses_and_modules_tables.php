<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesAndModulesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->boolean('published')->nullable();
                $table->integer("position")->unsigned();
            });
        }

        if (!Schema::hasTable('module_translations')) {
            Schema::create('module_translations', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('active')->nullable()->default(1);
                $table->string('locale', 3)->nullable();
                $table->integer('module_id')
                    ->unsigned()->nullable()->index('ndx_module_translations_module_id');
                $table->foreign('module_id', 'fk_module_translations_module_id')
                    ->references('id')->on('modules')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->text('title')->nullable();
                $table->text('description')->nullable();
            });
        }

        if (!Schema::hasTable('module_theme')) {
            Schema::create('module_theme', function (Blueprint $table) {
                $table->integer('module_id')->unsigned()->index('module_theme_module_id');
                $table->integer('theme_id')->unsigned()->index('module_theme_theme_id');
                $table->primary(['module_id','theme_id']);
                $table->foreign('module_id', 'fk_module_theme_module_id')
                    ->references('id')->on('modules')->onUpdate('NO ACTION')->onDelete('CASCADE');
                $table->foreign('theme_id', 'fk_module_theme_theme_id')
                    ->references('id')->on('themes')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->boolean('published')->nullable();
                $table->integer('module_id')
                    ->unsigned()->nullable()->index('ndx_courses_module_id');
                $table->foreign('module_id', 'fk_courses_module_id')
                    ->references('id')->on('modules')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->text('format')->nullable();
                $table->integer("position")->unsigned();
            });
        }

        if (!Schema::hasTable('course_translations')) {
            Schema::create('course_translations', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('active')->nullable()->default(1);
                $table->string('locale', 3)->nullable();
                $table->integer('course_id')
                    ->unsigned()->nullable()->index('ndx_course_translations_course_id');
                $table->foreign('course_id', 'fk_course_translations_course_id')
                    ->references('id')->on('courses')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->text('title')->nullable();
                $table->text('description')->nullable();
            });
        }

        if (!Schema::hasTable('course_theme')) {
            Schema::create('course_theme', function (Blueprint $table) {
                $table->integer('course_id')->unsigned()->index('course_theme_course_id');
                $table->integer('theme_id')->unsigned()->index('course_theme_theme_id');
                $table->primary(['course_id','theme_id']);
                $table->foreign('course_id', 'fk_course_theme_course_id')
                    ->references('id')->on('modules')->onUpdate('NO ACTION')->onDelete('CASCADE');
                $table->foreign('course_id', 'fk_course_themes_theme_id')
                    ->references('id')->on('themes')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (!Schema::hasColumn('questionnaires', 'module_id')) {
            Schema::table('questionnaires', function (Blueprint $table) {
                $table->integer('module_id')
                    ->unsigned()->nullable()->index('ndx_questionnaires_module_id');
                $table->foreign('module_id', 'fk_questionnaires_module_id')
                    ->references('id')->on('modules')->onUpdate('NO ACTION')->onDelete('set null');
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
        Schema::dropIfExists('modules');
        Schema::dropIfExists('module_translations');
        Schema::dropIfExists('module_theme');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_translations');
        Schema::dropIfExists('course_theme');
    }
}
