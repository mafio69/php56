<div class="tab-pane fade in " id="selling_wreck">
    {{ Form::token() }}
    <?php
        $disabled=(
            ($injury->totalRepair && $injury->totalRepair->alert_receive_confirm != '0000-00-00')
            || $injury->step != '-5'
            || ($injury->wreck && ($injury->wreck->dok_transfer != '0000-00-00' || $injury->wreck->payment_confirm != '0000-00-00') )
            )?true:false;
    ?>
    <div class="row">
        <div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
            <div class="panel panel-primary">
                @include('injuries.card_file.selling_wreck.wreck_info')
            </div>
            <div class="panel panel-primary" id="buyer_panel"
            @if(!$injury->wreck || $injury->wreck->buyer == '0')
                style="display: none;"
            @endif
            >
                @include('injuries.card_file.selling_wreck.buyer_info')
            </div>
            <div class="panel panel-primary" id="invoice_panel"
            @if(!$injury->wreck || $injury->wreck->buyer == '0')
                style="display: none;"
            @endif
            >
                @include('injuries.card_file.selling_wreck.invoice')
            </div>
        </div>

    </div>
</div>
@section('headerJs')
  @parent

@stop