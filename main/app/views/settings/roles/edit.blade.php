<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja roli</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('roles-set', array($role->id)) }}" method="post"  id="create-role-form">
            
            <fieldset>
                <div class="form-group">
                    <label>Nazwa roli:</label>
                	{{ Form::text('name', $role->name, array('class' => 'form-control required', 'placeholder' => 'nazwa roli', 'autofocuse' => ''))  }}                                
                </div>

                <label>Uprawniania:</label>
                <div class="row">
                    @foreach($perms as $k => $v)
                        <div class="col-sm-12 col-lg-6">
                            <div class="checkbox ">
                              <label>
                                <input type="checkbox" name="perms[]" value="{{$k}}"

                                    <?php if( isset($perms_r[$k]) && $perms_r[$k] == 1){
                                        echo 'checked="checked"';
                                    }?>

                                />
                                {{$v}}
                              </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                            
                {{Form::token()}}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="create-role">Zapisz zmiany</button>
</div>