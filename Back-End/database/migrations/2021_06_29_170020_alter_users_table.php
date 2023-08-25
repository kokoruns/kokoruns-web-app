<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('phone',20)->unique()->nullable()->change();
            $table->boolean('phone_verified',2)->default(false);
            $table->boolean('email_verified',2)->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table)
        {
            $table->string('phone',20)->unique()->nullable()->change();
            $table->boolean('phone_verified',2)->default(false);
            $table->boolean('email_verified',2)->default(false);
        });
    }
}
