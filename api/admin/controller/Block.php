<?php
// +----------------------------------------------------------------------
// | JDCMS V2.0
// +----------------------------------------------------------------------
// | Author: WANGQI <email:wq@ji-du.com.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Block as BlockModel;
use app\admin\model\BlockCat;

class Block extends Base
{
	// 自定义块分类
	public function cat()
	{
		$list = BlockCat::all(function($e){
			$e->order('rank asc');
		});

		foreach($list as $key => $val) {
			$list[$key]['num'] = BlockModel::where('cid', $val['id'])->count();
		}

		$this->assign('list', $list);
		
		return $this->fetch();
	}

	// 自定义块分类 - 添加
	public function catAdd()
	{
		if (input('sub')) {
			$block_cat_model = new BlockCat;
			$res = $block_cat_model->allowField(true)->save(input());
			$res ? $this->success(OK, 'cat') : $this->error(FAIL);
		}
		return $this->fetch();
	}

	// 自定义块分类 - 编辑
	public function catEdit($id)
	{
		if (input('sub')) {
			$block_cat_model = new BlockCat;
			$res = $block_cat_model->allowField(true)->save(input(), ['id'=>$id]);
			$res ? $this->success(OK, 'cat') : $this->error(FAIL);
		}
		$res = BlockCat::get($id);
		$this->assign('res', $res);
		return $this->fetch();
	}

	// 自定义分类 - 删除
	public function catDel($id)
	{
		$res = BlockCat::destroy($id);
		return $res ? ['status'=>1, 'msg'=>OK] : ['status'=>0, 'msg'=>FAIL];
	}


	// 自定义块 - 管理
	public function index()
	{
		if (input('cat')) {
			$where = ['cid'=>input('cat')];
		} else {
			$where = ['cid'=>0];
		}
		$block_model = new BlockModel;
		$list = $block_model->getPageList($where, 10);
		$this->assign('list', $list);

		return $this->fetch();
	}

	// 自定义块 - 添加
	public function add()
	{
		if (input('sub')) {
			$data = request()->post();

			switch ($data['type']) {
				case 1:		// 文本
					if ($data['content1'] == '') {
						$this->error('内容不能为空');
					}
					$data['content'] = $data['content1'];
					break;
				case 2:		// 图片
					if ($data['content2'] == '') {
						$this->error('内容不能为空');
					}
					$data['content'] = $data['content2'];
					break;
				case 3:		// 富文本
					if (!isset($data['editorValue'])) {
						$this->error('内容不能为空');
					}
					$data['content'] = $data['editorValue'];
					break;
				default:
					$this->error('参数有误');
					break;
			}

			$block_model = new BlockModel();
			$res = $block_model->allowField(true)->save($data);

			$res ? $this->success(OK, url('index', ['cat'=>input('cat')])) : $this->error(FAIL);
		}

		$cat = BlockCat::all(function($e){
			$e->order('rank asc');
		});
		$this->assign('cat', $cat);

		return $this->fetch();
	}

	// 自定义块 - 编辑
	public function edit($id)
	{
		if (input('sub')) {
			$data = request()->post();

			switch ($data['type']) {
				case 1:		// 文本
					if ($data['content1'] == '') {
						$this->error('内容不能为空');
					}
					$data['content'] = $data['content1'];
					break;
				case 2:		// 图片
					if ($data['content2'] == '') {
						$this->error('内容不能为空');
					}
					$data['content'] = $data['content2'];
					break;
				case 3:		// 富文本
					if (!isset($data['editorValue'])) {
						$this->error('内容不能为空');
					}
					$data['content'] = $data['editorValue'];
					break;
				default:
					$this->error('参数有误');
					break;
			}

			$block_model = new BlockModel();
			$res = $block_model->allowField(true)->save($data , ['id'=>$id]);

			$res ? $this->success(OK, url('index', ['id'=>$id, 'cat'=>input('cid')])) : $this->error(FAIL);
		}

		$res = BlockModel::get($id);
		$this->assign('res', $res);

		$cat = BlockCat::all(function($e){
			$e->order('rank asc');
		});
		$this->assign('cat', $cat);

		return $this->fetch();
	}

	// 自定义块 - 删除
	public function del($id)
	{
		$res = BlockModel::destroy($id);
		return $res ?['status'=>1, 'msg'=>OK] : ['status'=>0, 'msg'=>FAIL];
	}

}