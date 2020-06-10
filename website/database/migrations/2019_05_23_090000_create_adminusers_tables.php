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
        if (!Schema::hasTable('clients')) {
            Schema::create('clients', function (Blueprint $table) {
                $table->increments('client_id');
				$table->softDeletes();
                $table->timestamps();
                $table->boolean('is_activated')->nullable()->default(true)->index('ndx_clients_is_activated');
                $table->string('name');
                $table->text('logo_url')->nullable();
            });
        }

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
                $table->integer('client_id')->nullable()->unsigned()->index('ndx_admin_users_client_id');
                $table->foreign('client_id', 'fk_admin_users_client_id')
                    ->references('client_id')->on('clients')->onUpdate('NO ACTION')->onDelete('CASCADE');
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
        Schema::dropIfExists('clients');
    }
}
