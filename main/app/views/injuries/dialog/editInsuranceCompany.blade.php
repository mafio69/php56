
<div class="panel-body">
        <fieldset>
            <div class="form-group">
                <div class="row">
                    <label>Wybierz zakład ubezpieczeń:</label>
                	<select id="selectNewInsuranceCompany" class="form-control" >
                        <?php foreach($insurance_companies as $k => $v){?>
                            <option <?php if($v->id == $id){?>selected<?php }?>  value="<?php echo $v->id;?>">{{ $v->name }}</option>
                        <?php }?>
                    </select>                              
                </div>
            </div>
        </fieldset>
    
</div>
<script type="text/javascript" >
    $(document).ready(function(){
        $('#selectNewInsuranceCompany').change(function(){
            $('#insurance_company_id').val($(this).val());
            $('p[name=insurance_company]').html($('#selectNewInsuranceCompany option:selected').text());
        });
    });
</script>    