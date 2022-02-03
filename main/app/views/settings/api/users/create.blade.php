<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie konta</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/api/users/store') }}" method="post" id="dialog-form">
            <fieldset>
                <div class="form-group">
                    <label>Login:</label>
                    {{  Form::text('login', '', array('class' => 'form-control required', 'placeholder' => 'login')) }}
                </div>
                <div class="form-group">
                    <label>Nazwa:</label>
                    {{  Form::text('name',  '', array('class' => 'form-control required', 'placeholder' => 'nazwa konta'))  }}
                </div>
                {{ Form::token() }}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" data-loading-text="trwa dodawanie konta">Dodaj konto</button>
</div>