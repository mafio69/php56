<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Tworzenie nowej roli</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('roles-add') }}" method="post"  id="create-role-form">
            <fieldset>
                <div class="form-group">
                    <label>Nazwa roli:</label>
                	{{ Form::text('name', '', array('class' => 'form-control required', 'placeholder' => 'nazwa roli', 'autofocuse' => ''))  }}                                
                </div>
                <div class="form-group">
                    <label>Uprawniania:</label>
                    
                	<?php foreach($perms as $k => $v){?>
                        <div class="checkbox ">
                          <label>
                            <input type="checkbox" name="perms[]" value="{{$k}}">
                            {{$v}}
                          </label>
                        </div>
                    <?php }?>                
                            
                </div>                
                {{Form::token()}}	
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="create-role">Dodaj rolę</button>
</div>