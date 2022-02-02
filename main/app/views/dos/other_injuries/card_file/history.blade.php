<div class="tab-pane fade in " id="history">
    <div class="row">
        @if(Auth::user()->can('zlecenia#zarzadzaj'))
            <div class="col-sm-12 marg-btm">
                <p class="btn btn-primary btn-sm modal-open "
                   target="{{ URL::route('dos.other.injuries.add-history', array($injury->id)) }}" data-toggle="modal"
                   data-target="#modal"><i class="fa fa-plus"></i><span> dodaj notkę</span></p>
            </div>
        @endif
    </div>
    @foreach ($history as $k => $v)
        @if($v->history_type_id > 0)
            <p class="clearfix ">
                <strong>{{substr($v->created_at,0,-3)}} - {{$v->user->name}}:</strong>
                {{ $v->history_type->content}}
                <em>
                    @if($v->value == '-1')
                        {{$v->injury_history_content->content}}
                    @else
                        {{$v->value}}
                    @endif
                </em>
            <hr class="short"/>
            </p>
        @endif
    @endforeach
</div>
