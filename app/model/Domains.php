<?php
declare (strict_types = 1);

namespace app\model;

use app\BaseModel;

/**
 * @mixin \think\Model
 */
class Domains extends BaseModel
{
    public function sites()
    {
        return $this->belongsTo(Sites::class, 'site_id', 'id');
    }

}
