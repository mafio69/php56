@extends('layouts.main')


@section('header')

Generowanie raportu kart likwidacji szkód

@stop

@section('main')
@include('modules.flash_notification')
<div class="row">
    <div class="col-xs-12 col-sm-10 col-md-8  col-xs-offset-0 col-sm-offset-1 col-md-offset-2 ">
        {{ Form::open(array('url' => URL::route('settings.liquidation_cards', array('generate_report')), 'class' => 'form-horizontal')) }}

            <div class="form-group">
                <h4 class="inline-header"><span>Zakres numerów karty: <input type="checkbox" class="enable_report"></span> </h4>
                <label for="inputNumberFrom" class="col-sm-2 control-label">Od:</label>
                <div class="col-sm-4">
                  <input type="text" name="number_from"  class=" form-control " id="inputNumberFrom" placeholder="wprowadź numer karty"  disabled>
                </div>
                <label for="inputNumberTo" class="col-sm-2 control-label">Do:</label>
                <div class="col-sm-4">
                  <input type="text" name="number_to"  class=" form-control " id="inputNumberTo" placeholder="wprowadź numer karty"  disabled>
                </div>
            </div>
            <div class="form-group">
                <h4 class="inline-header"><span>Zakres dat wystawienia karty: <input type="checkbox" class="enable_report"></span></h4>
                <label for="inputReleaseDateFrom" class="col-sm-2 control-label">Od:</label>
                <div class="col-sm-4">
                  <input type="text" name="releaseDate_from" date-opt="from" class="form-control  date from" id="inputReleaseDateFrom" placeholder="wybierz datę"  disabled>
                </div>
                <label for="inputReleaseDateTo" class="col-sm-2 control-label">Do:</label>
                <div class="col-sm-4">
                  <input type="text" name="releaseDate_to" date-opt="to" class="form-control  date to" id="inputReleaseDateTo" placeholder="wybierz datę"  disabled>
                </div>
            </div>
            <div class="form-group">
                <h4 class="inline-header"><span>Zakres dat ważności karty: <input type="checkbox" class="enable_report"></span></h4>
                <label for="inputExpirationDateFrom" class="col-sm-2 control-label">Od:</label>
                <div class="col-sm-4">
                  <input type="text" name="expirationDate_from" date-opt="from" class="form-control  date from" id="inputExpirationDateFrom" placeholder="wybierz datę"  disabled>
                </div>
                <label for="inputExpirationDateTo" class="col-sm-2 control-label">Do:</label>
                <div class="col-sm-4">
                  <input type="text" name="expirationDate_to" date-opt="to" class="form-control  date to" id="inputExpirationDateTo" placeholder="wybierz datę"  disabled>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-offset-3 col-md-6 marg-top">
                        <button type="submit" id="form_submit" class="btn btn-primary btn-block">Wygeneruj raport</button>
                    </div>
                </div>
            </div>
        {{ Form::close() }}
    </div>
</div>



@stop

@section('headerJs')
	@parent
	<script type="text/javascript">
	    $(document).ready(function() {
            $("form").submit(function(e) {
                var self = this;
                e.preventDefault();
                var btn = $('#form_submit');
                btn.attr('disabled', 'disabled');

                if($("form").valid()){
                    self.submit();
                }else{
                    btn.removeAttr('disabled');
                }

                return false; //is superfluous, but I put it here as a fallback
            });

            $('.enable_report').on('change', function(){
                var $check = $(this);
                if($check.is(":checked")) {
                    $check.parent().parent().parent().find('input[type="text"]').removeAttr('disabled');
                }else{
                    $check.parent().parent().parent().find('input[type="text"]').attr('disabled', 'disabled');
                }
            }).change();

            $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd",
             onClose: function( selectedDate ) {
                if($(this).attr('date-opt') == 'from'){
                    $( $(this).parent().parent().find('input.to') ).datepicker( "option", "minDate", selectedDate );
                }else
                    $( $(this).parent().parent().find('input.from') ).datepicker( "option", "maxDate", selectedDate );
             }
            });
	    });
    </script>

@stop

