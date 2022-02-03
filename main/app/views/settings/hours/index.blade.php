@extends('layouts.main')

@section('header')

Zarządzanie godzinami pracy pracowników

@stop

@section('main')

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li class="active"><a href="#days" role="tab" data-toggle="tab">Dni tygodnia</a></li>
    <li><a href="#holidays" role="tab" data-toggle="tab">Kalendarz świąt</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">



    <div class="tab-pane active" id="days">
        <table class="table table-auto  table-hover " >
            <thead>
                <th >Dzień tygodnia</th>
                <th >Od</th>
                <th>Do</th>
                <th ></th>
            </thead>

            @foreach ($work_hours as $k => $v)
                <tr class="odd gradeX">
                    <td>{{ $v -> name }}</td>
                    <Td>
                        @if($v->free == 1)
                        <i class="text-danger">wolne</i>
                        @else
                        {{ substr($v -> work_from, 0 , -3) }}
                        @endif
                    </td>
                    <Td>
                        @if($v->free == 1)
                        <i class="text-danger">wolne</i>
                        @else
                        {{ substr($v -> work_to, 0 , -3) }}
                        @endif
                    </td>
                    <td>
                        <button target="{{ URL::route('settings.hours.edit', array($v->id)) }}" class="btn btn-warning modal-open" data-toggle="modal" data-target="#modal">edytuj</button>
                    </td>
                </tr>
            @endforeach

        </table>
    </div>

    <div class="tab-pane " id="holidays">
        <div class="calendar" >
            <div class="custom-calendar-wrap">
                <div id="custom-inner" class="custom-inner">
                    <div class="custom-header clearfix">
                        <nav>
                            <span id="custom-prev" class="custom-prev"></span>
                            <span id="custom-next" class="custom-next"></span>
                        </nav>
                        <h2 id="custom-month" class="custom-month"></h2>
                        <h3 id="custom-year" class="custom-year"></h3>
                    </div>
                    <div id="calendar" class="fc-calendar-container"></div>
                </div>
            </div>
        </div>
    </div>

</div>




<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>

@stop


@section('headerJs')
@parent
<script type="text/javascript">
    var caldata = null;
    $.ajax({
        type: "GET",
        url: "<?php echo URL::route('settings.hours.holidays.get');?>",
        assync:false,
        cache:false,
        dataType: 'json',
        success: function( data ) {
            caldata = data;

            var transEndEventNames = {
                    'WebkitTransition' : 'webkitTransitionEnd',
                    'MozTransition' : 'transitionend',
                    'OTransition' : 'oTransitionEnd',
                    'msTransition' : 'MSTransitionEnd',
                    'transition' : 'transitionend'
                },
                transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
                $wrapper = $( '#custom-inner' ),
                $calendar = $( '#calendar' ),
                cal = $calendar.calendario( {
                    onDayClick : function( $el, $contentEl, dateProperties ) {
                        if($el.hasClass('fc-content')){
                            unregisterHoliday( dateProperties );
                            $el.removeClass('fc-content');
                        }else{
                            registerHoliday( dateProperties );
                            $el.addClass('fc-content');
                        }
                    },
                    caldata: caldata,
                    displayWeekAbbr : true
                } ),

                $month = $( '#custom-month' ).html( cal.getMonthName() ),
                $year = $( '#custom-year' ).html( cal.getYear() );

            $( '#custom-next' ).on( 'click', function() {
                cal.gotoNextMonth( updateMonthYear );
            } );
            $( '#custom-prev' ).on( 'click', function() {
                cal.gotoPreviousMonth( updateMonthYear );
            } );

            function updateMonthYear() {
                $month.html( cal.getMonthName() );
                $year.html( cal.getYear() );
            }
        }
    });

    function registerHoliday( dateProperties ) {
        $.ajax({
            type: "POST",
            url: "<?php echo URL::route('settings.hours.holidays.register');?>",
            data: { day: dateProperties.day, month: dateProperties.month, year:dateProperties.year},
            assync:false,
            cache:false,
            success: function( data ) {

            }
        });
    }

    function unregisterHoliday( dateProperties ) {
        $.ajax({
            type: "POST",
            url: "<?php echo URL::route('settings.hours.holidays.unregister');?>",
            data: { day: dateProperties.day, month: dateProperties.month, year:dateProperties.year},
            assync:false,
            cache:false,
            success: function( data ) {

            }
        });
    }


    $(document).ready(function() {
        var hash = window.location.hash;
        $('.nav-tabs a[href="' + hash + '"]').tab('show');
        $('.nav-tabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');

            if(history.pushState) {
                history.pushState(null, null, e.target.hash);
            }
            else {
                location.hash = e.target.hash;
            }
        });

        $('.table').on('click', '.modal-open', function(){
            $.ajax({
                type: "GET",
                url: $(this).attr('target'),
                assync:false,
                cache:false,
                success: function( data ) {
                    $('#modal .modal-content').html(data);
                },
            });
        });

        $('#modal').on('click', '#save', function(){

            if($('#edit-form').valid() ){
                $.ajax({
                    type: "POST",
                    url: $('#edit-form').prop( 'action' ),
                    data: $('#edit-form').serialize(),
                    assync:false,
                    cache:false,
                    success: function( data ) {
                        location.reload();
                    },
                });

                return false;
            }

        });

    });
</script>

@stop