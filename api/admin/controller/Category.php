<?php
// +----------------------------------------------------------------------
// | JDCMS V2.0
// +----------------------------------------------------------------------
// | Author: WANGQI <email:wq@ji-du.com.cn>
// +----------------------------------------------------------------------
// | 栏目管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Category as CategoryModel;
// use app\admin\model\

class Category extends Base
{
    // 栏目管理 - 首页
    public function index()
    {
        return $this->fetch();
    }

    // 栏目管理 - 添加
    public function add()
    {
        $cat = CategoryModel::all(function ($e) {
            $e->field('id, pid, title')->order('rank asc');
        });
        $cat = action('Menu/rule', ['cate'=>$cat]);
        $this->assign('list', $cat);
 
        // $model = 

        return  $this->fetch();
    }
}