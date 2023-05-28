<?php
declare (strict_types=1);

namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * 控制器基础类
 */
abstract class BaseModel extends Model
{
    /**
     * 获取分页数据
     *
     * @param mixed $field
     * @param array $where
     * @param int   $limit
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public static function getPageList(array $where = [], $field = true, int $limit = 20)
    {
        return static::field($field)
            ->where($where)
            ->order('id', 'desc')
            ->paginate($limit);
    }

    /**
     * 获取数据列表
     * @param $where
     * @param string $orderBy
     * @param mixed $field
     * @return BaseModel[]|array|\think\Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getList($where, string $orderBy = 'id desc', $field = true)
    {
        return static::field($field)->where($where)->order($orderBy)->select();
    }

    /**
     * 获取一条数据
     *
     * @param array $where
     * @param mixed $field
     * @return array|Model
     */
    public static function getRow(array $where, $field = true)
    {
        return static::field($field)->where($where)->findOrEmpty();
    }

}
