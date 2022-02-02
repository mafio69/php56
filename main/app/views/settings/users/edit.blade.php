<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja użytkownika</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/users/update', [$user_db->id]) }}" method="post" id="dialog-form">
            <fieldset>
                <div class="form-group">
                    <label>Nazwisko:</label>
                    {{ Form::text('name',  $user_db->name, array('class' => 'form-control required', 'placeholder' => 'nazwisko'))  }}
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    {{ Form::text('email',  $user_db->email, array('class' => 'form-control email mail', 'placeholder' => 'adres email'))  }}
                </div>
                <div class="form-group">
                    <label>Data ważności konta:</label>
                    {{ Form::text('active_to', $user_db->active_to ? $user_db->active_to->format('Y-m-d') : null, ['class'=> 'form-control', 'placeholder' => 'data ważności konta']) }}
                </div>
                <div class="form-group">
                    <label>Nr telefonu:</label>
                    {{ Form::text('phone_number',  $user_db->phone_number, array('class' => 'form-control', 'placeholder' => 'numer telefonu'))  }}
                </div>
                <div class="form-group">
                    <label>Dział:</label>
                    {{ Form::select('department_id', $departments,  $user_db->department_id, array('class' => 'form-control'))  }}
                </div>
                <div class="form-group">
                    <label>Zespół:</label>
                    {{ Form::select('team_id', $teams,  $user_db->team_id, array('class' => 'form-control'))  }}
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="is_external" value="1" @if($user_db->is_external == 1) checked="checked" @endif > Pracownik zewnętrzny
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
    <button type="button" class="btn btn-primary" data-loading-text="Zapisywanie..."  id="set">Zapisz zmiany</button>
</div>
<script>
    $('input[name="active_to"]').datepicker();
</script>
