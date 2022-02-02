<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Wycofywanie umowy nr {{ $agreement->nr_contract}}</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/manage-dialog/move-to-withdraw', [$agreement->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label>Przyczyna wycofania:</label>
                    {{ Form::select('withdraw_reason_id', Config::get('definition.withdrawReasons'), null, ['id' => 'withdraw_reason_id', 'class' => 'form-control'])}}
                </div>
            </div>
            <div class="row" id="withdraw_reason">
                <div class="col-sm-12">
                    <label>Podaj przyczynę:</label>
                    {{ Form::textarea('withdraw_reason','', ['class' => 'form-control'])}}
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wycofywanie..." id="set">Wycofaj</button>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#withdraw_reason').hide();
        $('#withdraw_reason_id').on('change', function(){
            if($(this).val() == 3)
                $('#withdraw_reason').show();
            else
                $('#withdraw_reason').hide();
        });
    });

</script>

