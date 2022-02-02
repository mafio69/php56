<div class="tab-pane fade in " id="documents">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <table class="table table-hover">
                @foreach($documentsTypes as $documentType)
                    <tr>
                        <td>
                        <strong>{{ $documentType->name }}</strong>
                        </td>
                        <td>
                            @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                <p class="btn btn-primary btn-sm modal-open generate_doc"
                                   target="{{ URL::to('insurances/documents/info', [$agreement->id, $documentType->id]) }}"
                                   data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-file-text-o"></i><span> generuj dokument</span>
                                </p>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
