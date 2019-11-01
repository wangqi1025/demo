<?php
namespace app\index\taglib;

use think\template\TagLib;
use think\Db;

class JiDu  extends TagLib
{
    protected $tab = [

        // 自定义块
        'block'	=> [
            'attr'	=> 'name, textflag',
			'close'	=> 0,
        ]
    ];


    public function tagBlock($attr) 
    {
		$name = isset($attr['name']) ? $attr['name'] : '';
		$textflag = empty($attr['textflag']) ? 0 : 1;
		$name = trim($name);
		$str =<<<str
<?php
		\$_block = Db::('block')->where('title', $name)->find();
		\$_block_content = '';
		if (\$_block) {
			if (\$_block['type'] == 2) {
				if (!$textflag) {
					\$_block_content = '<img src="'. \$_block['content'] .'" />';
				} else {
					\$_block_content = \$_block['content'];
				}
			} else {
				\$_block_content = \$_block['content'];
			}

			\$_blockurl = '';
			if (\$_block['url']) {
				\$_blockurl = \$_block['url'];
				\$_block_content= "<a href='".\$_blockurl."' target='_blank'>".\$_block_content."</a>";
			}
		}
		echo \$_block_content;
?>
str;
		return $str;
	}
}
