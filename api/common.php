<?php
// 应用公共文件

use think\Db;

define('OK', '操作成功');
define('FAIL', '系统繁忙');


/*
 *	验证用户名 4-18位数字或字母
 * */
function is_name($str)
{
    return preg_match("/^[A-Za-z0-9]{4,18}$/", $str) ? true : false ;
}


/*
 *	验证密码 6-18位数字或字母
 * */
function is_pass($str)
{
    return preg_match("/^[A-Za-z0-9]{6,18}$/", $str) ? true : false ;
}


/*
 *	验证手机号
 * */
function is_phone($str)
{
    return preg_match("/^1[3456789]{1}\d{9}$/", $str) ? true : false ;
}


/*
 *	获取用户IP
 */
function get_ip()
{
	return $_SERVER['SERVER_ADDR'];
}


/*
 *	记录用户操作日志
 */
function get_log($id, $name, $info, $status)
{	
	$data = [
        'admin_id'  => $id,
        'admin_name'=> $name,
        'info'      => $info,
        'ip'        => get_ip(),
        'status'    => $status,
        'time'      => date('Y-m-d H:i:s')
    ];
    Db::name('admin_log')->insert($data);
}


/**
 *  功能：计算文件大小
 *  @param int $bytes
 *  @return string 转换后的字符串
 */
function get_byte($bytes) {
    if (empty($bytes)) {
        return '--';
    }
    $sizetext = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . $sizetext[$i];
}
