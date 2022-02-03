<div class="tab-pane fade in " id="history">
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
            <hr class="short" />
          </p>
        @endif
        <?php }?>
      </div>