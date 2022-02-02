<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewPermissionsTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement("SET foreign_key_checks=0");

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('assigned_roles');
        Schema::dropIfExists('permission_histories');
        Schema::dropIfExists('user_role_histories');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('user_group');
        Schema::dropIfExists('permissions');

        Schema::table('users', function (Blueprint $table){
            $table->dropColumn('branch_id');
            $table->dropColumn('user_group_id');
            $table->dropColumn('last_login');
        });

        Schema::create('user_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permissions', function (Blueprint $table){
            $table->increments('id');
            $table->string('short_name')->nullable();
            $table->string('path')->nullable();
            $table->string('name')->nullable();
            $table->unsignedInteger('module_id')->nullable();
        });

        Schema::create('user_group', function(Blueprint $table){
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('user_group_id')->index();
            $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('cascade');
        });

        Schema::create('user_group_permission', function (Blueprint $table) {
            $table->unsignedInteger('user_group_id')->index();
            $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('cascade');

            $table->unsignedInteger('permission_id')->index();
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        Schema::create('user_logins', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('ip');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('permission_histories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('triggerer_user_id')->nullable();
            $table->unsignedInteger('permission_id')->nullable();
            $table->unsignedInteger('user_group_id')->nullable();
            $table->string('mode', 20)->nullable();
            $table->timestamps();

            $table->foreign('triggerer_user_id')->references('id')->on('users');
            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->foreign('user_group_id')->references('id')->on('user_groups');
        });

        Schema::create('user_group_histories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('triggerer_user_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('user_group_id')->nullable();
            $table->string('mode', 20)->nullable();
            $table->timestamps();

            $table->foreign('triggerer_user_id')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('user_group_id')->references('id')->on('user_groups');
        });

        DB::table('modules')->truncate();

        Schema::create('user_owner', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('owner_id')->index();
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
        });

        Schema::create('user_owner_histories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('triggerer_user_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('owner_id')->nullable();
            $table->string('mode', 20)->nullable();
            $table->timestamps();

            $table->foreign('triggerer_user_id')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('owner_id')->references('id')->on('owners');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->date('active_to')->nullable()->after('locked_at');

            $table->boolean('is_external')->nullable()->default(0)->after('id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('without_restrictions')->default(1)->nullable()->after('is_external');
        });

        DB::statement("SET foreign_key_checks=1");
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
