<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsAndFieldsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('shows')) {
            Schema::create('shows', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->boolean('published')->nullable()->default(true);
            });
        }

        if (!Schema::hasTable('module_types')) {
            Schema::create('module_types', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('published')->nullable()->default(true);
                $table->string('label', 5)->nullable();
            });
        }

        if (!Schema::hasTable('module_fields')) {
            Schema::create('module_fields', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('published')->nullable()->default(true);
                $table->string('label', 10)->nullable();
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
        Schema::dropIfExists('module_fields');
        Schema::dropIfExists('module_types');
        Schema::dropIfExists('shows');
    }
}
