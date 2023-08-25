<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAssociationTable5 extends Migration
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
            //$table->string('founded_month'); 
            //$table->string('founded_year'); 
            //$table->dropColumn('founded');
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
            $table->dropColumn('founded_month'); 
            $table->dropColumn('founded_year');
            $table->string('founded');
        });
    }
}
