<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie szablonu</h4>
</div>
<div class="modal-body">
    <div class="panel-body">

            <fieldset>
                <div class="form-group">
                    <label>Treść szablonu:</label>
                    {{ $template->body }}
                </div>
            </fieldset>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>