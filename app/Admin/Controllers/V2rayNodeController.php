<?php

namespace App\Admin\Controllers;

use App\Models\V2rayNode;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\AdminRole;
use Illuminate\Support\Str;

class V2rayNodeController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(V2rayNode::with('adminRoles'), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('服务器信息')->display(function () {
                $machineName = $this->machine_name ?? "";
                $machineIp = $this->machine_ip ?? "";
                $machinePort = $this->machine_port ?? "";
                $country = $this->country ?? "";
                $city = $this->city ?? "";

                return <<<HTML
                <div>名称：{$machineName}</div>
                <div>IP：{$machineIp}</div>
                <div>端口：{$machinePort}</div>
                <div>位置：{$country} {$city}</div>
                HTML;
            });
            $grid->column('node_uri')->display(function () {
                $nodeUri = $this->node_uri ?? "";
                $latency = $this->latency ?? "";
                $speed = $this->speed ?? "";

                // 只显示前20字符，后面加“...”
                $shortUri = Str::limit($nodeUri, 50);
                return <<<HTML
                <div style="word-break: break-all;">{$shortUri}</div>
                <div>延迟：{$latency}</div>
                <div>速度：{$speed}</div>
                HTML;
            });

            $grid->column('status')->switch();
            $grid->column('关联角色')->display(function () {
                return $this->adminRoles->map(function ($role) {
                    $statusText = $role->status ? '<span style="color: green;">启用</span>' : '<span style="color: red;">禁用</span>';
                    return $role->name . ' - [' . $statusText . ']';
                })->implode('<br>');
            });
            $grid->column('remark')->limit(50);

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('country');
                $filter->equal('city');
                $filter->switch('status')->option(
                    [
                        1 => '启用',
                        0 => '禁用',
                    ]
                );
                // $filter->whereHas('adminRoles', '关联角色')->multipleSelect(AdminRole::all()->pluck('name', 'id'));
                $filter->like('remark');
                $filter->date('created_at');
                $filter->date('updated_at');
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, V2rayNode::with('adminRoles'), function (Show $show) {
            $show->field('id');
            $show->field('machine_name', '服务器名称');
            $show->field('machine_ip', 'IP地址');
            $show->field('machine_port', '端口');
            $show->field('country', '国家');
            $show->field('city', '城市');
            $show->field('node_uri', '节点URI');
            $show->field('latency');
            $show->field('speed');
            $show->field('status');
            $show->field('adminRoles', '关联角色')->as(function ($adminRoles) {
                return $adminRoles->map(function ($role) {
                    $statusText = $role->status ? '启用' : '禁用';
                    return $role->name . ' - [' . $statusText . ']';
                })->implode(', ');
            });
            $show->field('remark');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(V2rayNode::with('adminRoles'), function (Form $form) {
            $form->display('id');
            $form->text('machine_name', '服务器名称');
            $form->text('machine_ip', 'IP地址');
            $form->text('machine_port', '端口');
            $form->text('country', '国家');
            $form->text('city', '城市');
            $form->textarea('node_uri', '节点URI');
            $form->display('latency');
            $form->display('speed');
            $form->switch('status')->default(true);
            $form->textarea('remark');

            // 管理员角色多选 - 多对多关系
            $form->multipleSelect('adminRoles', '关联角色')
                ->options(
                    AdminRole::query()
                        ->select('id', 'name', 'status')
                        ->get()
                        ->mapWithKeys(function ($role) {
                            $statusText = $role->status ? '启用' : '禁用';
                            return [$role->id => $role->name . ' - [' . $statusText . ']'];
                        })->toArray()
                )
                ->customFormat(function ($v) {
                    if (!$v) return [];
                    // 如果是Collection，提取ID数组
                    if (is_object($v) && method_exists($v, 'pluck')) {
                        return $v->pluck('id')->toArray();
                    }
                    return array_column($v, 'id');
                });

            $form->display('created_at');
            $form->display('updated_at');
        })->saved(function (Form $form, $result) {
            // 保存后同步多对多关系
            $model = $form->model();
            $adminRoles = $form->adminRoles;

            // 确保 adminRoles 是有效的数组
            if ($adminRoles !== null) {
                // 过滤掉空值
                $adminRoles = array_filter((array) $adminRoles, function ($value) {
                    return !empty($value) && is_numeric($value);
                });

                $model->adminRoles()->sync($adminRoles);
            } else {
                // 如果没有选择任何角色，清空关联关系
                $model->adminRoles()->sync([]);
            }

            return $result;
        });
    }
}
