<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class LockCreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Creates the users table
        Schema::create('lock_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('caller_type')->nullable();
            $table->integer('caller_id')->nullable();
            $table->string('role')->nullable();
            $table->string('type');
            $table->string('action');
            $table->string('resource_type')->nullable();
            $table->integer('resource_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lock_permissions');
    }
}