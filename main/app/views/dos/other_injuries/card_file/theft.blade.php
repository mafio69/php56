<div class="tab-pane fade in " id="theft">
    {{ Form::token() }}
    <?php $disabled_theft =(  $injury->step != '-9' ) ? true : false;  ?>

    @if(Auth::user()->can('zlecenia#zarzadzaj'))
        <div class="row">
            <div class="col-sm-12 col-md-10 col-md-offset-1">
                @if( $injury->theft )
                    <div class="panel panel-primary">
                        @include('dos.other_injuries.card_file.theft.base_info')
                    </div>
                    <div class="panel panel-primary" id="theft_doc_acceptation_panel"
                    @if($injury->theft->send_zu_confirm == '0000-00-00')
                        style="display: none;"
                    @endif
                    >
                        @include('dos.other_injuries.card_file.theft.acceptations')
                    </div>
                    <div class="panel panel-primary" id="handling_panel"
                    @if(!$injury->theft->hasAllAcceptations())
                        style="display: none;"
                    @endif
                    >
                        @include('dos.other_injuries.card_file.theft.handling')
                    </div>
                    <div class="panel panel-primary" id="theft_dok_communication_panel"
                    @if($injury->theft->compensation_payment_confirm == '0000-00-00' || ( $injury->object->gap == 1 && $injury->theft->gap_confirm == '0000-00-00') )
                        style="display: none;"
                    @endif
                    >
                        @include('dos.other_injuries.card_file.theft.dok_communication')
                    </div>

                @elseif(!$disabled_theft)
                    {{ Form::open(array('url' => URL::route('dos.other.injuries.theft', array('startProcessing', $injury->id)), 'method' => 'post')) }}
                        <button type="submit" class="btn btn-primary btn-sm btn-block marg-top">rozpocznij procesowanie kradzieży</button>
                    {{ Form::close() }}
                @endif
            </div>

        </div>
    @endif
</div>
@section('headerJs')
  @parent

@stop
