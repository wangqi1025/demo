<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {


        $arr = [
            [
                'id'    => 1,
                'name'  => 'aaa'
            ],
            [
                'id'    => 2,
                'name'  => 'bbb'
            ],
            [
                'id'    => 3,
                'name'  => 'ccc'
            ],
            [
                'id'    => 4,
                'name'  => 'ddd'
            ],
        ];

        $this->assign('arr', $arr);

        return $this->fetch();
    }
}
