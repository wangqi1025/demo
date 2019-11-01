<?php
namespace app\admin\controller;

use app\admin\model\DiyModel;
use app\admin\model\DiyField;
use think\Request;
use think\Db;
use think\Controller;

class Diymode extends Base
{
    public $fields;
    public function __construct()
    {
        parent::__construct();
        $this->fields = ['text' => '单行文本', 'textarea' => '多行文本', 'seditor' => '简约编辑器', 'editor' => '富文本编辑器', 'file' => '附件', 'image' => '单图上传','images' => '多图上传', 'datetime' => '日期和时间', 'number' => '数字', 'radio' => ' 单选按钮', 'checkbox' => '复选框', 'select' => '下拉框'];
        $this->formtype = [
            'text'=>'varchar',
            'textarea'=>'text',
            'seditor'=>'text',
            'editor'=>'text',
            'file'=>'varchar',
            'image'=>'varchar',
            'images'=>'text',
            'datetime'=>'varchar',
            'radio'=>'text',
            'checkbox'=>'text',
            'number'=>'int',
            'select'=>'text'
        ];
    }

    public function index()
    {
        $model = new DiyModel();
        $field = new DiyField();
        $info = $model->getAllModel();
        foreach ($info as $k => $v){
            if ($info[$k]['type'] == 1){
                $info[$k]['status'] = '系统模型';
            }else {
                $info[$k]['status'] = '用户模型';
            }
            $info[$k]['num'] = $field->getAllFieldCount($info[$k]['id'],1);
        }
        $this->assign('info',$info);
        return $this->fetch();
    }

    ////添加字段
    public function add()
    {
        $model = new DiyModel();
        $this->assign('info',null);
        $input = $this->request->param();
        if (isset($input['name'])){
            $data['name'] = $input['name'];
            $data['table_name'] = $input['table_name'];
            $data['remark'] = $input['remark'];
            $data['rank'] = $input['rank'];
            $data['type'] = 2;
            $res = $model->insertDiymodel($data);
            if ($res){
                $sql = "CREATE TABLE `".config('database.prefix')."form_".$input['table_name']."` (`id` INT(10) PRIMARY KEY NOT NULL AUTO_INCREMENT)";
                Db::execute($sql);
                $this->success('添加成功',"index");
            }else{
                $this->error('添加失败');
            }
        }
        return $this->fetch('edit');
    }

    ///修改字段
    public function edit($id=null)
    {
        $model = new DiyModel();
        $input = $this->request->param();
        if (isset($input['name'])){
            $id = $input['id'];
            $table_name = Db::name('auth_model')->where(array('id'=>$id))->value('table_name');
            $data['name'] = $input['name'];
            $data['table_name'] = $input['table_name'];
            $data['remark'] = $input['remark'];
            $data['rank'] = $input['rank'];
            $res = $model->saveDiyModel($id,$data);
            if ($res){
                $sql = "ALTER TABLE ".config('database.prefix')."diy_".$table_name." RENAME TO ".config('database.prefix')."diy_".$data['table_name'].";";
                Db::execute($sql);
                $this->success('修改成功',"index");
            }else{
                $this->error('修改失败');
            }
        }
        $info = $model->getOneDiymodel($id);
        if ($info){
            $this->assign('info',$info);
        }else{
            $this->assign('info',null);
        }
        return $this->fetch();
    }

    ///删除表
    public function deldiymodel($id=null)
    {
        if ($id == null){
            return $this->error('缺少参数');
        }
        $table = Db::name('auth_model')->where(array('id'=>$id))->value('table_name');
        $model = new DiyModel();
        $field = new DiyField();

        try{
            $model->delDiyModel($id);
            $field->delAllField($id);
        }catch (\Exception $e){
            $this->error('删除模型失败');return;
        }
        //删除数据表名
        $sql = "DROP TABLE `".config('database.prefix')."diy_".$table."`";
        Db::execute($sql);
        $this->success('删除成功','index');
    }



    //复制模型
    public function copydiymodel()
    {
        $input = $this->request->param();
        $id = $input['id'];
        $diymodel = new DiyModel();
        $info = $diymodel->getOnediymodel($id);
        unset($info['id']);
        $ytabname = $info['table_name'];
        $t = getdate();
        $year=$t['year'];
        $month = $t['mon']<10? "0".$t['mon']:$t['mon'];
        $day = $t['mday']>9?$t['mday']:"0".$t['mday'];
        $randnum=mt_rand(100,999);
        $copytabname=$year.$month.$day.$randnum;
        $info['name'] = $info['name'].$copytabname;
        $info['table_name'] = $info['table_name'].$copytabname;
        $info['type'] = 2;
        $info['remark'] = $info['remark'];
        $info['rank'] = $info['rank'];
        $flag = $diymodel->insertdiymodel($info);
        if ($flag) {
            //复制数据表
            $sql = "CREATE TABLE `".config('database.prefix')."diy_".$info['table_name']."` SELECT * FROM `".config('database.prefix')."diy_".$ytabname."` where 0";
            Db::execute($sql);
            //设置主键
            $sql = "ALTER TABLE `".config('database.prefix')."diy_".$info['table_name']."` modify id int AUTO_INCREMENT PRIMARY KEY";
            Db::execute($sql);
            //复制字段管理数据
            $jg = $diymodel->copyDiymodelField($ytabname, $info['table_name']);
            if ($jg['code'] != 1) {
                return $jg;
            }else{
                return json(['code' => 1, 'msg' => '复制模型成功']);
            }
        }
    }

    ///查看每个模型的具体字段
    public function field($id)
    {
        $field = new DiyField();
        $model = new DiyModel();
        $diyModel = $model->getOneDiymodel($id);
        $info = $field->selectField($id);
        foreach ($info as $k => $v){
            $info[$k]['status'] = $diyModel['type'];
            if ($info[$k]['field_type'] == '------'){
                $info[$k]['field_type'] = '分组线';
                $info[$k]['field'] = '------';
                $info[$k]['name'] = '------';
                $info[$k]['rands'] = 2;
            }else{
                $info[$k]['rands'] = 1;
            }
        }
        $this->assign('info',$info);
        $this->assign('id',$id);
        return $this->fetch();
    }

    ///删除某个模型的某个field
    public function delField($id)
    {
        $field = new DiyField();
        $res = $field->delField($id);
        if ($res){
            return $this->success('删除成功','index');
        }else{
            return $this->error('删除失败');
        }
    }

    //字段的添加
    public function add_Field($id=null)
    {
        $field = new DiyField();
        $input = $this->request->param();
        $model = new DiyModel();
        if (isset($input['field'])){
            $data['mid'] = $input['mid'];
            $data['field_type'] = $input['field_type'];
            $data['is_not_null'] = $input['is_not_null'];
            $data['field'] = $input['field'];
            $data['name'] = $input['name'];
            $data['remark'] = $input['remark'];
            $data['length'] = $input['length'];
            $data['rank'] = $input['rank'];
            $data['type'] = 1;
            $data['default_value'] = $input['default_value'];
            $fields =  $this->fields;
            $type =  $this->formtype;
            $types = '';
            foreach ($fields as $k => $v){
                if ($fields[$k] == $input['field_type']){
                    foreach ($type as $key => $val){
                        if ($k == $key){
                            $types = $val;
                        }
                    }
                }
            }
            $selectField = $field->selectField($input['mid']);
            if ($selectField){
                $arr = [];
                foreach ($selectField as $k => $v){
                    array_push($arr,$selectField[$k]['field']);
                }
                $list = '';
                if(in_array($data['field'],$arr) == true){
                    return $this->error('字段名称重复');
                }
                $models = $model->getOneDiymodel($input['mid']);
                $tablename = config('database.prefix')."diy_".$models['table_name'];
                $res = $field->insertField($data);
                if ($res){
                    switch ($types) {
                        case 'varchar':
                            $sql = "ALTER TABLE `{$tablename}` ADD `{$input['field']}` VARCHAR({$input['length']}) DEFAULT '{$data["default_value"]}'";
                            Db::execute($sql);
                            break;
                        case 'int':
                            $defaultvalue = intval($data["default_value"]);
                            $sql = "ALTER TABLE `{$tablename}` ADD `{$input['field']}` INT(11) UNSIGNED DEFAULT '{$defaultvalue}'";
                            Db::execute($sql);
                            break;
                        case 'smallint':
                            $defaultvalue = intval($data["default_value"]);
                            $sql = "ALTER TABLE `{$tablename}` ADD `{$input['field']}` SMALLINT(5) UNSIGNED DEFAULT '{$defaultvalue}'";
                            Db::execute($sql);
                            break;
                        case 'text':
                            Db::execute("ALTER TABLE `{$tablename}` ADD `{$input['field']}` TEXT");
                            break;
                    }
                    $this->success('添加字段成功','index');
                }else{
                    $this->error('添加字段失败');
                }
            }

        }
        $checkbox = [
            '单行文本',
            '多行文本',
            '简约编辑器',
            '富文本编辑器',
            '附件',
            '单图上传',
            '多图上传',
            '日期和时间',
            '数字',
            '单选按钮',
            '复选框',
            '下拉框'
        ];
        $this->assign('checkbox',$checkbox);
        $info = $field->findField($id);
        if ($info){
            $this->assign('id',$info['mid']);
            $this->assign('res',$info);
            $this->assign('str',$info['field_type']);
        }else{
            $this->assign('id',$id);
            $this->assign('res',null);
        }
        return $this->fetch('edit_field');
    }

    ///编辑某个模型的field页面展示
    public function edit_field($id=null)
    {
        $input = $this->request->param();
        $model = new DiyModel();
        $field = new DiyField();
        if (isset($input['field'])){
            $data['mid'] = $input['mid'];
            $data['field_type'] = $input['field_type'];
            $data['is_not_null'] = $input['is_not_null'];
            $data['field'] = $input['field'];
            $data['name'] = $input['name'];
            $data['remark'] = $input['remark'];
            $data['length'] = $input['length'];
            $data['rank'] = $input['rank'];
            $data['type'] = 1;
            $data['default_value'] = $input['default_value'];
            $fields =  $this->fields;
            $type =  $this->formtype;
            $types = '';
            foreach ($fields as $k => $v){
                if ($fields[$k] == $input['field_type']){
                    foreach ($type as $key => $val){
                        if ($k == $key){
                            $types = $val;
                        }
                    }
                }
            }
            $selectField = $field->selectField($input['mid']);
            if ($selectField){
                $arr = [];
                foreach ($selectField as $k => $v){
                    array_push($arr,$selectField[$k]['field']);
                }
                $list = '';
                foreach ($selectField as $key => $val){
                    if ($input['id'] == $val['id']){
                        $list = $val['field'];
                    }
                    if ($input['id'] != $val['id'] && $input['field'] == $val['field']){
                        return $this->error('字段名称重复');
                    }
                }
            }
            $models = $model->getOneDiymodel($input['mid']);
            $tablename = config('database.prefix')."diy_".$models['table_name'];
            $res = $field->updateField($input['id'],$data);
            if ($res){
                switch ($types){
                    case 'varchar':
                        if ($data['default_value']){
                            $sql = "ALTER TABLE `{$tablename}` CHANGE COLUMN `{$list}` `{$data['field']}` VARCHAR({$input['length']}) DEFAULT '({$data["default_value"]})'";
                        }else{
                            $sql = "ALTER TABLE `{$tablename}` CHANGE COLUMN `{$list}` `{$data['field']}`  VARCHAR({$input['length']})";
                        }
                        Db::execute($sql);
                        break;
                    case 'int':
                        if ($data['default_value']){
                            $sql = "ALTER TABLE `{$tablename}` CHANGE COLUMN `{$list}` `{$data['field']}` INT(11) DEFAULT '({$data["default_value"]})'";
                        }else{
                            $sql = "ALTER TABLE `{$tablename}` CHANGE COLUMN `{$list}` `{$data['field']}` INT(11)";
                        }
                        Db::execute($sql);
                        break;
                    case 'smallint':
                        if ($data['default_value']){
                            $sql = "ALTER TABLE `{$tablename}` CHANGE COLUMN `{$list}` `{$data['field']}` SMALLINT(5) DEFAULT '({$data["default_value"]})'";
                        }else{
                            $sql = "ALTER TABLE `{$tablename}` CHANGE COLUMN `{$list}` `{$data['field']}`  SMALLINT(5)";
                        }
                        Db::execute($sql);
                        break;
                    case 'text':
                        $sql = "ALTER TABLE `{$tablename}` CHANGE COLUMN `{$list}` `{$data['field']}` TEXT ";
                        Db::execute($sql);
                        break;
                }
                $this->success('编辑成功','index');
            }else{
                $this->error('编辑失败');
            }

        }
        $checkbox = [
            '单行文本',
            '多行文本',
            '简约编辑器',
            '富文本编辑器',
            '附件',
            '单图上传',
            '多图上传',
            '日期和时间',
            '数字',
            '单选按钮',
            '复选框',
            '下拉框'
        ];
        $this->assign('checkbox',$checkbox);
        $info = $field->findField($id);
        if ($info){
            $this->assign('id',$info['mid']);
            $this->assign('res',$info);
            $this->assign('str',$info['field_type']);
        }else{
            $this->assign('id',$id);
            $this->assign('res',null);
        }
        return $this->fetch();
    }

    ///分组线添加
    public function add_grouping($id=null)
    {
        $input = $this->request->param();
        if (isset($input['ramark'])){
            $field = new DiyField();
            $box = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x'.'y','z'];
            $size = array_rand($box,3);
            $type = array_rand($box,3);
            $data['mid'] = $input['mid'];
            $data['remark'] = $input['ramark'];
            $data['rank'] = $input['rank'];
            $data['type'] = 1;
            $data['name'] = 'jd_'.$box[$size[0]].$box[$size[1]].$box[$size[2]];
            $data['field'] = 'jd_'.$box[$type[0]].$box[$type[1]].$box[$type[2]];
            $data['field_type'] = '------';
            $data['is_not_null'] = 1;
            $res = $field->insertField($data);
            if ($res){
                $this->success('添加分组线成功','index');
            }else{
                $this->error('添加分组线失败');
            }
        }
        $this->assign('info',null);
        $this->assign('mid',$id);
        return $this->fetch('grouping');
    }

    //分组线页面
    public function grouping($id,$type)
    {
            $field = new DiyField();
            $info = $field->findField($id);
            if ($info){
                $this->assign('info',$info);
            }else{
                $this->assign('info',null);
            }
        return $this->fetch();
    }
    ///分组线编辑
    public function update_grouping()
    {
        $input = $this->request->param();
        $field = new DiyField();
        $data['remark'] = $input['ramark'];
        $data['rank'] = $input['rank'];
        $res = $field->updateField($input['id'],$data);
        if ($res){
            $this->success('编辑成功','index');
        }else{
            $this->error('编辑失败');
        }
    }

}

