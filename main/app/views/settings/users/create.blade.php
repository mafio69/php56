<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie użytkownika</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/users/store') }}" method="post" id="dialog-form">
            <fieldset>
                <div class="form-group">
                    <label>Login:</label>
                    {{  Form::text('login', '', array('class' => 'form-control required', 'placeholder' => 'login')) }}
                </div>
                <div class="form-group">
                    <label>Nazwisko:</label>
                    {{  Form::text('name',  '', array('class' => 'form-control required', 'placeholder' => 'nazwisko'))  }}
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    {{  Form::text('email',  '', array('class' => 'form-control email mail', 'placeholder' => 'email'))  }}
                </div>
                <div class="form-group">
                    <label>Data ważności konta:</label>
                    {{ Form::text('active_to', null, ['class'=> 'form-control', 'placeholder' => 'data ważności konta']) }}
                </div>
                <div class="form-group">
                    <label>Nr telefonu:</label>
                    {{ Form::text('phone_number',  null, array('class' => 'form-control', 'placeholder' => 'numer telefonu'))  }}
                </div>
                <div class="form-group">
                    <label>Dział:</label>
                    {{ Form::select('department_id', $departments,  null, array('class' => 'form-control'))  }}
                </div>
                <div class="form-group">
                    <label>Zespół:</label>
                    {{ Form::select('team_id', $teams,  null, array('class' => 'form-control'))  }}
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="is_external" value="1" > Pracownik zewnętrzny
                        </label>
                    </div>
                </div>
                {{ Form::token() }}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" data-loading-text="trwa dodawanie użytkownika">Dodaj użytkownika</button>
</div>
<script>
    $('input[name="active_to"]').datepicker();
</script>
