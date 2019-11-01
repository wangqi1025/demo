<?php
// +----------------------------------------------------------------------
// | JDCMS V1.2
// +----------------------------------------------------------------------
// | Author: WANGQI <email:wq@ji-du.com.cn>
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\Model;

class Config extends Model
{
	protected $name = 'config';

	public function getValue($tag)
	{
		return $this->where('tag', $tag)->value('val');
	}
}