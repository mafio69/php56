@if(Auth::user()->can('kartoteka_szkody#generowanie_dokumentow'))
    <div class="tab-pane fade in " id="gen_docs">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <table class="table table-hover">
                    @foreach($documentsTypes as $documentType)
                        @if( in_array($injury->step, $documentType->getAvailabilities() ))
                            @if($documentType->id !=  72 || ($documentType->id ==  72 && $injury->receive_id != 2) )
                                <tr>
                                    <td>
                                        <strong>{{ $documentType->name }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($documentType->if_fee_collection == 1 && $injury->if_doc_fee_enabled)
                                            <i class="fa fa-money fa-2x"></i>
                                        @endif
                                    </td>
                                    <td @if($documentType->id == 72 && (!($injury->receive_id != 0 && $injury->invoicereceives_id != 0)))
                                        data-toggle="tooltip" data-placement="top"
                                        title="Wymagane uzupełnienie pól: odbiór odszkodowania, odbiór faktury"
                                            @endif>
                                        <p class="btn {{$documentType->id>=92 && $documentType->id<=111 || in_array($documentType->id, [114, 115]) ?
                                        'btn-success' :
                                        'btn-primary'}} btn-sm @if($documentType->id==88) modal-open-lg @else modal-open @endif generate_doc"
                                           @if($documentType->conditions ==1)
                                           @if(in_array($documentType->id, array(13)) && $injury->totalRepair)
                                           disabled="disabled"
                                           @endif

                                           @if($documentType->id == 16 && (!$injury->wreck || $injury->wreck->buyer != 2))
                                           disabled="disabled"
                                           @endif

                                           @if($documentType->id == 31 && $injury->receive_id != 2)
                                           disabled="disabled"
                                           @endif

                                           @if($documentType->id == 32 && $injury->receive_id == 2)
                                           disabled="disabled"
                                           @endif
                                           @endif

                                           @if($documentType->id == 72 && (!($injury->receive_id != 0 && $injury->invoicereceives_id != 0)))
                                           disabled="disabled"
                                           @endif

                                           @if($injury->locked_status == 5)
                                           disabled="disabled"
                                           @endif
                                           id="doc_{{$documentType->id}}"
                                           target="{{ URL::route('injuries-generate-docs-info', array($injury->id,
                                           $documentType->id, $documentType->if_fee_collection)) }}"
                                           data-toggle="modal" @if($documentType->id==88) data-target="#modal-lg"
                                           @else data-target="#modal" @endif>
                                            <i class="fa fa-file-text-o"></i><span> generuj dokument</span>
                                        </p>
                                    </td>
                                </tr>
                            @endif
                        @endif
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endif
