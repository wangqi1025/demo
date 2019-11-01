<?php
namespace app\admin\model;
use think\Model;
use think\Db;

class DiyModel extends Model{
    protected $name = 'auth_model';

    public function getAllModel()
    {
        return $this->order('rank asc')->select();
    }

    public function getOneDiymodel($id)
    {
        $item = $this->where(['id'=>$id])->find();
        $product = empty($item)?array():$item->toArray();
        return $product;
    }

    public function insertDiymodel($param)
    {
        $result = $this->insertGetId($param);
        return $result;
    }

    public function saveDiyModel($id,$param)
    {
        return $this->where(array('id'=>$id))->update($param);
    }

    public function delDiyModel($id)
    {
        return $this->where(array('id'=>$id))->delete();
    }

    public function copyDiymodelField($ytabname, $ntabname)
    {
        try{
            $yid = $this->where('table_name', $ytabname)->find();
            $nid = $this->where('table_name', $ntabname)->find();
            $db = Db::name('auth_field');
            $fieldlist = $db->where(['mid'=>$yid['id']])->select();
            foreach ($fieldlist as $k => $v) {
                unset($fieldlist[$k]['id']);
                $fieldlist[$k]['mid'] = $nid['id'];
            }
            $db->insertAll($fieldlist);
            return ['code' => 1, 'data' => '', 'msg' => ''];
        }catch(\Exception $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}