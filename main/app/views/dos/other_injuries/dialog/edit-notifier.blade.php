<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych zgłaszającego</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('dos.other.injuries.set', array('updateNotifier', $id)) }}" method="post"  id="dialog-injury-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label>Imię:</label>
                    {{ Form::text('notifier_name', $injury->notifier_name, array('class' => 'form-control required', 'placeholder' => 'imię'))  }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label>Nazwisko:</label>
                    {{ Form::text('notifier_surname', $injury->notifier_surname, array('class' => 'form-control', 'placeholder' => 'nazwisko'))  }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label>Telefon:</label>
                    {{ Form::text('notifier_phone', $injury->notifier_phone, array('class' => 'form-control', 'placeholder' => 'telefon') )}}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label>Email:</label>
                    {{ Form::text('notifier_email', $injury->notifier_email, array('class' => 'form-control', 'placeholder' => 'email'))  }}
                </div>
            </div>

        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
</div>

