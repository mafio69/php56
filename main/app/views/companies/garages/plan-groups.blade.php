<h4 class="text-center lead">Wskaż grupę programu:</h4>
@foreach($plan->groups->chunk(4) as $chunk)
    <div class="row">
        @foreach($chunk as $group)
            <div class="col-sm-3">
                <div class="radio">
                    <label>
                        <input type="radio" name="plan_group_id" value="{{ $group->id }}">
                        {{ $group->name }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>
@endforeach
