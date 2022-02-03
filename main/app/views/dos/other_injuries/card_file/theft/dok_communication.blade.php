<div class="panel-heading">
    Komunikacja z DOK
</div>
<div class="panel-body">
    <div class="row">
        <div class="col-sm-12 ">
            <form role="form" >
                <div class="form-group form-group-sm col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2" id="theft_send_to_dok_date-group">
                    <div class="row">
                        <div type="button" class="btn btn-sm btn-primary tips col-sm-9 send_to_dok_date" id="theft_send_to_dok_date"
                        @if($injury->theft->send_to_dok_date != '0000-00-00 00:00:00')
                            disabled="disabled"
                        @else
                            hrf="{{ URL::route('dos.other.injuries.theft', array('sendToDok', $injury->theft->id)) }}"
                        @endif
                        >
                            przekazano do DOK
                        </div>

                        <span id="alert_theft_send_to_dok_date" class="label label-info col-sm-2 pull-right label-next-to-btn"
                        @if( $injury->theft->send_to_dok_date == '0000-00-00 00:00:00' )
                            style="display: none;"
                        @endif
                        >
                            @if($injury->theft->send_to_dok_date != '0000-00-00 00:00:00' )
                                {{ substr($injury->theft->send_to_dok_date,0,-3) }}
                            @endif
                        </span>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
