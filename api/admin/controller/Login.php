<?php
// +----------------------------------------------------------------------
// | JDCMS V2.0
// +----------------------------------------------------------------------
// | Author: WANGQI <email:wq@ji-du.com.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use think\Controller;

class Login extends Controller
{
	public function login()
	{
		return $this->fetch();
	}

	// name pass code
	public function loginDo()
	{
		$data = request()->post();

		$name = isset($data['name']) ? trim($data['name']) : '';
		$pass = isset($data['pass']) ? trim($data['pass']) : '';
		$code = isset($data['code']) ? trim($data['code']) : '';

		if ($name == '') {
			return ['status'=>-1, 'msg'=>'请填写用户名'];
		}
		if ($pass == '') {
			return ['status'=>-2, 'msg'=>'请填写密码'];
		}
		if ($code == '') {
			return ['status'=>-3, 'msg'=>'请填写验证码'];
		}

		if (!is_name($name)) {
			return ['status'=>-4, 'msg'=>'用户名格式不正确'];
		}
		if (!is_pass($pass)) {
			return ['status'=>-5, 'msg'=>'密码格式不正确'];
		}

		$admin = Db::name('admin')->where('name', $name)->find();
		if (!$admin) {
			return ['status'=>-6, 'msg'=>'用户名密码错误1'];
		}

		if (!password_verify($pass, $admin['pass'])) {
			get_log($admin['id'], $admin['name'], '登录系统：密码错误', 0);
			return ['status'=>-7, 'msg'=>'用户名密码错误2'];
		}

		if (!captcha_check($code)) {
			get_log($admin['id'], $admin['name'], '登录系统：验证码错误', 0);
            return ['status'=>-8, 'msg'=>'验证码错误'];
        }

        if ($admin['is_active'] != 1) {
        	get_log($admin['id'], $admin['name'], '登录系统：账号被冻结', 0);
        	return ['status'=>-9, 'msg'=>'账号已被冻结'];
        }

        // 登录成功
        session('admin.id',		$admin['id']);
        session('admin.name',	$admin['name']);
        session('admin.role',	$admin['role_id']);

        Db::name('admin')->where('id', $admin['id'])->update([
        	'last_time'	=> date('Y-m-d H:i:s'),
        	'last_ip'	=> get_ip()
        ]);

        get_log($admin['id'], $admin['name'], '登录系统', 1); 

        return ['status'=>1,'msg'=>'登陆成功！'];
	}

    public function out()
    {
        get_log(session('admin.id'), session('admin.name'),'退出系统', 1);
        session('admin',NULL);
        $this->redirect('Login/login');
    }
}