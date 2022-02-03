<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie marki</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('brands-add') }}" method="post"  id="dialog-form">
            
            <fieldset>
                <div class="form-group">
                    <label>Nazwa marki:</label>
                	{{ Form::text('name', '', array('class' => 'form-control required', 'placeholder' => 'nazwa marki', 'autofocuse' => ''))  }}                                
                </div>
                <div class="form-group">
                    <label>Typ:</label>
                    
                    <select name="typ" id="typeGarages" class="form-control" >
			    		
			    		<option value="1"  select="selected" >osobowe</option>
			    		<option value="2" >ciężarowe</option>
			    		
			    	</select>
                             
                            
                </div>                
                {{Form::token()}}	
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Dodaj</button>
</div>