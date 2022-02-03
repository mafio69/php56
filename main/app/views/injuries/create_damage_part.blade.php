<div class="col-md-6">
    <table class="table table-hover">
        <?php for($i = 0 ; $i < (count($damage)/2); $i++){?>
        <tr >
            <td>
                <input type="checkbox" id="uszkodzenia_check<?php echo $damage[$i]['id'];?>" class="uszkodzenia_check" name="uszkodzenia[]" value="<?php echo $damage[$i]['id'];?>" <?php if(isset($damageInjury[$damage[$i]['id']])){?> checked="checked" <?php }?> />
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
                <input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>r" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="2" <?php if(isset($damageInjury[$damage[$i]['id']][2])){?> checked="checked" <?php }?>/>
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
        <?php for($i ; $i < count($damage); $i++){?>
        <tr>
            <td>
                <input type="checkbox" id="uszkodzenia_check<?php echo $damage[$i]['id'];?>" class="uszkodzenia_check" name="uszkodzenia[]" value="<?php echo $damage[$i]['id'];?>" <?php if(isset($damageInjury[$damage[$i]['id']])){?> checked="checked" <?php }?>/>
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
                <input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>r" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="2" <?php if(isset($damageInjury[$damage[$i]['id']][2])){?> checked="checked" <?php }?>/>
            </td>
            <?php }else{?>
            <td colspan="2"></td>
            <?php }?>
        </tr>
        <?php }?>
    </table>
</div>
<div class="col-sm-12">
    <label>Uwagi do uszkodzeń</label>
    {{ Form::textarea('damage_info', '', array('class' => 'form-control  ', 'placeholder' => 'Uwagi do uszkodzeń'))  }}
</div>