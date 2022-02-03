<div class="row">
  <?php $field = $documentType->alert_name;?>
  @if($field != '0' && (!$injury->wreck || $injury->wreck->$field == '0000-00-00') )
  <div class="col-sm-12 marg-btm">
    <label >Alert czasowy:</label>
    {{ Form::text('alert', Date('Y-m-d', strtotime("+3 days")) , array('class' => 'form-control required', 'id'=>'date_submit',  'placeholder' => 'Alert czasowy', 'required')) }}
  </div>
  @else
    <div class="col-sm-12 marg-btm">
        Potwierdź wygenerowanie dokumentu.
    </div>
  @endif
</div>