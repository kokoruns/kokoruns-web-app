<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAssociationTable2 extends Migration
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
            $table->string('facebook')->nullable()->change();
            $table->string('linkedin')->nullable()->change();
            $table->string('instagram')->nullable()->change();
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
            $table->string('facebook')->nullable()->change();
            $table->string('linkedin')->nullable()->change();
            $table->string('instagram')->nullable()->change();
        });
    }
}
