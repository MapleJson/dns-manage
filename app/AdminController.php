<?php
declare (strict_types=1);

namespace app;

class AdminController extends BaseController
{
    /**
     * @param string $template
     * @param array  $vars
     * @param int    $code
     * @param null   $filter
     * @return \think\response\View
     */
    protected function view(string $template = '', $vars = [], $code = 200, $filter = null): \think\response\View
    {
        $vars['nav'] = config('menu');
        return view($template, $vars, $code, $filter);
    }

    /**
     * 保存新建的资源
     *
     * @return \think\Response
     */
    public function save()
    {
        $result = $this->model->create(input('post'));
        if ($result->id) {
            return message('Submitted successfully', false);
        }
        return message('Submission Failed');
    }

    /**
     * 保存更新的资源
     *
     * @return \think\Response
     */
    public function update()
    {
        $data = $this->model->getById(intval(input('post.id')));
        if (!$data->isExists()) {
            return message('The data does not exist');
        }

        $result = $this->model->update(input('post'), ['id' => intval(input('post.id'))]);
        if ($result) {
            return message('Edit successfully', false);
        }
        return message('Edit failed');
    }

    /**
     * 删除选中的资源
     * @return mixed
     */
    public function delete()
    {
        if ($this->model->where('id', intval(input('get.id')))->delete()) {
            return message('Delete successfully', false);
        }
        return message('Delete failed');
    }

}