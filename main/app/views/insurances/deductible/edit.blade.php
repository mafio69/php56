<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja wartości franszyzy redukcyjnej <i>{{ $rate->name }}</i></h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('insurances/deductible/update', [$rate->id]) }}" method="post"  id="dialog-form">
            {{Form::token()}}
            <fieldset>
                <div class="form-group">
                  <label>Wartość</label>
                  <div class="input-group">
                    {{ Form::text('value', ($rate->deductible_percent) ? $rate->deductible_percent : $rate->deductible_value, array('class' => 'form-control required number currency_input', 'placeholder' => 'Wartość')) }}
                    <div class="input-group-btn"  data-toggle="buttons">
                      <label class="btn btn-primary @if(!$rate->deductible_percent) active @endif ">
                         <input type="radio" name="type" autocomplete="off" value="1" @if(!$rate->deductible_percent) checked @endif > zł
                       </label>
                       <label class="btn btn-primary @if($rate->deductible_percent) active @endif "">
                         <input type="radio" name="type"autocomplete="off" value="2" @if($rate->deductible_percent) checked @endif> %
                       </label>
                    </div>
                  </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wykonywanie..."  id="set">Zapisz</button>
</div>
