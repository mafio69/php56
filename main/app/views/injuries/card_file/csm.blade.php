<div class="tab-pane fade" id="csm">
    <div class="row">
        @foreach($csm_types as $id => $name)
            <?php $csm_object = $injury->vehicle->company->csm()->where('vmanage_csm_type_id', $id)->first();?>
            @if($csm_object && $csm_object->content != '' && !is_null($csm_object))
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="panel panel-primary ">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ $name }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ $injury->vehicle->company->csm()->where('vmanage_csm_type_id', $id)->first()->content }}
                    </div>
                </div>
            </div>
                @endif
        @endforeach
    </div>

</div>

