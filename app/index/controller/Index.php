<?php
namespace app\index\controller;

use app\common\controller\Base;

class Index extends Base
{
    public function index()
    {
        $this->redirect('admin/login/index');
    }
}