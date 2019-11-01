<?php
// +----------------------------------------------------------------------
// | JDCMS V2.0
// +----------------------------------------------------------------------
// | Author: WANGQI <email:wq@ji-du.com.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Config as ConfigModel;
use app\admin\model\Link;
use app\admin\model\Banner;

class Config extends Base
{
	// seo设置
	public function seo()
	{
		if (input('sub')) {
			$data = request()->post();
	        foreach ($data as $key => $val) {
	            ConfigModel::where('tag', $key)->update(['val'=>$val]);
	        }
	        $this->success(OK, 'seo');
		}
		$list = ConfigModel::all(['type'=>1]);
		$this->assign('list', $list);
		return $this->fetch();
	}

	// 基础设置
	public function basic()
	{
		if (input('sub')) {
			$data = request()->post();
	        foreach ($data as $key => $val) {
	            ConfigModel::where('tag', $key)->update(['val'=>$val]);
	        }

	        $file = request()->file();
	        if (isset($file['site_logo'])) {
	        	$info = $file['site_logo']->move(ROOT_PATH.'public'.DS.'uploads');
				if (!$info) {
					$this->error('图片传输有误');
				}
				$img = '/uploads/'.date('Ymd').'/'.$info->getFilename();
				ConfigModel::where('tag', 'site_logo')->update(['val'=>$img]);
	        }
	        if (isset($file['site_ico'])) {
	        	$info = $file['site_ico']->move(ROOT_PATH.'public'.DS.'uploads');
	        	if (!$info) {
	        		$this->error('图片传输有误');
	        	}
	        	$img = '/uploads/'.date('Ymd').'/'.$info->getFilename();
	        	ConfigModel::where('tag', 'site_ico')->update(['val'=>$img]);
	        }
	        if (isset($file['wap_logo'])) {
	        	$info = $file['wap_logo']->move(ROOT_PATH.'public'.DS.'uploads');
	        	if (!$info) {
	        		$this->error('图片传输有误');
	        	}
	        	$img = '/uploads/'.date('Ymd').'/'.$info->getFilename();
	        	ConfigModel::where('tag', 'wap_logo')->update(['val'=>$img]);
	        }

	        $this->success(OK, 'basic');
		}
		$list = ConfigModel::all(['type'=>['in', '2,3']]);
		$this->assign('list', $list);
		return $this->fetch();
	}

	// 友情链接
	public function link()
	{
		$list = Link::all(function($query){
			$query->order('rank asc, id asc');
		});
		$this->assign('list', $list);
		return $this->fetch();
	}

	// 友情链接 - 添加
	public function linkAdd()
	{
		if (input('sub')) {
			$data = request()->post();

			$file = request()->file('img');
			if (isset($file)) {
				$info = $file->move(ROOT_PATH.'public'.DS.'uploads');
				if (!$info) {
					$this->error('图片传输有误');
				}
				$data['img'] = '/uploads/'.date('Ymd').'/'.$info->getFilename();
			}

			$link_model = new Link;
			$res = $link_model->allowField(true)->save($data);

			$res ? $this->success(OK, 'link') : $this->error(FAIL);
		}

		return $this->fetch();
	}

	// 友情链接 - 编辑
	public function linkEdit($id)
	{
		if (input('sub')) {
			$data = request()->post();

			$file = request()->file('img');
			if (isset($file)) {
				$info = $file->move(ROOT_PATH.'public'.DS.'uploads');
				if (!$info) {
					$this->error('图片传输有误');
				}
				$data['img'] = '/uploads/'.date('Ymd').'/'.$info->getFilename();
			}

			$link_model = new Link;
			$res = $link_model->allowField(true)->save($data, ['id'=>$id]);

			$res ? $this->success(OK, 'link') : $this->error(FAIL);
		}

		$res = Link::get($id);
		$this->assign('res', $res);

		return $this->fetch();
	}

	// 友情链接 - 删除
	public function linkDel($id)
	{
		$res = link::destroy(['id'=>['in', $id]]);
		return $res ? ['status'=>1, 'msg'=>OK] : ['status'=>0, 'msg'=>FAIL];
	}


	// 轮播管理
	public function banner()
	{
		$list = Banner::all(function($e){
			$e->order('rank asc');
		});
		$this->assign('list', $list);
		return $this->fetch();
	}

	// 轮播管理 - 添加
	public function bannerAdd()
	{
		if (input('sub')) {
			$data = request()->post();
			$file = request()->file('img');

			if (!isset($file)) {
				$this->error('图片不能为空');
			}
			$info = $file->move(ROOT_PATH.'public'.DS.'uploads');
			if (!$info) {
				$this->error('图片传输有误');
			}
			$data['img'] = '/uploads/'.date('Ymd').'/'.$info->getFilename();

			$banner_model = new Banner;
			$res = $banner_model->allowField(true)->save($data);

			$res ? $this->success(OK, 'banner') : $this->error(FAIL);
		}
		return $this->fetch();
	}

	// 轮播管理 - 编辑
	public function bannerEdit($id)
	{
		if (input('sub')) {
			$data = request()->post();
			$file = request()->file('img');

			if (isset($file)) {
				$info = $file->move(ROOT_PATH.'public'.DS.'uploads');
				if (!$info) {
					$this->error('图片传输有误');
				}
				$data['img'] = '/uploads/'.date('Ymd').'/'.$info->getFilename();
			}
			$banner_model = new Banner;
			$res = $banner_model->allowField(true)->save($data, ['id'=>$id]);

			$res ? $this->success(OK, 'banner') : $this->error(FAIL);
		}

		$res = Banner::get($id);
		$this->assign('res', $res);

		return $this->fetch();
	}

	// 轮播管理 - 删除
	public function bannerDel($id)
	{
		$res = Banner::destroy(['id'=>['in', $id]]);
		return $res ? ['status'=>1, 'msg'=>OK] : ['status'=>0, 'msg'=>FAIL];
	}


	// 站点地图
	public function sitemap()
	{
		return $this->fetch();
	}
}