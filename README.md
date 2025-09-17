# V2ray 订阅管理系统

基于 Laravel 8 和 Dcat Admin 构建的 V2ray 订阅链接管理系统。

## 项目简介

这是一个专为 V2ray 节点管理而设计的订阅系统，提供完整的节点管理、用户权限控制和订阅链接生成功能。
就是个人用的，自己手动分配用户组，分配节点。不提供给外部的人注册订阅。
只是用于整理自己的节点以及分享节点给同事

## 主要功能

- 🚀 **节点管理** - 支持添加、编辑、删除 V2ray 节点
- 👥 **用户管理** - 基于角色的用户权限管理系统
- 🔗 **订阅链接** - 自动生成用户专属订阅链接
- 🌍 **地理位置** - 支持节点国家和城市标识
- 📊 **节点监控** - 延迟和速度监控功能
- 🎛️ **管理面板** - 基于 Dcat Admin 的现代化管理界面

## 技术栈

- **后端框架**: Laravel 8.x
- **管理面板**: Dcat Admin 2.x
- **数据库**: MySQL
- **认证**: Laravel Sanctum
- **前端**: Bootstrap + Vue.js (Admin界面)

## 环境要求

- PHP >= 7.3 或 8.0
- MySQL >= 5.7
- Composer
- Node.js (可选，用于前端编译)

## 安装指南

### 1. 克隆项目

```bash
git clone <repository-url>
cd v2ray.shcong.local
```

### 2. 安装依赖

```bash
composer install
npm install  # 可选
```

### 3. 环境配置

```bash
cp .env.example .env
php artisan key:generate
```

编辑 `.env` 文件，配置数据库连接：

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=v2ray_subscription
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. 数据库迁移

```bash
php artisan migrate
php artisan db:seed 
```

### 5. 启动服务

```bash
php artisan serve
```

访问 `http://localhost:8000/admin` 进入管理面板。

## API 接口

### 订阅链接

```
GET /api/v1/client/subscribe/{subscribe_key}/{admin_user_id}/{api_token}
```

> 需要手动修改.env文件，修改SUBSCRIBE_KEY的内容

返回 V2ray 客户端可用的订阅配置。

## 项目结构

```
app/
├── Admin/                 # Dcat Admin 相关文件
│   └── Controllers/       # 管理面板控制器
├── Http/Controllers/      # API 控制器
├── Models/               # Eloquent 模型
│   ├── AdminUser.php     # 管理员用户模型
│   ├── AdminRole.php     # 角色模型
│   └── V2rayNode.php     # V2ray节点模型
└── Services/             # 业务逻辑服务
    └── SubscriptionService.php
```

## 数据库表结构

- `admin_users` - 管理员用户表
- `admin_roles` - 角色表
- `v2ray_nodes` - V2ray节点表
- `v2ray_node_roles` - 节点角色关联表

## 配置说明

主要配置文件：

- `config/admin.php` - Dcat Admin 配置
- `config/database.php` - 数据库配置
- `config/app.php` - 应用基础配置

## 开发指南

### 添加新节点

1. 通过管理面板添加节点
2. 配置节点的地理位置和连接信息
3. 分配节点给相应的用户角色

### 自定义订阅格式

修改 `app/Services/SubscriptionService.php` 中的相关方法来自定义订阅内容格式。

## 安全说明

- 所有 API 接口都使用 token 验证
- 管理面板基于 Laravel 的用户认证系统
- 订阅链接包含用户特定的加密 token

## 许可证

本项目基于 [MIT 许可证](https://opensource.org/licenses/MIT) 开源。
