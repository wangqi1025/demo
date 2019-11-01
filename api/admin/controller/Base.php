<?php
// +----------------------------------------------------------------------
// | JDCMS V2.0
// +----------------------------------------------------------------------
// | Author: WANGQI <email:wq@ji-du.com.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use think\Controller;

class Base extends Controller
{
	public function _initialize()
    {
        $controller = request()->controller();
        $action =  request()->action();

        if (session('admin')) {
        	$admin = Db::name('admin')->where('id',session('admin.id'))->find();
        	$this->assign('admin',$admin);

            $nav = Db::name('admin_menu')->where('pid', 0)->where('is_show', 1)->order('rank')->select();
            foreach ($nav as $k => $v) {
                $child = Db::name('admin_menu')->where('pid', $v['id'])->where('is_show', 1)->order('rank')->select();
                $nav[$k]['child'] = $child;
            }
            $this->assign('nav', $nav);
            
            if ($admin['role_id'] != 1) {
                if (!$this->getPower($controller, $action)) {
                    $this->error('没有操作权限');
                }
            }
        } else {
			$this->redirect('admin/Login/login');
		}
    }

    public function getPower($controller, $action)
    {
        $id = session('admin.id');
        $admin_menu = Db::name('admin')->alias('a')->join('admin_role b', 'a.role_id=b.id', 'left')->where('a.id', $id)->field('b.nav_list')->find();
        if (!$admin_menu['nav_list']) {
            return false;
        }
        else {
            $arr = explode(',', $admin_menu['nav_list']);
            $now_menu_id = Db::name('admin_menu')->where(['c'=>$controller, 'a'=>$action])->value('id');
            if ($now_menu_id) {
                if (in_array($now_menu_id, $arr)) {
                    return true;
                } else {
                    return false;
                }  
            } else {    // 一些公共的操作方法
                return true;
            }
            
        }
    }
}