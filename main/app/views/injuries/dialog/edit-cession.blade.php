<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Kwoty do cesji </h4>
</div>
<div class="modal-body" style="overflow:hidden;">
        <form action="{{ URL::route('injuries-updateCessionAmounts', array($cessionAmount->id)) }}" method="post" id="dialog-form">
            {{Form::token()}}
    <div class="form-group">
{{--        <div class="row">--}}
{{--            <div class="col-sm-12 marg-btm">--}}
{{--                <label>Kwota wypłaconego odszkodowania</label>--}}
{{--                {{ Form::text('paid_amount', $cessionAmount->paid_amount, array('class' => 'form-control required', 'autocomlete' => 'off', 'id' => 'amountInput')) }}--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="row marg-btm">--}}
{{--            <div class="col-sm-12 marg-btm">--}}
{{--                {{ Form::select('net_gross', Config::get('definition.compensationsNetGross'), $cessionAmount->net_gross, ['class' => 'form-control', 'id' => 'amountType']) }}--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="row">--}}
{{--            <div class="col-sm-12 marg-btm">--}}
{{--                <label>Kwota z FV</label>--}}
{{--                {{ Form::text('fv_amount', $cessionAmount->fv_amount, array('class' => 'form-control required', 'autocomlete' => 'off', 'id' => 'fvAmountInput')) }}--}}
{{--            </div>--}}
{{--        </div>--}}

        <div class="row marg-btm">
            <div class="col-md-4">
                <label>Kwota wypłaconego odszkodowania</label>
                {{ Form::text('paid_amount', $cessionAmount->paid_amount, array('class' => 'form-control required', 'autocomlete' => 'off', 'id' => 'amountInput')) }}
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                {{ Form::select('net_gross', Config::get('definition.compensationsNetGross'), $cessionAmount->net_gross, ['class' => 'form-control', 'id' => 'amountType']) }}
            </div>
            <div class="col-md-4">
                <label>Kwota z FV</label>
                {{ Form::text('fv_amount', $cessionAmount->fv_amount, array('class' => 'form-control required', 'autocomlete' => 'off', 'id' => 'fvAmountInput')) }}
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <label id="differenceField" class="pull-right">Aby wyznaczyć różnicę proszę poprawnie wprowadzić kwoty</label>
            </div>
        </div>
    </div>
        </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set"> Zapisz</button>
</div>

<style>
    .modal-lg {
        position: fixed;
        bottom:0px;
        right:0px;
        margin: 0;
        padding: 5px;
    }
</style>

<script type="text/javascript">

    $(document).ready(function () {
        var paidAmountInput = document.getElementById('amountInput');
        var fvAmountInput = document.getElementById('fvAmountInput');
        var differenceField = document.getElementById('differenceField');

        var paidAmount = document.getElementById('amountInput').value.replace(",", ".");
        var fvAmount = document.getElementById('fvAmountInput').value.replace(",", ".");

        checkIfInputsValid();

        paidAmountInput.addEventListener("focusout", function () {
            if (!isNaN(paidAmountInput.value.replace(",", "."))){
                paidAmount = parseFloat(paidAmountInput.value.replace(",", ".")).toFixed(2);
                paidAmountInput.value = paidAmount;
            }
            checkIfInputsValid()
        });

        fvAmountInput.addEventListener("focusout", function () {
            if (!isNaN(fvAmountInput.value.replace(",", "."))){
                fvAmount = parseFloat(fvAmountInput.value.replace(",", ".")).toFixed(2);
                fvAmountInput.value = fvAmount;
            }
            checkIfInputsValid()
        });

        function checkIfInputsValid() {
                if (!isNaN(paidAmount) && !isNaN(fvAmount) && paidAmount !== '' && fvAmount !== ''
                    && paidAmount !== null && fvAmount !== null){
                    var differenceVal = parseFloat(+fvAmount - +paidAmount).toFixed(2);
                    differenceField.innerHTML = 'Różnica: ' + differenceVal;
                } else differenceField.innerHTML = 'Aby wyznaczyć różnicę proszę poprawnie wprowadzić kwoty';
        }
    });
</script>
