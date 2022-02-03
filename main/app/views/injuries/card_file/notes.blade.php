<div class="tab-pane fade in " id="notes">
    @if($injury->sap)
        <div class="row">
            <div class="col-sm-12 marg-btm">
                <p class="btn btn-primary btn-sm modal-open "
                   target="{{ URL::route('injuries-add-history', array($injury->id)) }}" data-toggle="modal"
                   data-target="#modal"><i class="fa fa-plus"></i><span> wyślij notatkę</span></p>
            </div>
        </div>

        @foreach($injury->notes()->withTrashed()->get() as $note)
            <p class="clearfix ">

                <strong>
                    <span class="label label-info">{{ $note->source }}</span> |
                    @if($note->trashed())
                        <span class="label label-danger"><i class="fa fa-trash-o fa-fw"></i> {{ $note->deleted_at->format('Y-m-d H:i') }}</span>
                        <del>
                    @endif
                        {{ $note->created_at->format('Y-m-d H:i') }} :
                    @if($note->trashed())
                        </del>
                    @endif
                </strong>

                @if($note->trashed())
                    <del>
                @endif
                    {{ $note->temat}}
                |
                    <em>
                        <label>  nr notatki:</label> {{ $note->nrnotatki }}
                    </em>
                @if($note->user)
                    <br>
                        <span class="small">
                            {{$note->user->name}}
                        </span>
                @endif
                @if($note->trashed())
                    </del>
                @endif
                <hr class="short"/>

            </p>
        @endforeach
    @else
        <h3 class="text-center">Szkoda nie wysłana do SAP</h3>
    @endif
</div>
