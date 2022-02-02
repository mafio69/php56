<div class="tab-pane fade in " id="gen_docs">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <table class="table table-hover">
                @foreach($documentsTypes as $documentType)
                    @if( in_array($injury->step, $documentType->getAvailabilities() ))
                        <tr>
                            <td>
                                <strong>{{ $documentType->name }}</strong>
                            </td>
                            <td>
                                @if(Auth::user()->can('zlecenia#zarzadzaj'))
                                    <p class="btn btn-primary btn-sm modal-open generate_doc"
                                       @if($injury->locked_status == 5)
                                       disabled="disabled"
                                       @endif
                                       id="doc_{{$documentType->id}}"
                                       target="{{ URL::route('dos.other.injuries.generate.docs', array($injury->id, $documentType->id)) }}"
                                       data-toggle="modal" data-target="#modal">
                                        <i class="fa fa-file-text-o"></i><span> generuj dokument</span>
                                    </p>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>
    </div>
</div>
