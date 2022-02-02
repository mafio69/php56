@if(Auth::user()->can('kartoteka_szkody#kradziez'))
    <div class="tab-pane fade in " id="theft">
        {{ Form::token() }}
        <?php $disabled_theft =(  !in_array($injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46]) ) ? true : false;  ?>

        <div class="row">
            <div class="col-sm-12 ">
                @if( $injury->theft )
                    <div class="panel panel-primary">
                        @include('injuries.card_file.theft.base_info')
                    </div>
                    <div class="panel panel-primary" id="theft_doc_acceptation_panel"
                    @if($injury->theft->police_memo_confirm == '0000-00-00')
                        style="display: none;"
                    @endif
                    >
                        @include('injuries.card_file.theft.acceptations')
                    </div>
                    <div class="panel panel-primary" id="handling_panel"
                    @if(!$injury->theft->hasAllAcceptations())
                        style="display: none;"
                    @endif
                    >
                        @include('injuries.card_file.theft.handling')
                    </div>
                    @if(! $injury->theft->punishable)
                        <div class="panel panel-primary" id="theft_dok_communication_panel"
                        @if( ($injury->theft->compensation_payment_confirm == '0000-00-00' &&  ! $injury->theft->compensation_payment_deny) || ( $injury->injuryPolicy->gap == 1 && $injury->theft->gap_confirm == '0000-00-00') )
                            style="display: none;"
                        @endif
                        >
                            @include('injuries.card_file.theft.dok_communication')
                        </div>
                    @endif

                @elseif(!$disabled_theft)
                    {{ Form::open(array('url' => URL::route('injuries.info.theft.startProcessing', array($injury->id)), 'method' => 'post')) }}
                        <button type="submit" class="btn btn-primary btn-sm btn-block marg-top">rozpocznij procesowanie kradzieży</button>
                    {{ Form::close() }}
                @endif
            </div>

        </div>
    </div>
@endif
