<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zwrot faktury</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('injuries/invoice/return', array($invoice->id)) }}" method="post"  id="dialog-injury-form">

        {{Form::token()}}

        @if($invoice->injury_files->category == 3)
        Dokumenty przekazane z FV:
        <table class="table table-hover">
            @foreach($injuryInvoiceForwardDocumentTypes as $dT)
                <tr>
                    <td style="width: 1px; white-space: nowrap">
                        <input type="checkbox" id="test{{$dT->id}}" class="" name="document_types[]" value="{{$dT->id}}"
                                disabled {{in_array($dT->id, $injuryInvoiceForwardDocuments) ? 'checked' : ''}}/>
                    </td>
                    <td style="text-align:left;">
                        <b>{{$dT->name}}</b>
                    </td>
                </tr>
            @endforeach
        </table>
        @endif

        Potwierdź zwrócenie faktury.

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Potwierdź</button>
</div>
