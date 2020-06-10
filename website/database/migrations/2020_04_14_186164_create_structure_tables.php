<?php

use App\Models\ModuleField;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStructureTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('module_types')) {
            Schema::create('module_types', function (Blueprint $table) {
                $table->increments('module_type_id');
                $table->softDeletes();
                $table->timestamps();
                $table->boolean('published')->nullable()->default(true)->index('ndx_module_types_published');
                $table->string('structure_name', 10)->nullable()->index('ndx_module_types_structure_name');
            });
        }

        if (!Schema::hasTable('module_fields')) {
            Schema::create('module_fields', function (Blueprint $table) {
                $table->increments('module_field_id');
                $table->softDeletes();
                $table->timestamps();
                $table->boolean('published')->nullable()->default(true)->index('ndx_module_fields_published');
                $table->string('structure_name', 10)->nullable()->index('ndx_module_fields_structure_name');
                $table->enum('type', ModuleField::STRUCTURE_TYPES)->default('string')->index('ndx_module_fields_type');
                $table->boolean('is_readable')->default(true)->index('ndx_module_fields_is_readable');
            });
        }

        if (!Schema::hasTable('module_type_field_associations')) {
            Schema::create('module_type_field_associations', function (Blueprint $table) {
                $table->increments('module_type_field_association_id');
                $table->softDeletes();
                $table->timestamps();
                $table->boolean('published')->nullable()->default(true)
                    ->index('ndx_module_type_field_associations_published');
                $table->integer('module_type_id')->unsigned()->index('ndx_module_associations_type_id');
                $table->integer('module_field_id')->unsigned()->index('ndx_module_associations_field_id');
                $table->integer("position")->unsigned();
                $table->foreign('module_type_id', 'fk_module_associations_type_id')
                    ->references('module_type_id')->on('module_types')->onUpdate('NO ACTION')->onDelete('CASCADE');
                $table->foreign('module_field_id', 'fk_module_associations_field_id')
                    ->references('module_field_id')->on('module_fields')->onUpdate('NO ACTION')->onDelete('CASCADE');
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
        Schema::dropIfExists('module_type_field_associations');
        Schema::dropIfExists('module_fields');
        Schema::dropIfExists('module_types');
    }
}
