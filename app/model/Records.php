<?php
declare (strict_types = 1);

namespace app\model;

use app\BaseModel;

/**
 * @mixin \think\Model
 */
class Records extends BaseModel
{
    public function domains()
    {
        return $this->belongsTo(Domains::class, 'domain_id', 'id');
    }

    public function sites()
    {
        return $this->belongsTo(Sites::class, 'site_id', 'id');
    }
}
