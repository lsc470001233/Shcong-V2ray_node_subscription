<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\AdminRole;

class V2rayNode extends Model
{
	use HasDateTimeFormatter;
    
    protected $table = 'v2ray_nodes';
    
    protected $fillable = [
        'machine_name',
        'machine_ip', 
        'machine_port',
        'country',
        'city',
        'node_uri',
        'latency',
        'speed',
        'status',
        'remark'
    ];
    
    /**
     * V2ray节点关联的管理员角色
     *
     * @return BelongsToMany
     */
    public function adminRoles(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminRole::class,
            'v2ray_node_roles',
            'v2ray_node_id',
            'admin_role_id'
        )->withTimestamps();
    }
}
