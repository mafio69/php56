<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja umowy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/info-dialog/update-agreement', [$agreement->id]) }}" method="post"  id="dialog-form">
        <fieldset>
            <div class="form-group">
                <label>Nr umowy</label>
                {{ Form::text('nr_contract', $agreement->nr_contract, array('class' => 'form-control required', 'placeholder' => 'nr umowy')) }}
            </div>
            <div class="form-group">
                <label>Ilość miesięcy</label>
                {{ Form::text('months', $agreement->months, array('class' => 'form-control', 'placeholder' => 'ilość miesięcy')) }}
            </div>
            <div class="form-group">
                <label>Rodzaj umowy</label>
                {{ Form::select('leasing_agreement_type_id', $leasingAgreementTypes, $agreement->leasing_agreement_type_id, array('class' => 'form-control')) }}
            </div>
            <div class="form-group">
                <label>Ubezpieczenie od kwoty</label>
                {{ Form::select('net_gross', ['1' => 'netto', '2' => 'brutto'], $agreement->net_gross, array('class' => 'form-control')) }}
            </div>
            <div class="form-group">
                <label>Okres ubezp. od</label>
                {{ Form::text('insurance_from', $agreement->insurance_from, array('class' => 'form-control date', 'placeholder' => 'okres ubezp. od')) }}
            </div>
            <div class="form-group">
                <label>Okres ubezp. do</label>
                {{ Form::text('insurance_to', $agreement->insurance_to, array('class' => 'form-control date', 'placeholder' => 'okres ubezp. do')) }}
            </div>
        </fieldset>
        {{Form::token()}}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary " data-loading-text="trwa zapisywanie..." id="set">Zapisz</button>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,
            changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd"
        });
    });
</script>