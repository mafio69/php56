<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja karty <i>{{$card->number}}</i></h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('settings.liquidation_cards', array('update', $card->id)) }}" method="post"  id="dialog-form">

            <fieldset>
                <div class="form-group">
                    <label>Numer karty:</label>
                	{{ Form::text('number', $card->number, array('class' => 'form-control required number', 'autofocuse' => ''))  }}
                </div>
                <div class="form-group">
                    <label>Data wydania karty:</label>
                    {{ Form::text('release_date', $card->release_date, array('class' => 'form-control required', 'id' => 'release_date'))  }}
                </div>
                <div class="form-group">
                    <label>Data ważności karty:</label>
                    {{ Form::text('expiration_date', $card->expiration_date, array('class' => 'form-control required', 'id' => 'expiration_date'))  }}
                </div>

                {{Form::token()}}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zapisz zmiany</button>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $( "#release_date" ).datepicker({ showOtherMonths: true, selectOtherMonths: true,
          dateFormat: "yy-mm-dd",
          changeMonth: true,
          numberOfMonths: 3,
          onClose: function( selectedDate ) {
            $( "#expiration_date" ).datepicker( "option", "minDate", selectedDate );
          }
        });
        $( "#expiration_date" ).datepicker({ showOtherMonths: true, selectOtherMonths: true,
          defaultDate: "+1w",
          dateFormat: "yy-mm-dd",
          changeMonth: true,
          numberOfMonths: 3,
          onClose: function( selectedDate ) {
            $( "#release_date" ).datepicker( "option", "maxDate", selectedDate );
          }
        });
    });
</script>