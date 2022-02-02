<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja marki</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('brands-set', array($brand->id)) }}" method="post"  id="edit-brand-form"> 
            
            <fieldset>
                <div class="form-group">
                    <label>Nazwa marki:</label>
                	{{ Form::text('name', $brand->name, array('class' => 'form-control required', 'placeholder' => 'nazwa marki', 'autofocuse' => ''))  }}                                
                </div>
                <div class="form-group">
                    <label>Typ:</label>
                    
                    <select name="typ" id="typeGarages" class="form-control" >
			    		
			    		<option value="1" <?php if($brand->typ == 1){?> select="selected" <?php }?>>osobowe</option>
			    		<option value="2" <?php if($brand->typ == 2){?> select="selected" <?php }?>>ciężarowe</option>
			    		
			    	</select>
                             
                            
                </div>                
                {{Form::token()}}	
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="save-brand">Zapisz zmiany</button>
</div>