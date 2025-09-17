<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 检查菜单是否已存在，避免重复插入
        $existingMenu = DB::table('admin_menu')
            ->where('uri', 'v2ray-nodes')
            ->first();

        if (!$existingMenu) {
            $menus = [
                [
                    'parent_id' => 2,
                    'title' => '节点',
                    'icon' => 'fa-linode',
                    'uri' => 'v2ray-nodes',
                    'extension' => '',
                    'show' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // 如果需要，可以添加更多V2ray相关菜单项
                // [
                //     'parent_id' => 2,
                //     'title' => '用户管理',
                //     'icon' => 'fa-users',
                //     'uri' => 'admin-users',
                //     'extension' => '',
                //     'show' => 1,
                //     'created_at' => now(),
                //     'updated_at' => now(),
                // ],
            ];

            foreach ($menus as $menu) {
                DB::table('admin_menu')->insert($menu);
            }

            $this->command->info('Admin menu seeded successfully!');
        } else {
            $this->command->warn('V2ray节点菜单已存在，跳过插入。');
        }
    }
}
