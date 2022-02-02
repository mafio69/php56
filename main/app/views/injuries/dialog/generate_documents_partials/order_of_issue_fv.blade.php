<div class="row">
    <div class="col-sm-12 marg-btm">
        <div class="form-group">
            <label>Kwota netto FV</label>
            {{ Form::text('net_value', '', ['class' => 'form-control number']) }}
        </div>
    </div>
    <div class="col-sm-12 marg-btm">
        <div class="form-group">
            <label>Waluta</label>
            {{ Form::text('currency', '', ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-sm-12 marg-btm">
        <div class="form-group">
            <label>Informacja na kogo FV</label>
            {{ Form::select('for_whom', ['1' => 'FV na LB', '2' => 'FV na oferenta'], 1, ['class' => 'form-control', 'data-buyer' => ($injury->wreck->buyerInfo) ? '1' : '0']) }}
        </div>
    </div>
    <div class="col-sm-12 marg-btm" id="for_whom_info" style="display: none;">
        <div class="form-group">
            <label>Dane oferenta</label>
            {{ Form::textarea('for_whom_info', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-sm-12 marg-btm">
        <div class="form-group">
            <label>Uwagi</label>
            {{ Form::select('remarks', ['pojazd uszkodzony' => 'pojazd uszkodzony', 'pojazd spalony' => 'pojazd spalony'], 1, ['class' => 'form-control']) }}
        </div>
    </div>
</div>

<script>
    $('select[name="for_whom"]').on('change', function(){
        if($(this).data('buyer') == 0) {
            if ($(this).val() == 2) {
                $('#for_whom_info').show();
            } else {
                $('#for_whom_info').hide();
            }
        }
    });
</script>