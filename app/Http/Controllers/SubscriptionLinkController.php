<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use App\Models\V2rayNode;
use App\Services\SubscriptionService;

class SubscriptionLinkController extends Controller
{
    /**
     * 显示订阅链接列表
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建订阅链接的表单
     */
    public function create()
    {
        //
    }

    /**
     * 存储新创建的订阅链接
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * 显示指定的订阅链接
     */
    public function show($id)
    {
        //
    }

    /**
     * 显示编辑订阅链接的表单
     */
    public function edit($id)
    {
        //
    }

    /**
     * 更新指定的订阅链接
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定的订阅链接
     */
    public function destroy($id)
    {
        //
    }

    /**
     * 生成订阅链接
     */
    public function generateSubscription($token)
    {
        //
    }

    /**
     * 获取订阅内容（通过路径参数）
     */
    public function getSubscriptionContent($subscribe_key, $admin_user_id, $api_token)
    {
        // 1. 验证订阅密钥
        if (!SubscriptionService::validateSubscribeKey($subscribe_key)) {
            return response('1', 200, [
                'Content-Type' => 'text/plain; charset=utf-8'
            ]);
        }

        // 2. 验证用户ID和API令牌
        $user = AdminUser::where('id', $admin_user_id)
            ->where('api_token', $api_token)
            ->where('status',true)
            ->first();

        if (!$user) {
            return response('1', 200, [
                'Content-Type' => 'text/plain; charset=utf-8'
            ]);
        }

        // 4. 获取用户拥有的角色对应的节点
        // 首先获取用户的启用状态角色ID
        $userRoleIds = $user->roles()->where('status', 1)->pluck('id')->toArray();

        if (empty($userRoleIds)) {
            return response('1', 200, [
                'Content-Type' => 'text/plain; charset=utf-8'
            ]);
        }

        // 查询与用户启用角色关联的V2ray节点
        $nodes = V2rayNode::whereHas('adminRoles', function ($query) use ($userRoleIds) {
            $query->whereIn('admin_roles.id', $userRoleIds)
                  ->where('admin_roles.status', 1); // 确保角色状态为启用
        })
            ->where('status', 1) // 只获取启用的节点
            ->orderBy('country')
            ->orderBy('city')
            ->orderBy('id')
            ->get();

        if ($nodes->isEmpty()) {
            return response('', 200, [
                'Content-Type' => 'text/plain; charset=utf-8'
            ]);
        }

        // 5. 生成订阅内容（base64编码的节点信息）
        $nodeUris = [];
        foreach ($nodes as $node) {
            if (!empty($node->node_uri)) {
                $nodeUris[] = $node->node_uri;
            }
        }

        if (empty($nodeUris)) {
            return response('', 200, [
                'Content-Type' => 'text/plain; charset=utf-8'
            ]);
        }

        // 将所有节点URI合并为一个字符串，每行一个
        $subscriptionContent = implode("\n", $nodeUris);

        // 对内容进行base64编码（V2ray订阅格式）
        $base64Content = base64_encode($subscriptionContent);

        // 返回V2ray订阅格式
        return response($base64Content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'no-cache',
            'Content-Disposition' => 'attachment; filename="v2ray_subscription.txt"'
        ]);
    }

    /**
     * 获取订阅内容（通过查询参数）
     */
    // public function getSubscriptionByToken(Request $request)
    // {
    //     $token = $request->query('token');

    //     if (!$token) {
    //         return response('Token required', 400);
    //     }

    //     // TODO: 根据token解析用户信息
    //     // TODO: 获取用户关联的V2ray节点
    //     // TODO: 返回节点配置内容

    //     return response('订阅内容生成中...', 200, [
    //         'Content-Type' => 'text/plain; charset=utf-8'
    //     ]);
    // }
}
