<?php
namespace app\admin\controller;
use app\admin\model\DiyField;
use app\admin\model\DiyForm;
use think\Request;
use think\Db;
use think\Controller;

class Diyforms extends Base
{
    public $fields;
    public function __construct()
    {
        parent::__construct();
        $this->fields = ['text' => '单行文本','number' => '数字', 'textarea' => '多行文本','radio' => ' 单选按钮', 'checkbox' => '复选框', 'select' => '下拉框'];
        $this->formtype = [
            'text'=>'varchar',
            'textarea'=>'text',
            'radio'=>'text',
            'checkbox'=>'text',
            'number'=>'int',
            'select'=>'text'
        ];
    }

    public function index()
    {
        $form = new DiyForm();
        $info = $form->selectAllForm();
        $this->assign('info',$info);
        return $this->fetch();
    }

    ///新增表单表
    public function add()
    {
        $input = $this->request->param();
        if (isset($input['table_name'])){
            dump($input);die;
        }
        if (isset($info['is_send_email'])){
            $this->assign('email_type',1);
        }else{
            $this->assign('email_type',0);
        }
        $this->assign('res',null);
        return $this->fetch();
    }




}