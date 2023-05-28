<?php
namespace app\controller;

use app\AdminController;

class Index extends AdminController
{
    public function index()
    {
        return $this->view('index');
    }
}
