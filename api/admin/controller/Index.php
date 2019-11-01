<?php
// +----------------------------------------------------------------------
// | JDCMS V2.0
// +----------------------------------------------------------------------
// | Author: WANGQI <email:wq@ji-du.com.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Content;
use app\admin\model\Category;
use app\admin\model\Browse;

class Index extends Base
{

	public function index()
	{
        $content_num = Content::count();
        $category_num = Category::count();

        $browse_model = new Browse();
		$browse = [];
		for ($i = 6; $i >= 0; $i --) { 
            $start_time = strtotime(date('Y-m-d 00:00:00', strtotime("-".$i." day")));
            $end_time   = strtotime(date('Y-m-d 23:59:59', strtotime("-".$i." day")));
            $browse[] = Browse::where('time', 'between time', [$start_time, $end_time])->count();
        }

        $data = [
        	'content_num' 	=> $content_num,
        	'category_num'	=> $category_num,
        	'browse'		=> $browse
        ];

        $this->assign('data', $data);

		return $this->fetch();
	}
}