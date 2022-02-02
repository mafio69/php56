<div class="tab-pane fade in " id="history">
    <div class="row">
        @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
            <div class="col-sm-12 marg-btm">
                <p class="btn btn-primary btn-sm modal-open " target="{{ URL::to('insurances/info-dialog/history', [$agreement->id]) }}" data-toggle="modal" data-target="#modal" ><i class="fa fa-plus"></i><span> dodaj notkę</span></p>
            </div>
        @endif
    </div>
    @if($agreement->history)
        @foreach ($agreement->history()->with('type', 'user')->orderBy('created_at', 'desc')->get() as $entry)
            <p class="clearfix
            @if($entry->type->warning == 1)
                red
            @endif
            ">
                <strong>{{substr($entry->created_at,0,-3)}} - {{$entry->user->name}}:</strong>
                {{ $entry->type->content}}
                <em>
                    @if($entry->value == '-1')
                        {{$entry->content->content}}
                    @else
                        {{$entry->value}}
                    @endif
                </em>
            <hr class="short" />
            </p>
        @endforeach
    @endif
</div>