<?php
// +----------------------------------------------------------------------
// | JDCMS V1.2
// +----------------------------------------------------------------------
// | Author: WANGQI <email:wq@ji-du.com.cn>
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\Model;

class BlockCat extends Model
{
	protected $name = 'block_cat';

	public function blocks()
	{
		return $this->hasMany('Block');
	}

	
}