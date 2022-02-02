@extends('layouts.main')

@include('modules.flash_notification')

@section('header')
<?php $object = $injury->object()->first();?>
<?php $owner = $injury->object()->first()->owner()->first();?>

Kartoteka szkody nr {{$injury->case_nr}} <br><small>Numer umowy leasingowej {{ $object->nr_contract}}</small>

<div class="pull-right">
  <a href="
  @if(Session::has('prev'))
    {{{ Session::get('prev') }}}
  @else
    {{{ URL::previous() }}}
  @endif
  " class="btn btn-default">Powrót</a>
</div>
@stop

@section('main')

    @include('dos.other_injuries-infolinia.card_file.nav')
  <div class="tab-content">
    {{ Form::token() }}
    @include('dos.other_injuries-infolinia.card_file.injury_data')
    @include('dos.other_injuries-infolinia.card_file.documentation')
    @include('dos.other_injuries-infolinia.card_file.communicator')
    @include('dos.other_injuries-infolinia.card_file.history')

  </div>



@stop

@section('headerJs')
  @parent
    <script type="text/javascript">
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
            url: "<?php echo  URL::route('dos.other.chat.checkConversation');?>",
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
            $ograniczenie = new Array(161,307,460,613,766,918); //wartosc musi byc +1

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


        $(document).ready(function() {
            var lat = "<?php echo $injury->lat; ?>";
            var lng = "<?php echo $injury->lng; ?>";

            var hash = window.location.hash;

            readCommunicator();

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
                }

            });

            $('.uszkodzenia_check').change(function(){
                if($(this).is(':checked')){
                    $(this).parent().nextAll('td').children('.check_strona').removeAttr('disabled');
                } else {
                    $(this).parent().nextAll('td').children('.check_strona').attr('disabled', 'disabled');
                }
            }).change();

            $('.modal-open-sm').on('click', function(){
                hrf=$(this).attr('target');
                $('#modal .modal-content').html('');
                $.get( hrf, function( data ) {
                    $('#modal-sm .modal-content').html(data);
                });
            });
            $('.modal-open').on('click', function(){
                hrf=$(this).attr('target');
                $('#modal-sm .modal-content').html('');
                $.get( hrf, function( data ) {
                    $('#modal .modal-content').html(data);
                });
            });

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



            $('#modal, #modal-sm').on('click', '#set-injury', function(){
                btn = $(this);
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



            $('#modal').on('click', '#cancel_docs', function(){
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

            $('#modal').on('click', '#set_docs', function(){
                if($("#dialog-set-doc-form").valid()){
                    $.post(
                        $('#dialog-set-doc-form').prop( 'action' ),

                        $('#dialog-set-doc-form').serialize()
                        ,
                        function( data ) {
                            if(data != '0'){
                                filesA = new Array();


                            if( data == 4 || data == 3 )
                                window.location.hash = '#invoices';
                            else
                                window.location.hash = "#documentation";

                                $('#modal').modal('hide');

                                window.location.reload();
                            }else{
                                $('#modal .modal-body').html( data);
                            }
                        },
                        'json'
                    );
                }
                return false;
            });

            $('#modal').on('click', '#generate-document', function(){
                missed = 0;
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

            countChar();

            $("#bodySMS").on("keyup", function() {
                countChar();
            });

            $('#template').on('change',function(){
                $('#bodySMS').html($(this).val());
            });

            $('.datepicker').datepicker();

            $('.wreck_alert').on('change', function(){
                $alert = $(this);
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

                    },
                    dataType: 'json'
                });
            });

            $('.alert_confirmation, .set_acceptation').on('change', function(){
                $alert = $(this);
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

                        if( !isset(data.enable) || data.enable == 0 )
                            $alert.attr('disabled','disabled');

                        $alert.parent().removeAttr('data-original-title');

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
                                console.log(item);
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

                    },
                    dataType: 'json'
                });
            });

            $('.focusout-input').on('focusout', function(){
                $ele = $(this);
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
                        if(isset(data.to_set_val) && data.to_set_val != ''){
                            $.each(data.to_set_val, function(i, item) {
                                if( $('#'+i).is("input") )
                                    $('#'+i).val(item);
                                else
                                    $('#'+i).html(item);
                            });
                        }

                    },
                    dataType: 'json'
                })
                },250);
            });

            $('.send_to_dok_date').click(function () {
                $btn = $(this);
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
                                console.log(item);
                                $('#'+item).attr('disabled', 'disabled');
                            });
                        }

                    },
                    dataType: 'json'
                });
            });

        });



    </script>

@stop