<?php
namespace app\admin\model;
use think\Model;
use think\Db;

class DiyField extends Model{
    protected $name = 'auth_field';
    public function getAllFieldCount($mid,$type)
    {
        return $this->where(array('type'=>$type,'mid'=>$mid))->count();
    }

    public function delAllField($mid)
    {
        return $this->where(array('mid'=>$mid,'type'=>1))->delete();
    }

    public function selectField($mid)
    {
        $item = $this->where(array('mid'=>$mid,'type'=>1))->select();
        $product = empty($item) ? array() : $item->toArray();
        return $product;
    }

    public function delField($id)
    {
        return $this->where(array('id'=>$id,'type'=>1))->delete();
    }

    public function findField($id)
    {
        $item = $this->where(array('id'=>$id,'type'=>1))->find();
        $product = empty($item) ? array():$item->toArray();
        return $product;
    }

    public function updateField($id,$param)
    {
        return $this->where(array('id'=>$id))->update($param);
    }

    public function insertField($param)
    {
        return $this->insertGetId($param);
    }


}