<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUsersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('admin_users')) {
            Schema::create('admin_users', function (Blueprint $table) {
                $table->increments('admin_user_id');
                $table->softDeletes();
                $table->timestamps();
                $table->boolean('is_activated')->nullable()->default(true)->index('ndx_admin_users_is_activated');
                $table->boolean('is_superadmin')->nullable()->default(false)->index('ndx_admin_users_is_superadmin');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->index('ndx_admin_users_email');
                $table->string('password');
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
        Schema::dropIfExists('admin_users');
    }
}
