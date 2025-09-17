<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryAndCityToV2rayNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('v2ray_nodes', function (Blueprint $table) {
            $table->string('country')->after('machine_port')->nullable()->comment('国家');
            $table->string('city')->after('country')->nullable()->comment('城市');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('v2ray_nodes', function (Blueprint $table) {
            $table->dropColumn(['country', 'city']);
        });
    }
}
