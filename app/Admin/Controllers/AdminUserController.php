<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Auth\Permission;
use App\Models\AdminUser;
use Dcat\Admin\Show;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Tree;

class AdminUserController extends AdminController
{
    public function title()
    {
        return trans('admin.administrator');
    }


    protected function grid()
    {
        return Grid::make(AdminUser::with(['roles']), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('username');
            $grid->column('name');

            if (config('admin.permission.enable')) {
                $grid->column('roles','节点组')->pluck('name')->label('primary', 3);

                $permissionModel = config('admin.database.permissions_model');
                $roleModel = config('admin.database.roles_model');
                $nodes = (new $permissionModel())->allNodes();
                $grid->column('permissions')
                    ->if(function () {
                        return ! $this->roles->isEmpty();
                    })
                    ->showTreeInDialog(function (Grid\Displayers\DialogTree $tree) use (&$nodes, $roleModel) {
                        $tree->nodes($nodes);

                        foreach (array_column($this->roles->toArray(), 'slug') as $slug) {
                            if ($roleModel::isAdministrator($slug)) {
                                $tree->checkAll();
                            }
                        }
                    })
                    ->else()
                    ->display('');
            }

            $grid->column('status')->switch();
            $grid->quickSearch(['id', 'name', 'username']);

            // $grid->showQuickEditButton();
            // $grid->enableDialogCreate();
            $grid->showColumnSelector();
            // $grid->disableEditButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($actions->getKey() == AdminUser::DEFAULT_ID) {
                    $actions->disableDelete();
                }
            });
        });
    }

    protected function detail($id)
    {
        return Show::make($id, AdminUser::with(['roles']), function (Show $show) {
            $show->field('id');
            $show->field('username');
            $show->field('name');

            $show->field('avatar', __('admin.avatar'))->image();

            if (config('admin.permission.enable')) {
                $show->field('roles')->as(function ($roles) {
                    if (! $roles) {
                        return;
                    }

                    return collect($roles)->pluck('name');
                })->label();

                $show->field('permissions')->unescape()->as(function () {
                    $roles = $this->roles->toArray();

                    $permissionModel = config('admin.database.permissions_model');
                    $roleModel = config('admin.database.roles_model');
                    $permissionModel = new $permissionModel();
                    $nodes = $permissionModel->allNodes();

                    $tree = Tree::make($nodes);

                    $isAdministrator = false;
                    foreach (array_column($roles, 'slug') as $slug) {
                        if ($roleModel::isAdministrator($slug)) {
                            $tree->checkAll();
                            $isAdministrator = true;
                        }
                    }

                    if (! $isAdministrator) {
                        $keyName = $permissionModel->getKeyName();
                        $tree->check(
                            $roleModel::getPermissionId(array_column($roles, $keyName))->flatten()
                        );
                    }

                    return $tree->render();
                });
            }

            $show->field('name');
            $show->field('api_token');
            $show->field('status')->display(function ($value) {
                return $value ? '启用' : '禁用';
            });
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    public function form()
    {
        return Form::make(AdminUser::with(['roles']), function (Form $form) {
            $userTable = config('admin.database.users_table');

            $connection = config('admin.database.connection');

            $id = $form->getKey();

            $form->display('id', 'ID');

            $form->text('username', trans('admin.username'))
                ->required()
                ->creationRules(['required', "unique:{$connection}.{$userTable}"])
                ->updateRules(['required', "unique:{$connection}.{$userTable},username,$id"]);
            $form->text('name', trans('admin.name'))->required();
            $form->image('avatar', trans('admin.avatar'))->autoUpload();

            if ($id) {
                $form->password('password', trans('admin.password'))
                    ->minLength(5)
                    ->maxLength(20)
                    ->customFormat(function () {
                        return '';
                    });
            } else {
                $form->password('password', trans('admin.password'))
                    ->required()
                    ->minLength(5)
                    ->maxLength(20);
            }

            $form->password('password_confirmation', trans('admin.password_confirmation'))->same('password');

            $form->ignore(['password_confirmation']);

            if (config('admin.permission.enable')) {
                $form->multipleSelect('roles', trans('admin.roles'))
                    ->options(function () {
                        $roleModel = config('admin.database.roles_model');

                        return $roleModel::all()->pluck('name', 'id');
                    })
                    ->customFormat(function ($v) {
                        return array_column($v, 'id');
                    });
            }

            // API Token字段
            $form->text('api_token', 'API Token')
                ->required()
                ->help('API访问令牌，用于接口认证')
                ->append('<button type="button" class="btn btn-sm btn-primary" onclick="refreshApiToken()" style="margin-left: 5px;">
                    <i class="feather icon-refresh-cw"></i> 刷新
                </button>');
            
            // 添加JavaScript处理刷新按钮
            $form->html('<script>
                // 生成UUID函数
                function generateUUID() {
                    return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function(c) {
                        var r = Math.random() * 16 | 0, v = c == "x" ? r : (r & 0x3 | 0x8);
                        return v.toString(16);
                    });
                }
                
                // 刷新API Token函数
                function refreshApiToken() {
                    var newToken = generateUUID();
                    var tokenInput = document.querySelector("input[name=\"api_token\"]");
                    if (tokenInput) {
                        tokenInput.value = newToken;
                        console.log("生成新的API Token:", newToken);
                        if (typeof Dcat !== "undefined") {
                            Dcat.success("API Token已刷新");
                        } else {
                            alert("API Token已刷新: " + newToken);
                        }
                    } else {
                        console.error("找不到api_token输入框");
                    }
                }
                
                // 页面加载完成后检查是否需要自动生成
                $(document).ready(function() {
                    // 延迟执行，确保DOM完全加载
                    setTimeout(function() {
                        var tokenInput = document.querySelector("input[name=\"api_token\"]");
                        var isCreatePage = window.location.href.indexOf("/create") > -1;
                        
                        console.log("页面检查 - 是否创建页面:", isCreatePage);
                        console.log("当前Token值:", tokenInput ? tokenInput.value : "输入框未找到");
                        
                        if (isCreatePage && tokenInput && !tokenInput.value.trim()) {
                            refreshApiToken();
                            console.log("自动生成了API Token");
                        }
                    }, 500);
                });
            </script>');
            $form->switch('status')->default(true);

            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));

            if ($id == AdminUser::DEFAULT_ID) {
                $form->disableDeleteButton();
            }
        })->saving(function (Form $form) {
            // 密码处理
            if ($form->password && $form->model()->get('password') != $form->password) {
                $form->password = bcrypt($form->password);
            }

            if (! $form->password) {
                $form->deleteInput('password');
            }
        });
    }

    public function destroy($id)
    {
        if (in_array(AdminUser::DEFAULT_ID, Helper::array($id))) {
            Permission::error();
        }

        return parent::destroy($id);
    }
}
