<?php

  class DokProcesses_tree {

    private $items;

    public function __construct($items) {
      $this->items = $items;
    }

    public function htmlList() {
      return $this->htmlFromArray($this->itemArray());
    }

    private function itemArray() {
      $result = array();
      foreach($this->items as $item) {
        if ($item->parent_id == 0) {
          $result[$item->id.'|'.$item->name] = $this->itemWithChildren($item);
        }
      }
      return $result;
    }

    private function childrenOf($item) {
      $result = array();
      foreach($this->items as $i) {
        if ($i->parent_id == $item->id) {
          $result[] = $i;
        }
      }
      return $result;
    }

    private function itemWithChildren($item) {
      $result = array();
      $children = $this->childrenOf($item);
      foreach ($children as $child) {
        $result[$child->id.'|'.$child->name] = $this->itemWithChildren($child);
      }
      return $result;
    }

   

    private function htmlFromArray($array) {
      $html = '';

      foreach($array as $k=>$v) {
          $html .= '<li';
            if(count($v) == 0) 
              $html .= ' class="last_child" ';
          $html .= '>';

            $pos = strpos($k, '|');
            $name = substr($k, $pos+1);
            $k = substr($k, 0, $pos);

            $html .= '<span';
              if(count($v) == 0){
                $html .= ' class="label label-info last_child edit_process" target="';
                $html .= URL::route('settings.processes.info', array($k));
                $html .= ' "';
              }else {
                $html .= ' class="btn btn-primary btn-sm edit_process" target="';
                $html .= URL::route('settings.processes.info-node', array($k));
                $html .= ' "';
              }
            $html .= '>';

                $html .= $name;
                if(count($v) > 0) $html .= ' <i class="fa fa-plus"></i>';

            $html .= '</span>';

            if(count($v) > 0) {
              $html .= '<ul>';
                  $html .= $this->htmlFromArray($v);
              $html .= '</ul>';
            }

          $html .= '</li>';
      }
      
      return $html;
    }
  }
