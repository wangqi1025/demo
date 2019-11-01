<?php
// +----------------------------------------------------------------------
// | JDCMS V2.0
// +----------------------------------------------------------------------
// | Author: WANGQI <email:wq@ji-du.com.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Menu as MenuModel;

class Menu extends Base
{
	// 菜单列表
	public function index()
	{
		$cate = MenuModel::all(function($query){
			$query->order('rank asc, id asc');
		});
		$arr  = $this->rule($cate);
		$this->assign('list', $arr);

		return $this->fetch();
	}

	// 添加菜单
	public function add()
	{
		if (input('sub')) {
			$menu_model = new MenuModel;
			$res = $menu_model->allowField(true)->save(input());
			if ($res) {
				$this->success(OK, 'index');
			} else {
				$this->error(FAIL);
			}
		}
		
		$cate = MenuModel::all(function($query){
			$query->order('rank asc, id asc');
		});
		$arr  = $this->rule($cate);
		$this->assign('list', $arr);

		return $this->fetch();
	}

	// 编辑菜单
	public function edit($id)
	{
		if (input('sub')) {
			switch (input('type')) {
				case 'css':		// sub, id, type, value
					$res = MenuModel::where('id', $id)->update(['css'=>input('value')]);
					return $res ? ['status'=>1, 'msg'=>'OK'] : ['status'=>0, 'msg'=>FAIL];
					break;
				case 'is_show':
					$res = MenuModel::where('id', $id)->update(['is_show'=>input('value')]);
					return $res ? ['status'=>1, 'msg'=>'OK'] : ['status'=>0, 'msg'=>FAIL];
					break;
				
				default:
					$menu_model = new MenuModel();
					$res = $menu_model->allowField(true)->save(input(), ['id'=>$id]);
					$res ? $this->success(OK, 'index') : $this->error(FAIL);
					break;
			}
		}

		$cate = MenuModel::all(function($query){
			$query->order('rank asc, id asc');
		});
		$arr  = $this->rule($cate);
		$this->assign('list', $arr);
		
		$res = MenuModel::get($id);
		$this->assign('res', $res);

		return $this->fetch();
	}

	// 删除菜单
	public function del($id)
	{
		$res  = MenuModel::destroy($id);
		return $res ? ['status'=>1, 'msg'=>OK] : ['status'=>0, 'msg'=>FAIL];
	}

	// 菜单样式
	static public function rule($cate ,$lefthtml = '— — ', $pid=0 ,$lvl=0, $leftpin=0 )
	{
		$arr = array();
		foreach ($cate as $v){
			if($v['pid'] == $pid){
				$v['lvl'] = $lvl + 1;
				$v['leftpin'] = $leftpin + $lvl*20;//左边距
				$v['lefthtml'] = str_repeat($lefthtml,$lvl);
				$v['leftkong'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',$lvl);
				$arr[] = $v;
				$arr = array_merge($arr,self::rule($cate,$lefthtml,$v['id'] ,$lvl+1, $leftpin+15));
			}
		}
		return $arr;
	}
}