<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przypisywanie pracowników do procesu</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('settings.processes.appendUser', array($process->id)) }}" method="post"  id="edit-form"> 
            
            <div class="form-group">
                <label>Wprowadź pracowników:</label>
            	<input name="users" class="form-control" id="users" multiple ="multiple " />                       
            </div>
            {{Form::token()}}	
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="save">Przypisz</button>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#users').select2({
            placeholder: "Wpisz dane szukanego pracownika",
            minimumInputLength: 2,
            multiple:true,
            ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                url: "<?php echo  URL::route('settings.processes.searchUsers', array($process->id) );?>",
                dataType: 'json',
                type: "POST",
                data: function (term, page) {
                    return {
                        q: term,
                        _token: $('input[name="_token"]').val()
                    };
                },  
                results: function (data) {  
                                
                    return {results: data};
                }
                
            }
        });
    });
</script>