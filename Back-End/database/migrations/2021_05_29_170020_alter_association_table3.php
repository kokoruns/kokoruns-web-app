<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAssociationTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('associations', function($table)
        {
            $table->string('tags')->nullable()->change();
            $table->string('field')->nullable()->change();
            $table->string('logo')->nullable()->change();
            //$table->string('founded')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('associations', function($table)
        {
            $table->string('tags')->nullable()->change();
            $table->string('field')->nullable()->change();
            $table->string('logo')->nullable()->change();
            $table->string('founded')->nullable()->change();
        });
    }
}
