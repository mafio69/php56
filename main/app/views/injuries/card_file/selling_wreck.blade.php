@if(Auth::user()->can('kartoteka_szkody#sprzedaz_wraku'))
    <?php
      if(!$injury->wreck){
          InjuryWreck::create(array(
              'injury_id'  =>  $injury->id
          ));
      }
    ?>
    <div class="tab-pane fade in " id="selling_wreck">
        {{ Form::token() }}
        <?php
            $disabled=(
                    ($injury->totalRepair && $injury->totalRepair->alert_receive_confirm != '0000-00-00')
                    || !in_array($injury->step, [30,31,32,33,34,35,36,37])
                    || ($injury->wreck && ($injury->wreck->dok_transfer != '0000-00-00') )
                ) ? true : false;
        ?>
        <div class="row">
            <div class="col-sm-12 col-lg-8">
                <div class="panel panel-primary">
                    @include('injuries.card_file.selling_wreck.wreck_info')
                </div>
                <div class="panel panel-primary" id="buyer_panel"
                @if(!$injury->wreck || in_array($injury->wreck->buyer, ['0' , '4' , '5' , '6']) || $injury->wreck->scrapped)
                    style="display: none;"
                @endif
                >
                    @include('injuries.card_file.selling_wreck.buyer_info')
                </div>
                <div class="panel panel-primary" id="invoice_panel"
                @if(!$injury->wreck || in_array($injury->wreck->buyer, ['0' , '4' , '5' , '6', '7']) || $injury->wreck->scrapped)
                    style="display: none;"
                @endif
                >
                    @include('injuries.card_file.selling_wreck.invoice')
                </div>

                <div class="panel panel-primary" id="scrapped_panel"
                     @if(!$injury->wreck || is_null( $injury->wreck->scrapped ) )
                     style="display: none;"
                        @endif
                >
                    @include('injuries.card_file.selling_wreck.scrapped')
                </div>
            </div>
            <div class="col-sm-12 col-lg-4">
                @include('injuries.card_file.selling_wreck.new-offer')
            </div>
        </div>
    </div>
@endif
