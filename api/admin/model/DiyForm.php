<?php
namespace app\admin\model;
use think\Model;
use think\Db;

class DiyForm extends Model{
    protected $name = 'auth_form';
    public function selectAllForm()
    {
        $item = $this->select();
        $product = empty($item)?array():$item->toArray();
        return $product;
    }

    public function insertForm($param)
    {
        return $this->insertGetId($param);
    }

    public function findForm($id)
    {
        $info = $this->where(array('id'=>$id))->find();
        $form = empty($info)?array():$info->toArray();
        return $form;
    }

    public function updateForm($id,$param)
    {
        return $this->where(array('id'=>$id))->update($param);
    }
}