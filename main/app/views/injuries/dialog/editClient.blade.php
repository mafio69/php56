
<div class="panel-body">
        <fieldset>
            <div class="form-group">
                <div class="row">
                    <label>Wybierz klienta:</label>
                	<select id="selectNewClient" class="form-control" >
                        <?php foreach($clients as $k => $v){?>
                            <option <?php if($v->id == $id){?>selected<?php }?>  value="<?php echo $v->id;?>">{{ $v->name }}</option>
                        <?php }?>
                    </select>                              
                </div>
            </div>
        </fieldset>
    
</div>
<script type="text/javascript" >
    $(document).ready(function(){
        $('#selectNewClient').change(function(){
            $('#client_id').val($(this).val());
            $('p[name=client]').html($('#selectNewClient option:selected').text());
            if($('#driver_id').val() != ''){
                $('#driver_id').val('');
                $('input[name=driver_surname]').val('');
                $('input[name=driver_name]').val('');
                $('input[name=driver_phone]').val('');
                $('input[name=driver_email]').val('');
                $('input[name=driver_city]').val('');
            }
        });
    });
</script>    