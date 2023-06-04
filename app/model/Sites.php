<?php
declare (strict_types = 1);

namespace app\model;

use app\BaseModel;

/**
 * @mixin \think\Model
 */
class Sites extends BaseModel
{
    public function domains()
    {
        return $this->belongsTo(Domains::class, 'a_domain_id');
    }

    public function webDomains()
    {
        return $this->hasMany(Domains::class, 'site_id');
    }

    public function dns()
    {
        return $this->hasMany(Records::class, 'site_id');
    }

}
