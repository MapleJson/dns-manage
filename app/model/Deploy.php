<?php
declare (strict_types = 1);

namespace app\model;

use app\BaseModel;

/**
 * @mixin \think\Model
 */
class Deploy extends BaseModel
{
    public function sites()
    {
        return $this->belongsTo(Sites::class, 'site_id');
    }

    public function servers()
    {
        return $this->belongsTo(Servers::class, 'server_id');
    }

}
