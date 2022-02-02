<?php
namespace Idea\Observers;

class GapAgreementObserver {

    public function created($object)
    {
      $last=array();
      foreach($object->getOriginal() as $key => $value){
        if($object->$key!=$value&&$key!='updated_at')
          $last[$key]=$value;
      }
      $object->storeHistory('create',null,null,json_encode($last));
    }

    public function updated($object)
    {
      $last=array();
      foreach($object->getOriginal() as $key => $value){
        if($object->$key!=$value&&$key!='updated_at')
          $last[$key]=$value;
      }
      $object->storeHistory('edit',null,null,json_encode($last));
    }

}
