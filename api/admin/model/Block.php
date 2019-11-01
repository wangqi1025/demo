<?php
// +----------------------------------------------------------------------
// | JDCMS V1.2
// +----------------------------------------------------------------------
// | Author: WANGQI <email:wq@ji-du.com.cn>
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\Model;

class Block extends Model
{
	protected $name = 'block';

	public function getPageList($where, $page_num)
	{
		return $this->where($where)->order('id desc')->paginate($page_num);
	}
}