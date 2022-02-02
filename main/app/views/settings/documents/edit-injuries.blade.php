<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja {{ $documentType->name }}</h4>
</div>
<div class="modal-body">
    <form action="{{ URL::route('settings.documents', array('updateInjuries', $documentType->id)) }}" method="post" class="form-inline"  id="dialog-form">
        {{Form::token()}}
        <h5>Grupy spółek</h5>
        <div class="row">
            @foreach($ownersGroups as $ownersGroup)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="ownersGroups[]" value="{{ $ownersGroup->id }}" @if($documentType->ownersGroups->contains($ownersGroup->id)) checked @endif >
                            {{ $ownersGroup->name }}
                            <span class="btn btn-xs btn-info tips" title="
                            @foreach($ownersGroup->owners as $owner)
                                {{ $owner->name }}
                                    @if($owner->old_name)
                                        ({{ $owner->old_name }})
                                    @endif
                                ;
                            @endforeach
                            ">
                        <i class="fa fa-info"></i>
                    </span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-sm-12 marg-top">
                <div class="form-group">
                    <label>CFM</label>
                    {{ Form::select('cfm', ['0' => 'nie', '1' => 'tak', '2' => 'oba przypadki'], $documentType->cfm, ['class' => 'form-control'] ) }}
                </div>
            </div>
        </div>
        <h5>Dostępność na etapie</h5>
        <div class="row">
            @foreach($steps as $step_id => $step)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="steps[]" value="{{ $step_id }}" @if($documentType->steps->contains($step_id)) checked @endif > {{ $step }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wykonywanie..." id="set">Zapisz</button>
</div>
