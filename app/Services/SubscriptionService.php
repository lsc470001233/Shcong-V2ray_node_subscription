<?php

namespace App\Services;

use App\Models\AdminUser;

class SubscriptionService
{
    /**
     * 生成用户订阅链接
     *
     * @param AdminUser $user
     * @return string|null
     */
    public static function generateSubscriptionLink(AdminUser $user): ?string
    {
        if (empty($user->api_token)) {
            return null;
        }
        
        $subscribeKey = self::getSubscribeKey();
        $baseUrl = self::getBaseUrl();
        
        return $baseUrl . '/api/v1/client/subscribe/' . 
               $subscribeKey . '/' . 
               $user->id . '/' . 
               $user->api_token;
    }
    
    /**
     * 生成API令牌
     *
     * @param AdminUser $user
     * @return string
     */
    public static function generateApiToken(AdminUser $user): string
    {
        return md5($user->id . $user->username . time() . uniqid());
    }
    
    /**
     * 为用户生成并保存API令牌
     *
     * @param AdminUser $user
     * @return AdminUser
     */
    public static function generateAndSaveApiToken(AdminUser $user): AdminUser
    {
        $user->api_token = self::generateApiToken($user);
        $user->save();
        
        return $user;
    }
    
    /**
     * 刷新用户订阅链接（重新生成API令牌）
     *
     * @param AdminUser $user
     * @return string
     */
    public static function refreshSubscriptionLink(AdminUser $user): string
    {
        self::generateAndSaveApiToken($user);
        
        return self::generateSubscriptionLink($user);
    }
    
    /**
     * 验证订阅密钥
     *
     * @param string $subscribeKey
     * @return bool
     */
    public static function validateSubscribeKey(string $subscribeKey): bool
    {
        return $subscribeKey === self::getSubscribeKey();
    }
    
    /**
     * 获取订阅密钥
     *
     * @return string
     */
    public static function getSubscribeKey(): string
    {
        return env('SUBSCRIBE_KEY', '97f7S5EdFe2RUf8');
    }
    
    /**
     * 获取基础URL
     *
     * @return string
     */
    public static function getBaseUrl(): string
    {
        // 优先使用当前请求的域名
        if (request()) {
            return request()->getSchemeAndHttpHost();
        }
        
        // 如果没有请求上下文，使用配置文件中的URL
        return config('app.url', 'http://localhost');
    }
    
    /**
     * 解析订阅链接参数
     *
     * @param string $subscribeKey
     * @param int $adminUserId
     * @param string $apiToken
     * @return array
     */
    public static function parseSubscriptionParams(string $subscribeKey, int $adminUserId, string $apiToken): array
    {
        return [
            'subscribe_key' => $subscribeKey,
            'admin_user_id' => $adminUserId,
            'api_token' => $apiToken,
            'is_valid_key' => self::validateSubscribeKey($subscribeKey)
        ];
    }
}
