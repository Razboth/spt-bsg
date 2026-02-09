<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('add_app_prefix_to_currenet', function (Blueprint $table) {
            Schema::rename('roles', 'app_roles');
            Schema::rename('permissions', 'app_permissions');
            Schema::rename('model_has_permissions', 'app_model_has_permissions');
            Schema::rename('model_has_roles', 'app_model_has_roles');
            Schema::rename('role_has_permissions', 'app_role_has_permissions');
            Schema::rename('users', 'app_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('add_app_prefix_to_currenet', function (Blueprint $table) {
            //
        });
    }
};
