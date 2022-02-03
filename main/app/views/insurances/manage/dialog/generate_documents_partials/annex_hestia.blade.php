<div class="row">
    <?php $insurance = $agreement->insurances->last();?>
    <div class="col-sm-12 marg-btm">
        <label >Aneks nr:</label>
        {{ Form::text('annex_id', '1', array('class' => 'form-control')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Numer aneksu:</label>
        {{ Form::text('annex_number', '', array('class' => 'form-control')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Dotyczy:</label>
        {{ Form::select('refer',$annex_refers, null  , array('class' => 'form-control')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Polisa od:</label>  {{ $insurance->date_from }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Polisa do:</label>  {{ $insurance->date_to }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Składka leasingodawcy:</label> {{ number_format($insurance->contribution,2,"."," ") }} zł
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Data obowiązywania zmian od:</label>
        {{ Form::text('date_form', date('Y-m-d') , array('class' => 'form-control datepicker',  'placeholder' => 'Od')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Data obowiązywania zmian do:</label>
        {{ Form::text('date_to', '' , array('class' => 'form-control datepicker',  'placeholder' => 'Od')) }}
    </div>
    <div class="col-sm-12 marg-btm" id="end_date" style="display:none;">
        <label >Data rozwiązania polisy:</label>
        {{ Form::text('end_date', date('Y-m-d') , array('class' => 'form-control datepicker',  'placeholder' => 'Data rozwiązania polisy')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Kwota zwrotu składki:</label>
        @if($insurance->if_refund_contribution == '0')
            {{ Form::text('annex_value',0, array('class' => 'form-control currency_input number','placeholder' => 'Kwota')) }}
        @else
            {{ Form::text('annex_value',number_format($insurance->refund,2,".",""), array('class' => 'form-control currency_input number','placeholder' => 'Kwota')) }}
        @endif
    </div>
    <div class="col-sm-12 marg-btm">
      <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-primary active">
          <input type="radio" name="type" autocomplete="off" checked value="1">Zmniejszenie składki
        </label>
        <label class="btn btn-primary">
          <input type="radio" name="type" autocomplete="off" value="2">Zwiększenie składki
        </label>
      </div>
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Termin zwrotu:</label>
        {{ Form::text('return_date', date('Y-m-d') , array('class' => 'form-control datepicker',  'placeholder' => 'Termin zwrotu')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Treść aneksu:</label>
        {{ Form::textarea('annex_content', '', array('class' => 'form-control  ', 'id'=>'content',  'placeholder' => 'Treść aneksu')) }}
    </div>
</div>

<script>
    $('input[name="annex_value"]').on('focusout', function(){
        document.getElementById('generate-document').disabled = false;
        $('#base_error').remove();

        var contribution = {{ $insurance->contribution }};
        var annex_value = $('input[name="annex_value"]').val();
        var type_input = document.getElementsByName('type');
        var type;
        for(i = 0; i < type_input.length; i++){
            if (type_input[i].checked){
                type = type_input[i].value;
            }
        }

        if (type == 1 && (contribution * 100 - annex_value * 100) / 100 < 0){
            document.getElementById('generate-document').disabled = true;
            $('<label for="annex_value" id="base_error" class="error">Kwota zmniejszenia składki nie może być większa od obecnej składki.</label>').insertAfter($(this));
        }
    });
</script>
