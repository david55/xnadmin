<?php
// +----------------------------------------------------------------------
// | 小牛Admin
// +----------------------------------------------------------------------
// | Website: www.xnadmin.cn
// +----------------------------------------------------------------------
// | Author: dav <85168163@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Test extends AdminBase
{
    public function index()
    {
        return view();
    }

    public function add()
    {
        if( $this->request->isPost() ) {
            $this->success('添加成功');
        }
        return view();
    }

    public function delete()
    {
        $this->success('删除成功');
    }
}
