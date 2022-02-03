<div class="row marg-btm" >
    <div class="col-sm-12 ">
        <label>Zakres od:</label>
        <input type="text" name="date_from" date-opt="from" class="form-control  date from
                @if(isset($datepicker_classes))
                    {{ $datepicker_classes }}
                @endif
                "
               @if(isset($datepicker_id_from))
                   id="{{$datepicker_id_from}}"
               @endif
               placeholder="wybierz datę" autocomplete="off" >
</div>
</div>
<div class="row marg-btm" >
    <div class="col-sm-12 ">
        <label>Zakres do:</label>
        <input type="text" name="date_to" date-opt="to" class="form-control  date to
                @if(isset($datepicker_classes))
                    {{ $datepicker_classes }}
                @endif
                "
                @if(isset($datepicker_id_to))
                    id="{{$datepicker_id_to}}"
                @endif
               placeholder="wybierz datę"  autocomplete="off" >
    </div>
</div>

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function(){
            $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd",
                onClose: function( selectedDate ) {
                    if($(this).attr('date-opt') == 'from'){
                        $( $(this).parent().parent().find('input.to') ).datepicker( "option", "minDate", selectedDate );
                    }else
                        $( $(this).parent().parent().find('input.from') ).datepicker( "option", "maxDate", selectedDate );

                    $('.form_submit').button('reset');
                }
            });
        });
    </script>
@stop
