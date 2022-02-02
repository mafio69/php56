@if(Auth::user()->can('kartoteka_szkody#historia'))
    <div class="tab-pane fade in " id="history">
        <div class="row">
            <div class="col-sm-12 marg-btm">
                <p class="btn btn-primary btn-sm modal-open "
                   target="{{ URL::route('injuries-add-history', array($injury->id)) }}" data-toggle="modal"
                   data-target="#modal"><i class="fa fa-plus"></i><span> dodaj notkę</span></p>
            </div>
        </div>

        <?php foreach ($history as $k => $v) {?>
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
        <?php }?>

        @if($injury->mobileInjury)
            <p class="clearfix ">
                <strong>{{substr($injury->mobileInjury->created_at,0,-3)}} - system:</strong>
                napłynięcie zgłoszenia online
            <hr class="short"/>
            </p>
        @endif
    </div>
@endif
