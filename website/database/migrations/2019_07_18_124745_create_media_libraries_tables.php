<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaLibrariesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('media_libraries')) {
            Schema::create('media_libraries', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->text('url')->nullable();
                $table->integer('width')->nullable();
                $table->integer('height')->nullable();
                $table->text('public_id')->nullable();
                $table->text('artist')->nullable();
                $table->integer('format')->nullable();
            });
        }

        if (!Schema::hasTable('media_library_translations')) {
            Schema::create('media_library_translations', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('active')->nullable()->default(true);
                $table->string('locale', 3)->nullable();
                $table->integer('media_library_id')
                    ->unsigned()->nullable()->index('ndx_media_library_translations_media_library_id');
                $table->foreign('media_library_id', 'fk_media_library_translations_media_library_id')
                    ->references('id')->on('media_libraries')->onUpdate('NO ACTION')->onDelete('cascade');
                $table->text('title')->nullable();
                $table->text('description')->nullable();
                $table->string('legend', 255)->nullable();
            });
        }

        if (!Schema::hasTable('media_library_slugs')) {
            Schema::create('media_library_slugs', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('active')->nullable();
                $table->timestamps();
                $table->integer('media_library_id')->unsigned()->index('ndx_media_library_slugs_media_library_id');
                $table->string('slug')->nullable();
                $table->string('locale', 2)->index('ndx_media_library_slugs_locale');
                $table->foreign('media_library_id', 'fk_media_library_slugs_media_library_id')
                    ->references('id')->on('media_libraries')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (!Schema::hasTable('mediables')) {
            Schema::create('mediables', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('media_library_id')->unsigned()->index('mediables_media_library_id');
                $table->integer('mediable_id')->unsigned();
                $table->text('mediable_type');
                $table->integer('crop_x')->unsigned()->nullable();
                $table->integer('crop_y')->unsigned()->nullable();
                $table->integer('crop_w')->unsigned()->nullable();
                $table->integer('crop_h')->unsigned()->nullable();
                $table->text('ratio');
                $table->integer("position")->unsigned()->default(0);
                $table->foreign('media_library_id', 'fk_mediables_media_library_id')
                    ->references('id')->on('media_libraries')->onUpdate('NO ACTION')->onDelete('CASCADE');
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
        Schema::dropIfExists('media_libraries');
        Schema::dropIfExists('media_library_translations');
        Schema::dropIfExists('media_library_slugs');
        Schema::dropIfExists('mediables');
    }
}
