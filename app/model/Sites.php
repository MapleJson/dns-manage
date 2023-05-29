<?php
declare (strict_types = 1);

namespace app\model;

use app\BaseModel;

/**
 * @mixin \think\Model
 */
class Sites extends BaseModel
{
    public function servers()
    {
        return $this->belongsTo(Servers::class, 'server_id', 'id');
    }

    public function frontServers()
    {
        return $this->belongsTo(Servers::class, 'front_server_id', 'id');
    }

    public function domains()
    {
        return $this->belongsTo(Domains::class, 'a_domain_id', 'id');
    }

    public function records()
    {
        return $this->belongsTo(Records::class, 'a_record_id', 'id');
    }


}
