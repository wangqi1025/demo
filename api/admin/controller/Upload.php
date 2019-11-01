<?php
// +----------------------------------------------------------------------
// | JDCMS V2.0
// +----------------------------------------------------------------------
// | 上传类
// +----------------------------------------------------------------------
// | Author: WANGQI <email:wq@ji-du.com.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Config;
use common\Dir;

class Upload extends Base
{
    // 单图片上传
    public function imgSingle()
    {
        $config_model = new Config();
        $ext  = $config_model->getValue('upload_image_ext');
        $size = $config_model->getValue('upload_image_size');

        $file = request()->file('img');
        if (!isset($file)) {
            return ['status'=>-1, 'msg'=>'请选择上传的文件'];
        }

        $info = $file->validate(['ext'=>$ext, 'size'=>$size*1024])->move(ROOT_PATH.'public'.DS.'uploads');
        if (!$info) {
            return ['status'=>-1, 'msg'=>'图片格式只能是'.$ext.'，图片大小不超过'.$size.'KB'];
        }
        
        $img = '/uploads/'.date('Ymd').'/'.$info->getFilename();    // 图片路径
        return ['status'=>1, 'msg'=>OK, 'data'=>$img];
    }

    // 图片文件 管理
    public function fileMag($spath='')
    {
        $spath = input('spath');

	    $base_path = '/uploads';
	    $encodeflag = input('encodeflag', 0, 'intval');
	    if ($encodeflag) {
	      	$spath = base64_decode($spath);
	    }
	    $spath = str_replace('.', '', ltrim($spath,$base_path));
	    $path = $base_path . '/'. $spath;

	    $dirlist = new Dir('.'.$path);
        $list = $dirlist->toArray();

	    for ($i=0; $i < count($list); $i++) { 
	      	$list[$i]['isImg'] = 0;
	      	if ($list[$i]['isFile']) {
		        $url = rtrim($path,'/') . '/'. $list[$i]['filename'];
		        $ext = explode('.', $list[$i]['filename']);
		        $ext = end($ext);
		        if (in_array($ext, explode(',', 'rar,zip,pdf,doc,docx,xls,xlsx,ttf'))) {
		          	$list[$i]['isImg'] = 1;
		        }
	      	} else {
	        	$url = url('upload/fileMag', array(
                    'spath'     => base64_encode(rtrim($path,'/') . '/'. $list[$i]['filename']),
                    'encodeflag'=> 1 )
                );
	      	} 
	      	$list[$i]['url'] = $url;      
	      	$list[$i]['size'] = get_byte($list[$i]['size']);
        }

	    $last_names = array_column($list, 'filename');
        array_multisort($last_names, SORT_DESC, $list);

	    $parentpath = substr($path, 0, strrpos($path, '/'));
        
        $data = [
            'purl'=>  url('upload/fileMag', array('spath'=> base64_encode($parentpath),'encodeflag' => 1)),
            'infolist'=> $list,
        ];

        return ['status'=>1, 'msg'=>OK, 'data'=>$data];
    }
}