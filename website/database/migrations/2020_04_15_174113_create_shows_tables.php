<?php

use App\Models\Show;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsTables extends Migration
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
                $table->uuid('show_id')->primary();
                $table->softDeletes();
                $table->timestamps();
                $table->boolean('published')->nullable()->default(true)->index('ndx_shows_published');
                $table->string('title', 250)->nullable();
                $table->enum('type', Show::TYPES)->default(Show::TYPES['REPLAY'])->index('ndx_shows_type');
                $table->integer('client_id')->unsigned()->index('ndx_shows_client_id');
                $table->text('source_desktop_url')->nullable();
                $table->text('source_mobile_url')->nullable();
                $table->text('thumbnail_desktop_url')->nullable();
                $table->text('thumbnail_mobile_url')->nullable();
                $table->foreign('client_id', 'fk_shows_client_id')
                    ->references('client_id')->on('clients')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (!Schema::hasTable('show_slugs')) {
            Schema::create('show_slugs', function (Blueprint $table) {
                $table->increments('show_slug_id');
                $table->softDeletes();
                $table->boolean('active')->nullable();
                $table->timestamps();
                $table->string('show_id', 40)->index('ndx_show_slugs_show_id');
                $table->string('slug')->nullable();
                $table->string('locale', 2)->index('ndx_transition_slugs_locale');
                $table->foreign('show_id', 'fk_show_slugs_show_id')
                    ->references('show_id')->on('shows')->onUpdate('NO ACTION')->onDelete('CASCADE');
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
        Schema::dropIfExists('shows_slug');
        Schema::dropIfExists('shows');
    }
}
