<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2rayNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2ray_nodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('machine_name')->nullable()->comment('机器名称');
            $table->string('machine_ip')->nullable()->comment('机器IP地址');
            $table->string('machine_port')->nullable()->comment('机器端口');
            $table->text('node_uri')->comment('节点URI配置');
            $table->string('latency')->nullable()->comment('延迟');
            $table->string('speed')->nullable()->comment('速度');
            $table->boolean('status')->default(true)->comment('节点状态：1-启用，0-禁用');
            $table->text('remark')->nullable()->comment('节点备注');
            $table->timestamps();
            
            // 添加索引以提高查询性能
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('v2ray_nodes');
    }
}
