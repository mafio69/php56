<div class="tab-pane fade in " id="repair_total">
    {{ Form::token() }}
    <div class="row">
        <div class="col-sm-12 col-md-10 col-md-offset-1 ">
            <div class="panel panel-primary">
                @include('injuries.card_file.repair_total.base_info')

            </div>
            <div class="panel panel-primary" id="doc_acceptation_panel"
            @if($injury->totalRepair->repair_agreement_date == '0000-00-00')
                style="display: none;"
            @endif
            >
                @include('injuries.card_file.repair_total.acceptations')
            </div>
            <div class="panel panel-primary" id="dok_communication_panel"
            @if(!$injury->totalRepair->hasAllAcceptations())
                style="display: none;"
            @endif
            >
                @include('injuries.card_file.repair_total.dok_communication')
            </div>
        </div>

    </div>
</div>
@section('headerJs')
  @parent

@stop