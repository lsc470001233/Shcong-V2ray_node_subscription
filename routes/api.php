<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionLinkController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 订阅路由（注意：api.php会自动添加/api前缀）
Route::get('/v1/client/subscribe/{subscribe_key}/{admin_user_id}/{api_token}', [SubscriptionLinkController::class, 'getSubscriptionContent']);

// 简化版订阅路由（使用token参数）
// Route::get('/api/v1/client/subscribe', [SubscriptionLinkController::class, 'getSubscriptionByToken']);