@if(Auth::user()->can('kartoteka_szkody#uszkodzenia'))
    <div class="tab-pane fade in " id="damage">
      <form id="form_damage">
        {{Form::token()}}
        <div class="row">
            <div class="col-md-6">
                <table class="table table-hover">
                    <?php for($i = 0 ; $i < ($ct_damage/2); $i++){?>
                    <tr >
                        <td>
                            <input type="checkbox" id="uszkodzenia_check<?php echo $damage[$i]['id'];?>" class="uszkodzenia_check" name="uszkodzenia[]" value="<?php echo $damage[$i]['id'];?>" <?php if(isset($damageInjury[$damage[$i]['id']])){?> checked="checked" <?php }?> @if(! Auth::user()->can('kartoteka_szkody#uszkodzenia#edytuj_uszkodzenia')) disabled @endif/>
                        </td>
                        <td class="check" style="text-align:left; padding-left:5px;">
                            <b><label for="uszkodzenia_check<?php echo $damage[$i]['id'];?>"><?php echo $damage[$i]['name'];?></label></b>
                        </td>
                        <?php if($damage[$i]['param'] != 0){?>
                        <td  class="check">
                            <label for="strona<?php echo $damage[$i]['id'];?>l">
                                <?php switch($damage[$i]['param']){
                                        case 1:
                                            echo 'lewy:';
                                            break;
                                        case 2:
                                            echo 'lewe:';
                                            break;
                                        case 3:
                                            echo 'lewa:';
                                            break;
                                }?>
                            </label>
                            <input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>l" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="1" <?php if(isset($damageInjury[$damage[$i]['id']][1])){?> checked="checked" <?php }?>/>
                        </td>
                        <td class="check">
                            <label for="strona<?php echo $damage[$i]['id'];?>r">
                                <?php switch($damage[$i]['param']){
                                        case 1:
                                            echo 'prawy:';
                                            break;
                                        case 2:
                                            echo 'prawe:';
                                            break;
                                        case 3:
                                            echo 'prawa:';
                                            break;
                                }?>
                            </label>
                            <input  class="check_strona" id="strona<?php echo $damage[$i]['id'];?>r" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="2" <?php if(isset($damageInjury[$damage[$i]['id']][2])){?> checked="checked" <?php }?>/>
                        </td>
                        <?php }else{?>
                        <td colspan="2"></td>
                        <?php }?>
                    </tr>
                    <?php }?>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-hover">
                    <?php for($i ; $i < $ct_damage; $i++){?>
                    <tr>
                        <td>
                            <input  type="checkbox" id="uszkodzenia_check<?php echo $damage[$i]['id'];?>" class="uszkodzenia_check" name="uszkodzenia[]" value="<?php echo $damage[$i]['id'];?>" <?php if(isset($damageInjury[$damage[$i]['id']])){?> checked="checked" <?php }?> @if(! Auth::user()->can('kartoteka_szkody#uszkodzenia#edytuj_uszkodzenia')) disabled @endif/>
                        </td>
                        <td class="check" style="text-align:left; padding-left:5px;">
                            <b><label for="uszkodzenia_check<?php echo $damage[$i]['id'];?>"><?php echo $damage[$i]['name'];?></label></b>
                        </td>
                        <?php if($damage[$i]['param'] != 0){?>
                        <td  class="check">
                            <label for="strona<?php echo $damage[$i]['id'];?>l">
                                <?php switch($damage[$i]['param']){
                                        case 1:
                                            echo 'lewy:';
                                            break;
                                        case 2:
                                            echo 'lewe:';
                                            break;
                                        case 3:
                                            echo 'lewa:';
                                            break;
                                }?>
                            </label>
                            <input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>l" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="1" <?php if(isset($damageInjury[$damage[$i]['id']][1])){?> checked="checked" <?php }?>/>
                        </td>
                        <td class="check">
                            <label for="strona<?php echo $damage[$i]['id'];?>r">
                                <?php switch($damage[$i]['param']){
                                        case 1:
                                            echo 'prawy:';
                                            break;
                                        case 2:
                                            echo 'prawe:';
                                            break;
                                        case 3:
                                            echo 'prawa:';
                                            break;
                                }?>
                            </label>
                            <input  class="check_strona" id="strona<?php echo $damage[$i]['id'];?>r" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="2" <?php if(isset($damageInjury[$damage[$i]['id']][2])){?> checked="checked" <?php }?>/>
                        </td>
                        <?php }else{?>
                        <td colspan="2"></td>
                        <?php }?>
                    </tr>
                    <?php }?>
                </table>
            </div>
        </div>
    </form>
        <div class="row">
            <div class="col-sm-6 item-m">
              <div class="panel panel-default small">
                 <div class="panel-heading ">Opis szkody:</div>
                 <table class="table">
                  <?php if($injury->remarks != 0){?>
                  <tr>
                    <td>{{ $remarks->content }}</td>
                  </tr>
                  <?php }?>
                 </table>
              </div>
            </div>

            <div class="col-sm-6 item-m">
              <div class="panel panel-default small">
                 <div class="panel-heading ">Uwagi do uszkodzeń:
                     @if(Auth::user()->can('kartoteka_szkody#uszkodzenia#edytuj_uwagi_do_uszkodzen'))
                          <i class="fa fa-pencil-square-o pull-right tips modal-open" target="{{ URL::route('injuries-getEditInjuryRemarks-damage', array($injury->id)) }}" data-toggle="modal" data-target="#modal" title="edytuj"></i>
                     @endif
                 </div>
                 <table class="table">
                  <?php if($injury->remarks_damage != 0){?>
                  <tr>
                    <td>{{ $remarks_damage->content }}</td>
                  </tr>
                  <?php }?>
                 </table>
              </div>
            </div>
        </div>
    </div>
@endif
