
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Generowanie dokumentu certyfikatur Hestii (bez danych Korzystającego)</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/documents/generate-hestia-certificate-no-client', [$policy->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}

        <div class="row">
            <div class="col-sm-12 marg-btm">
                <label >Miejsce ubezpieczenia:</label>
                {{ Form::text('place', '', array('class' => 'form-control')) }}
            </div>
            <div class="col-sm-12 marg-btm">
                <label >Opcje dodatkowe:</label>
                {{ Form::text('extra_options', '', array('class' => 'form-control')) }}
            </div>
        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Wygeneruj</button>
</div>
<script>
    $('.datepicker').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd"});
</script>