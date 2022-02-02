<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Grupy spółek</h4>
</div>
<div class="modal-body">
        <div class="row">
            @foreach($groups as $group)
                <div class="col-sm-12">
                    <h5>{{ $group->name }}</h5>
                    @foreach($group->owners as $owner)
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <i class="fa fa-check-square-o fa-fw" aria-hidden="true"></i> {{ $owner->name }}
                            @if($owner->old_name)
                                ({{ $owner->old_name }})
                            @endif
                            @if($owner->wsdl != '')
                                <span class="label label-info">komunikacja z AS</span>
                            @endif
                        </div>
                    @endforeach
                    <hr class="col-sm-12" />
                </div>
            @endforeach
        </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>
