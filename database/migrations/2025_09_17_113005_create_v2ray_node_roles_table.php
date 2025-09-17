<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV2rayNodeRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2ray_node_roles', function (Blueprint $table) {
            $table->bigInteger('v2ray_node_id')->unsigned()->comment('V2Ray节点ID');
            $table->bigInteger('admin_role_id')->unsigned()->comment('管理员角色ID');
            
            // 创建联合主键
            $table->primary(['v2ray_node_id', 'admin_role_id']);
            
            // 创建外键约束
            $table->foreign('v2ray_node_id')->references('id')->on('v2ray_nodes')->onDelete('cascade');
            $table->foreign('admin_role_id')->references('id')->on('admin_roles')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('v2ray_node_roles');
    }
}
