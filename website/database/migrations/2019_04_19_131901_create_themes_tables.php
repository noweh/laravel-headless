<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('themes')) {
            Schema::create('themes', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->boolean('published')->nullable();
                $table->string('code');
            });
        }

        if (!Schema::hasTable('theme_translations')) {
            Schema::create('theme_translations', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('active')->nullable()->default(1);
                $table->string('locale', 3)->nullable();
                $table->integer('theme_id')->unsigned()->nullable()->index('ndx_theme_translations_theme_id');
                $table->foreign('theme_id', 'fk_theme_translations_theme_id')
                    ->references('id')->on('themes')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->text('label')->nullable();
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
        Schema::dropIfExists('themes');
        Schema::dropIfExists('theme_translations');
    }
}
