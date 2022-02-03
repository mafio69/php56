@extends('layouts.main')


@section('header')
<?php $vehicle = $injury->vehicle()->first();?>
<?php $owner = $injury->vehicle()->first()->owner()->first();?>
<?php $driver = $injury->driver()->first();?>
<?php $branch = $injury->branch()->first();?>

Kartoteka szkody nr {{$injury->case_nr}}

@if($injury->skip_in_ending_report == 1)
    <i class="fa fa-check-square-o blue"></i>
@endif

@if($injury->if_vip == 1)
    <i class="fa fa-star text-warning tips" title="klient VIP"></i> <small>klient VIP</small>
@endif
<br>
<small>Numer rejestracyjny {{ $vehicle->registration}} / {{ $vehicle->nr_contract }}</small>

@if(Auth::user()->can('kartoteka_szkody#zarejestruj_w_sap') && ! $injury->sap)
    <small>
        |
        <form action="{{ url('injuries/register-sap', [$injury->id]) }}" method="post" target="_blank" style="display: inline;">
            {{ Form::token() }}
            <button class="btn btn-xs btn-primary" type="submit"><i class="fa fa-fw fa-share"></i> zarejestruj w SAP</button>
        </form>
    </small>
@elseif($injury->sap)
    <small>
        <span class="label label-info">SAP ID: {{ $injury->sap->szkodaId }}</span>
    </small>
    |
    <form  action="{{ url('injuries/update-sap', [$injury->id]) }}" target="_blank" method="post" style="display: inline;">
        {{ Form::token() }}
        <button class="btn btn-xs btn-primary" type="submit"><i class="fa fa-fw fa-share"></i> aktualizuj w SAP</button>
    </form>

    @if($injury->invoices()->where('active', 0)->whereNull('injury_note_id')->count() > 0)
        <form action="{{ url('injuries/send-to-sap', [$injury->id]) }}" target="_blank" method="post" style="display: inline;">
            {{ Form::token() }}
            <button class="btn btn-xs btn-info" type="submit"><i class="fa fa-fw fa-send-o"></i> przekaż do SAP <span class="badge">{{ $injury->invoices()->where('active', 0)->whereNull('injury_note_id')->count() }}</span> </button>
        </form>
    @endif
@endif



<div class="pull-right">
  <a href="
  @if(Session::has('prev'))
    {{ Session::get('prev') }}
  @else
    {{ URL::previous() }}
  @endif
  " class="btn btn-default">
      <i class="fa fa-fw fa-arrow-left"></i>
      Powrót
  </a>
</div>
@stop

@section('main')
    {{ Form::token() }}
    @include('injuries.card_file.nav')
  <div class="tab-content">

    @include('injuries.card_file.injury_data')

    @include('injuries.card_file.documentation')

    @include('injuries.card_file.photos')

    @if($injury->vehicle_type == 'VmanageVehicle')
        @include('injuries.card_file.csm')
    @endif

    @include('injuries.card_file.localization')
    @include('injuries.card_file.damage')
    @include('injuries.card_file.history')
    @if(!is_null($documentsTypes) )
        @include('injuries.card_file.gen_docs')
    @endif

    @if(Config::get('webconfig.WEBCONFIG_SETTINGS_bramka_sms') == 1 )
        @include('injuries.card_file.sms')
    @endif

    @if(in_array($injury->step, [30,31,32,33,34,35,36,37]) || $injury->wreck)
        @include('injuries.card_file.selling_wreck')
    @endif

    @if( (in_array($injury->step, [30,31,32,33,34,35,36,37]) && $injury->getDocument(3,15)->first()) || $injury->repairTotal )
        @include('injuries.card_file.repair_total')
    @endif

    @if( $injury->theft)
        @include('injuries.card_file.balance_theft')
    @elseif( $injury->wreck )
        @include('injuries.card_file.balance_wreck')
    @endif

    @if(in_array($injury->step, [40,41,42,43,44,45,46]) || $injury->theft)
        @include('injuries.card_file.theft')
    @endif
    
    @include('injuries.card_file.step_history')

    @if(
        $branch
        &&
            (
                ( $branch->company->groups->contains(1) || ( $branch->company->groups->contains(5) && $injury->vehicle->cfm == 1 ) )
                &&
                ( isset($genDocumentsA[60]) || isset($genDocumentsA[52]) )
            )
        &&
        ! in_array( $injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46, '-7'] )
    )
        @include('injuries.card_file.repair_stages')
    @endif
    @include('injuries.card_file.settlements')
    @include('injuries.card_file.communicator')
    @include('injuries.card_file.notes')
    @include('injuries.card_file.premiums')
    @include('injuries.card_file.tasks')

  </div>



@stop

@section('headerJs')
  @parent
    <script type="text/javascript">
        $.ajax({
            type: "POST",
            url: '{{ url('injuries/info/check-contract-status', [$injury->id]) }}',
            data: '_token=' + $('input[name=_token]').val(),
            assync: false,
            cache: false,
            dataType: 'json',
            beforeSend: function(){
                $('#status-loader').show();
            },
            success: function (data) {
                if(data.status == '200'){
                    if(data.is_active == '0'){
                        $('#current_contract_status').html('<span class="label label-danger " style="font-size: 120%;">' +
                            '                                   <i class="fa fa-exclamation-triangle fa-fw"></i>' +
                                                                data.contract_status +
                                '</span>'
                        );
                    }else{
                        $('#current_contract_status').html(data.contract_status);
                    }
                }

                $('#status-loader').hide();
            }
        });

        var mapa;
        var geocoder = new google.maps.Geocoder();
        var marker ;
        var infowindow = new google.maps.InfoWindow();

        function initialize(slat,slng) {

            var myOptions = {
              zoom: 6,
              scrollwheel: true,
              navigationControl: false,
              mapTypeControl: false,
              center: new google.maps.LatLng(52.528846,17.071874)
            };

          mapa = new google.maps.Map(document.getElementById('map-canvas'), myOptions);

          if(slat != ''  && slng != ''){
            latlng = new google.maps.LatLng(slat,slng);
            mapa.panTo(latlng);
            mapa.setZoom(16);
            placeMarker(latlng);
          }

        };

        function placeMarker(location) {
          marker = new google.maps.Marker({
            position: location,
            draggable:false,
            map: mapa
          });
        }

        function readCommunicator(){
          $.ajax({
            url: "<?php echo  URL::route('chat.checkConversation');?>",
            data: {
              injury_id: "<?php echo $injury->id;?>",
              _token: $('input[name="_token"]').val()
            },
            dataType: "json",
            type: "POST"
          });
        }

        function countChar(){
            var $maxsms = 918;

            var $ograniczenie = new Array(161,307,460,613,766,918); //wartosc musi byc +1

            var $podpis = $("#podpisSMS").html().length;

            var $iloscznakow = $podpis + $("#bodySMS").val().length + 1;

            for(var i = 0; i<6 ; i++)
                if($iloscznakow<$ograniczenie[i])
                    break;

            $("#iloscWiadomosci").html( i+1 );
            $("#iloscZnakow").html($iloscznakow + ' / ' + $maxsms );

            if($iloscznakow > $maxsms){
                alert('Wiadomość nie może zawierać więcej niż '+$maxsms+' znaków');
                $("#bodySMS").attr('value', $("#bodySMS").val().substr(0,($maxsms -$podpis)));
            }
        }

        function calcWreckBalance(){
            $.ajax({
                type: 'POST',
                url: '<?php echo URL::route('injuries.info.wreck.calc_balance', array($injury->id)); ?>',
                cache: false,
                assync:false,
                dataType: 'json',
                data: '_token='+$('input[name="_token"]').val(),
                success: function(data) {
                    if(data.status == 1){
                        $('#balance_result').html(data.balance);
                        $('#balance_result').removeClass();
                        $('#balance_result').addClass('label balance-label').addClass(data.label);
                    }
                }
            });
        }


        $(document).ready(function() {
      	    $('[data-toggle="tooltip"]').tooltip();
            var lat = "<?php echo $injury->lat; ?>";
            var lng = "<?php echo $injury->lng; ?>";

            var hash = window.location.hash;

            calcWreckBalance();

            $('#info_tabs a[href="' + hash + '"]').tab('show');
            if(hash == '#communicator') readCommunicator();
            else if(hash == '#localization'){
                setTimeout(function(){
                    initialize(lat, lng);
                }, 300);
            }

            $('.nav-tabs a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
                
                if(history.pushState) {
                    history.pushState(null, null, e.target.hash);
                }
                else {
                    location.hash = e.target.hash;
                }

                if(e.target.hash == '#communicator') readCommunicator();
                else if(e.target.hash == '#localization'){
                    setTimeout(function(){
                        initialize(lat, lng);
                    }, 300);
                }else if(e.target.hash == '#tasks'){
                    $.ajax({
                        type: 'GET',
                        url: '{{ url('tasks/injury-tasks/'.$injury->id)  }}',
                        cache: false,
                        assync:false,
                        dataType: 'html',
                        success: function(data) {
                            $('#tasks').html(data);
                        }
                    });
                }

            });

            @if(Auth::user()->can('kartoteka_szkody#uszkodzenia#edytuj_uszkodzenia'))
            $('.uszkodzenia_check').change(function(){
                if($(this).is(':checked')){
                    $(this).parent().nextAll('td').children('.check_strona').removeAttr('disabled');
                } else {
                    $(this).parent().nextAll('td').children('.check_strona').attr('disabled', 'disabled');
                }
            }).change();

                $('.uszkodzenia_check').on('click', function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo URL::route('injuries-setDamage', array($injury->id)); ?>',
                        cache: false,
                        assync:false,
                        data: $("#form_damage").serialize(),
                        success: function(data) {
                        }
                    });
                });

                $('.check_strona').change(function(){
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo URL::route('injuries-setDamage', array($injury->id)); ?>',
                        cache: false,
                        assync:false,
                        data: $("#form_damage").serialize(),
                            success: function(data) {
                        }
                    });
                });
            @endif

            $('#modal-sm').on('click', '#del-img', function(){
                $.post(
                    $('#dialog-del-img-form').prop( 'action' ),
                    $('#dialog-del-img-form').serialize()
                    ,
                    function( data ) {
                         $('#image-'+data).remove();
                         $('#modal-sm').modal('hide');
                    },
                    'json'
                );
                return false;
            });

            $('#modal-sm, #modal').on('click', '#del-doc', function(){
                if($("#dialog-del-doc-form").valid()){
                    $.post(
                        $('#dialog-del-doc-form').prop( 'action' ),
                        $('#dialog-del-doc-form').serialize()
                        ,
                        function( data ) {
                            location.reload();
                        },
                        'json'
                    );
                }
                return false;
            });


            $('#modal, #modal-sm, #modal-lg, #modal-xl').on('click', '#set-injury', function(){
                var btn = $(this);
                btn.attr('disabled', 'disabled');
                if($('#dialog-injury-form').valid() ){
                    $(".btn-group").find(".btn.active input").attr('checked', 'checked');
                    $.post(
                        $('#dialog-injury-form').prop( 'action' ),

                        $('#dialog-injury-form').serialize()
                        ,
                        function( data ) {
                            if(data == '0') location.reload();
                            else{
                                $('#modal .modal-body').html( data);
                                btn.attr('disabled',"disabled");
                            }
                        },
                        'json'
                    );
                    return false;
                }else{
                    btn.removeAttr('disabled');
                }
            });

            $('#modal').on('click', '#set-branch', function(){
                btn = $(this);
                btn.attr('disabled', 'disabled');

                if($('#id_warsztat').val() != '' ){

                    $.post(
                        $('#assign-branch-form').prop( 'action' ),

                        $('#assign-branch-form').serialize()
                        ,
                        function( data ) {
                            if(data == '0') location.reload();
                            else{
                              $('#modal .modal-body').html( data);
                              btn.attr('disabled',"disabled");
                            }
                        },
                        'json'
                    );
                    return false;
                }else{
                    alert('Proszę przypisać serwis.');
                    btn.removeAttr('disabled');
                }

            });

                var filesA = new Array();
                Dropzone.options.fileForm = {
                    init: function () {
                        this.on("complete", function (file) {
                            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                                var hrf="<?php echo URL::route('injuries-getDocumentSet');?>";
                                $.post( hrf, { "files": JSON.stringify(filesA), "_token" : $('input[name="_token"]').val() }, function( data ) {
                                    $('#modal-lg .modal-content').html(data);
                                    $('#modal-lg').modal({
                                        keybord:false
                                    }).modal('show');
                                });
                            }
                        });
                    },
                    success: function(file, response){
                        response = tryParseJSON( response );
                        filesA.push(response.id);
                    }
                };

                $('#modal-lg').on('click', '#set_docs', function(){
                    var btn = $(this);
                    if($("#dialog-set-doc-form").valid()){
                        btn.attr('disabled', 'disabled');
                        $.post(
                            $('#dialog-set-doc-form').prop( 'action' ),
                            $('#dialog-set-doc-form').serialize()
                            ,
                            function( data ) {
                                if(data != '0'){
                                    filesA = new Array();
                                    if( data == 4 || data == 3 || data == 6 || data == 2)
                                        window.location.hash = '#settlements';
                                    else
                                        window.location.hash = "#documentation";

                                    $('#modal-lg').modal('hide');

                                    window.location.reload();
                                }else{
                                    $('#modal-lg .modal-body').html( data);
                                }
                            },
                            'json'
                        );
                    }
                    return false;
                });

                Dropzone.options.imgBefore = {
                    acceptedFiles: "image/*",
                    dictInvalidFileType: "Przesłany plik nie jest zdjęciem"
                };

                Dropzone.options.imgInprogress = {
                    acceptedFiles: "image/*",
                    dictInvalidFileType: "Przesłany plik nie jest zdjęciem"
                };

                Dropzone.options.imgAfter = {
                    acceptedFiles: "image/*",
                    dictInvalidFileType: "Przesłany plik nie jest zdjęciem"
                };

                $('#modal-lg').on('click', '#cancel_docs', function(){
                    var btn = $(this);
                    btn.attr('disabled', 'disabled');
                    $.post(
                        "<?php echo URL::route('injuries-setDocumentDel');?>",

                        $('#dialog-set-doc-form').serialize()
                        ,
                        function( data ) {
                            if(data == '0'){
                              filesA = new Array();
                              $('#modal').modal('hide');

                              window.location.hash = "#documentation";

                              window.location.reload();
                            }else{
                              $('#modal .modal-body').html( data);
                            }
                        },
                        'json'
                    );
                    return false;
                });

                $('#modal, #modal-lg').on('click', '#generate-document', function(){
                    var missed = 0;
                    $('#dialog-generate-doc-form input').each(function(){
                        if($(this).val() == '' || $(this).val() == null){
                            missed = 1;
                        }
                    });
                    if(missed == 1){
                        if(confirm('Pozostały niewypełnione pola. Czy mimo to wygenerować dokument?')){
                            $.ajax({
                                type: "POST",
                                url: $('#dialog-generate-doc-form').prop( 'action' ),
                                data: $('#dialog-generate-doc-form').serialize(),
                                assync:false,
                                cache:false,
                                beforeSend: function(){
                                    $('#modal .modal-body').html( 'Trwa generowanie dokumentu. Proszę czekać.');
                                    $('#generate-document').attr('disabled',"disabled");
                                },
                                complete: function( data ) {
                                    window.open(data.responseText,'_blank');
                                    setTimeout(function(){
                                        window.location.hash = "#documentation";
                                        window.location.reload();
                                    }, 500);
                                },
                                dataType: 'json'
                            });
                        }else{

                        }
                    }else{
                        $.ajax({
                            type: "POST",
                            url: $('#dialog-generate-doc-form').prop( 'action' ),
                            data: $('#dialog-generate-doc-form').serialize(),
                            assync:false,
                            cache:false,
                            beforeSend: function(){
                                $('#modal .modal-body').html( 'Trwa generowanie dokumentu. Proszę czekać.');
                                $('#generate-document').attr('disabled',"disabled");
                            },
                            complete: function( data ) {
                                window.open(data.responseText,'_blank');
                                setTimeout(function(){
                                    window.location.hash = "#documentation";
                                    window.location.reload();
                                }, 500);
                            },
                            dataType: 'json'
                        });
                    }

                    return false;
                });

            $('#modal').on('keypress', function(e){
                if(e.which == 13){  //Enter is key 13

                }
            });

            $("#bodySMS").on("keyup", function() {
                countChar();
            });

            $('#template').on('change',function(){
                $('#bodySMS').html($(this).val());
            });

            $('.datepicker').datepicker();

            $('.wreck_alert').on('change', function(){
                var $alert = $(this);
                $.ajax({
                    type: "POST",
                    url: $alert.attr('hrf'),
                    data: '_token='+$('input[name=_token]').val()+'&alert='+$alert.val(),
                    assync:false,
                    cache:false,
                    success: function( data ) {
                        $('#'+data.alert).html(data.message).fadeIn(300).delay(2500).fadeOut(300, function(){
                            $(this).html('');
                        });

                        if(isset(data.appendShowAndHideElement) && data.appendShowAndHideElement != ''){
                            $to_remove = $(data.appendShowAndHideElement).insertAfter( $ele ).fadeIn(300).delay(2500).fadeOut(300);
                            setTimeout(function(){
                                $to_remove.remove()
                            },2800);
                        }

                        if(isset(data.appendClassIdElement) && data.appendClassIdElement != ''){
                            $('#'+data.appendClassIdElement).addClass(data.appendClass);
                            setTimeout(function(){
                                $('#'+data.appendClassIdElement).removeClass(data.appendClass);
                            },2500);
                        }

                        if(isset(data.to_show) && data.to_show != ''){
                            $.each(data.to_show, function(i, item) {
                                $('#'+item).fadeIn(200);
                              });
                        }

                        if(isset(data.to_hide) && data.to_hide != ''){
                            $.each(data.to_hide, function(i, item) {
                                $('#'+item).fadeOut(200);
                            });
                        }

                        if(isset(data.to_enable) && data.to_enable != ''){
                            $.each(data.to_enable, function(i, item) {
                                $('#'+item).removeAttr('disabled');
                            });
                        }
                        if(isset(data.to_disable) && data.to_disable != ''){
                            $.each(data.to_disable, function(i, item) {
                                $('#'+item).attr('disabled', 'disabled');
                            });
                        }
                        if(isset(data.to_clear) && data.to_clear != ''){
                            $.each(data.to_clear, function(i, item) {
                                $('#'+item).html('');
                            });
                        }

                        calcWreckBalance();
                    },
                    dataType: 'json'
                });
            });

            $('.alert_confirmation, .set_acceptation, .rollback_acceptation').on('change', function(){
                var $alert = $(this);
                if($alert.hasClass('with_required'))
                {
                    if(! $("#acceptations_form").validate().element('#'+$alert.data('required')))
                    {
                        return;
                    }
                }
                $.ajax({
                    type: "POST",
                    url: $alert.attr('hrf'),
                    data: '_token='+$('input[name=_token]').val(),
                    assync:false,
                    cache:false,
                    success: function( data ) {
                        $('#'+data.alert).html(data.message).fadeIn(300).delay(2500).fadeOut(300, function(){
                            $(this).html('');
                        });

                        if( !isset(data.active) || data.active == 0 )
                            $alert.parent().addClass('active');

                        if(isset(data.non_active)){
                            if(!Array.isArray(data.non_active)){
                                $('#' + data.non_active).removeClass('active');
                            }
                            else{
                              if(isset(data.non_active)){
                                  $.each(data.non_active, function(i, item) {
                                        $('#' + item).removeClass('active');
                                    });
                              }
                            }

                        }

                        if( !isset(data.enable) || data.enable == 0 ) {
                            $alert.attr('disabled', 'disabled');
                            $alert.parent().attr('disabled', 'disabled');
                        }
                        $alert.parent().removeAttr('data-original-title');

                        $('#'+data.label).html(data.label_content).fadeIn(200);

                        if(isset(data.appendShowAndHideElement) && data.appendShowAndHideElement != ''){
                            var $to_remove = $(data.appendShowAndHideElement).insertAfter( $ele ).fadeIn(300).delay(2500).fadeOut(300);
                            setTimeout(function(){
                                $to_remove.remove()
                            },2800);
                        }

                        if(isset(data.appendClassIdElement) && data.appendClassIdElement != ''){
                            $('#'+data.appendClassIdElement).addClass(data.appendClass);
                            setTimeout(function(){
                                $('#'+data.appendClassIdElement).removeClass(data.appendClass);
                            },2500);
                        }

                        if(isset(data.to_show) && data.to_show != ''){
                            $.each(data.to_show, function(i, item) {
                                $('#'+item).fadeIn(200);
                              });
                        }

                        if(isset(data.to_hide) && data.to_hide != ''){
                            $.each(data.to_hide, function(i, item) {
                                $('#'+item).fadeOut(200);
                            });
                        }

                        if(isset(data.to_enable) && data.to_enable != ''){
                            $.each(data.to_enable, function(i, item) {
                                $('#'+item).removeAttr('disabled');
                            });
                        }
                        if(isset(data.to_disable) && data.to_disable != ''){
                            $.each(data.to_disable, function(i, item) {
                                $('#'+item).attr('disabled', 'disabled');
                            });
                        }

                        if(isset(data.to_set_val) && data.to_set_val != ''){
                            $.each(data.to_set_val, function(i, item) {
                                if( $('#'+i).is("input") )
                                    $('#'+i).val(item);
                                else
                                    $('#'+i).html(item);
                            });
                        }
                        if(isset(data.to_clear) && data.to_clear != ''){
                            $.each(data.to_clear, function(i, item) {
                                $('#'+item).html('');
                            });
                        }

                        if(isset(data.to_deactivate && data.to_deactivate != '')){
                            $.each(data.to_deactivate, function(i, item) {
                                $('#'+item).removeClass('active');
                            });
                        }

                        calcWreckBalance();
                    },
                    dataType: 'json'
                });
            });

            $('.focusout-input').on('focusout', function(){
                var $ele = $(this);
                setTimeout(function(){

                $.ajax({
                    type: "POST",
                    url: $ele.attr('hrf'),
                    data: '_token='+$('input[name=_token]').val()+'&val='+$ele.val()+'&validation='+$ele.attr('validation')+'&right_space='+$ele.attr('right_space'),
                    assync:false,
                    cache:false,
                    success: function( data ) {
                        if(isset(data.alert) && data.alert != '')
                            $('#'+data.alert).html(data.message).fadeIn(300).delay(2500).fadeOut(300, function(){
                                $(this).html('');
                            });

                        if(isset(data.appendShowAndHideElement) && data.appendShowAndHideElement != ''){
                            var $to_remove = $(data.appendShowAndHideElement).insertAfter( $ele ).fadeIn(300).delay(2500).fadeOut(300);
                            setTimeout(function(){
                                $to_remove.remove()
                            },2800);
                        }

                        if(isset(data.appendClassIdElement) && data.appendClassIdElement != ''){
                            $('#'+data.appendClassIdElement).addClass(data.appendClass);
                            setTimeout(function(){
                                $('#'+data.appendClassIdElement).removeClass(data.appendClass);
                            },2500);
                        }

                        if(isset(data.to_show) && data.to_show != ''){
                            $.each(data.to_show, function(i, item) {
                                $('#'+item).fadeIn(200);
                              });
                        }

                        if(isset(data.to_hide) && data.to_hide != ''){
                            $.each(data.to_hide, function(i, item) {
                                $('#'+item).fadeOut(200);
                            });
                        }

                        if(isset(data.to_enable) && data.to_enable != ''){
                            $.each(data.to_enable, function(i, item) {
                                $('#'+item).removeAttr('disabled');
                            });
                        }
                        if(isset(data.to_disable) && data.to_disable != ''){
                            $.each(data.to_disable, function(i, item) {
                                $('#'+item).attr('disabled', 'disabled');
                            });
                        }
                        if(isset(data.to_set_val) && data.to_set_val != ''){
                            $.each(data.to_set_val, function(i, item) {
                                if( $('#'+i).is("input") )
                                    $('#'+i).val(item);
                                else
                                    $('#'+i).html(item);
                            });
                        }

                        if(isset(data.to_change_trigger) && data.to_change_trigger != ''){
                            $.each(data.to_change_trigger, function(i, item) {
                                $('#'+item).trigger('change');
                            });
                        }
                        if(isset(data.to_clear) && data.to_clear != ''){
                            $.each(data.to_clear, function(i, item) {
                                $('#'+item).html('');
                            });
                        }

                        calcWreckBalance();
                    },
                    dataType: 'json'
                })
                },200);
            });

            $('.send_to_dok_date').click(function () {
                var $btn = $(this);
                $.ajax({
                    type: "POST",
                    url: $btn.attr('hrf'),
                    data: '_token='+$('input[name=_token]').val(),
                    assync:false,
                    cache:false,
                    success: function( data ) {
                        $('#'+data.alert).html(data.message).fadeIn(300).delay(2500).fadeOut(300, function(){
                            $(this).html('');
                        });

                        $('#'+data.label).html(data.label_content).fadeIn(200);

                        if(isset(data.appendShowAndHideElement) && data.appendShowAndHideElement != ''){
                            $to_remove = $(data.appendShowAndHideElement).insertAfter( $ele ).fadeIn(300).delay(2500).fadeOut(300);
                            setTimeout(function(){
                                $to_remove.remove()
                            },2800);
                        }

                        if(isset(data.appendClassIdElement) && data.appendClassIdElement != ''){
                            $('#'+data.appendClassIdElement).addClass(data.appendClass);
                            setTimeout(function(){
                                $('#'+data.appendClassIdElement).removeClass(data.appendClass);
                            },2500);
                        }

                        if(isset(data.to_show) && data.to_show != ''){
                            $.each(data.to_show, function(i, item) {
                                $('#'+item).fadeIn(200);
                              });
                        }

                        if(isset(data.to_hide) && data.to_hide != ''){
                            $.each(data.to_hide, function(i, item) {
                                $('#'+item).fadeOut(200);
                            });
                        }

                        if(isset(data.to_enable) && data.to_enable != ''){
                            $.each(data.to_enable, function(i, item) {
                                $('#'+item).removeAttr('disabled');
                            });
                        }
                        if(isset(data.to_disable) && data.to_disable != ''){
                            $.each(data.to_disable, function(i, item) {
                                $('#'+item).attr('disabled', 'disabled');
                            });
                        }
                        if(isset(data.to_clear) && data.to_clear != ''){
                            $.each(data.to_clear, function(i, item) {
                                $('#'+item).html('');
                            });
                        }

                    },
                    dataType: 'json'
                });
            });

            $('[data-toggle="popover"]').popover({ html: true, trigger: 'hover'});

        });     

// injury-data
function refreshInvoiceCompanyData() {
    if (typeof $('#invoices').val() != 'undefined') {
        var branch_id = $('#invoices').val();

        $.ajax({
            type: "GET",
            url: '/injuries/card/load-branch-data',
            data: { branch_id : branch_id},
            dataType: 'json',
            assync: false,
            cache: false,
            success: function (data) {
                if (data.branch) {
                    branch  = data.branch;
                    $('#ic_short_name').text(branch.short_name);
                    $('#ic_adress').text(branch.code + ' ' + branch.city + ', ' + branch.street);
                    $('#ic_tel').text(branch.phone);
                    $('#ic_email').text(branch.email);
                } else {
                    $('#ic_short_name').text('');
                    $('#ic_adress').text('');
                    $('#ic_tel').text('');
                    $('#ic_email').text('');
                }
            }
        });
    }
}

$('#invoices').on('change', function(){
        refreshInvoiceCompanyData();
    });

$(document).ready(function() {
    refreshInvoiceCompanyData();

});
// injury-data
    </script>


@stop
