<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przypisane grupy do serwisu {{ $company->name }}</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('companies/groups', [$company->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            @foreach($groups as $group_id => $group_name)
                <div class="col-sm-6">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="groups[]" value="{{ $group_id }}" @if($company->groups->contains($group_id)) checked @endif>
                            {{ $group_name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Przypisz</button>
</div>