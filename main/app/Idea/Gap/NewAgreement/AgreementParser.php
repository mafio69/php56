<?php
namespace Idea\Gap\NewAgreement;

use Clients;
use Idea\AddressParser\AddressParser;
use Idea\VoivodeshipMatcher\SingleMatching;
use GapAgreement;
use GapAgreementGroup;
use GapAgreementType;
use GapAgreementObjectType;
use GapAgreementObjectGroup;

class AgreementParser {

    private $groups;

    public function __construct(){
        $this->groups = GapAgreementGroup::all()->lists('id','name');
        $this->types = GapAgreementType::all()->lists('id','name');
        $this->object_types = GapAgreementObjectType::all()->lists('id','code');
        $this->object_groups = GapAgreementObjectGroup::all()->lists('id','name');
    }

    public function checkAgreement($value)
    {
        $exist_agreement = GapAgreement::where('agreement_number', '=', $value)->get();
        if($exist_agreement->isEmpty())
        {
            return 'new';
        }else{
            return 'exist';
        }
    }

    public function checkGroup($value){
        if(isset($this->groups[$value])){
          return $this->groups[$value];
        }
        else{
          $group = GapAgreementGroup::create(['name'=>$value]);
          $this->groups = GapAgreementGroup::all()->lists('id','name');
          return $group->id;
        }
    }

    public function checkType($value){
        if(isset($this->types[$value])){
          return $this->types[$value];
        }
        else{
          $type = GapAgreementType::create(['name'=>$value]);
          $this->types = GapAgreementType::all()->lists('id','name');
          return $type->id;
        }
    }

    public function checkObjectType($value){
        if(isset($this->object_types[$value])){
          return $this->object_types[$value];
        }
        else{
          $type = GapAgreementObjectType::create(['code'=>$value]);
          $this->object_types = GapAgreementObjectType::all()->lists('id','code');
          return $type->id;
        }
    }

    public function checkObjectGroup($value,$type_value){
        if(isset($this->object_groups[$value])){
          return $this->object_groups[$value];
        }
        else{
          $group = GapAgreementObjectGroup::create(['name'=>$value,'type_id'=>$type_value]);
          $this->object_groups= GapAgreementObjectGroup::all()->lists('id','name');
          return $group->id;
        }
    }

}
