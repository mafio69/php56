<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przekazanie faktury</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('injuries/invoice/forward-again', array($invoice->id)) }}" method="post"  id="dialog-injury-form">

        {{Form::token()}}

        @if($invoice->injury_files->category == 3)
        Dokumenty przekazane z FV:
        <table class="table table-hover">
            @foreach($injuryInvoiceForwardDocumentTypes as $dT)
            @if($dT->id != 1 && $dT->id != 3)
                <tr>
                    <td style="width: 1px; white-space: nowrap">
                        <input type="checkbox" id="test{{$dT->id}}" class="" name="document_types[]" value="{{$dT->id}}"
                               {{in_array($dT->id, $injuryInvoiceForwardDocuments) ? 'checked' : ''}}/>
                    </td>
                    <td style="text-align:left;">
                        <b>{{$dT->name}}</b>
                        @if($dT->id == 2)
                            <div id="compensations" {{in_array($dT->id, $injuryInvoiceForwardDocuments) ? "" : "style=display: none;"}}>
                                <table class="table" style="margin: 0px">
                                    @foreach($compensations as $compensation)
                                            <tr>
                                                <td style="width: 1px; white-space: nowrap">
                                                    <input type="checkbox" id="test{{$compensation->id}}" class="" name="compensations[]" value="{{$compensation->id}}"
                                                    {{in_array($compensation->id, $invoiceCompensations) ? 'checked' : ''}}
                                                    />
                                                </td>
                                                <td style="text-align:left;">
                                                <b>
                                                    @if($compensation->injury_compensation_decision_type_id == 7)
                                                        <?php  $compensation->compensation = abs($compensation->compensation) * -1; ?>
                                                    @endif
                                                    {{$compensation->date_decision}} na kwotę {{number_format(checkIfEmpty($compensation->compensation, null, 0), 2, ",", " ") }} zł
                                                </b>
                                                </td>
                                            </tr>
                                    @endforeach
                                </table>
                            </div>
                        @endif
                    </td>
                </tr>
            @endif
            @endforeach
        </table>
        @endif

        Potwierdź przekazanie ponowne faktury.

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Potwierdź</button>
</div>

<script>

$('#test2').change(function () {
        if(this.checked) $('#compensations').show();
        else $('#compensations').hide();
    })

</script>
