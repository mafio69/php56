@extends('layouts.main')

@section('header')
<?php $vehicle = $notification->vehicle()->first();?>
<?php $owner = $notification->vehicle()->first()->owner()->first();?>

Kartoteka zgłoszenia nr {{$notification->case_nr}} <br><small>Numer rejestracyjny {{ $vehicle->registration}}</small>

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

@section('sub-header')
<ol class="breadcrumb processes">
    @foreach($procesess as $process)
    <li
        class="
  @if($process == end($procesess))
    active
  @endif

  pull-left
  "
        >{{ $process }}</li>
    @endforeach
    <li class="priority pull-right">
        @if($notification->priority == 2)
            <span class="label label-danger" style="font-size:12px"><i class="fa fa-bolt"></i> zgłoszenie priorytetowe</span>
        @else
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-danger btn-xs priority
                    @if($notification->priority == 1)
                        active
                    @endif
                ">
                    <input type="checkbox" name="priority" id="priority" value="1"
                    @if($notification->priority == 1)
                        checked
                    @endif
                    > <i class="fa fa-bolt"></i> zgłoszenie priorytetowe
                </label>
            </div>
        @endif
    </li>
</ol>

@stop

@section('main')

  
  <ul class="nav nav-tabs" id="info_tabs">
      <li class="active"><a href="#communicator" data-toggle="tab">Komunikator</a></li>
      <li><a href="#notification-data" data-toggle="tab">Dane umowy</a></li>
      <li><a href="#documentation" data-toggle="tab">Dokumentacja</a></li>
      <li><a href="#gen_docs" data-toggle="tab">Generowanie dokumentów</a></li>
      <li><a href="#history" data-toggle="tab">Historia</a></li>
    
  </ul>  
  <div class="tab-content">
      <div class="tab-pane fade in " id="notification-data">
          <div class="row">
            <div class="col-sm-6 col-md-4 col-lg-3 item-m">
              <div class="panel panel-default small">
                 <div class="panel-heading 
                 @if(
                    str_contains(mb_strtoupper($vehicle->contract_status, 'UTF-8'), 'AKTYWNA')
                 )
                 bg-success
                 @else
                 bg-danger
                 @endif
                 ">Status umowy</div>
                 <table class="table">
                  <tr>
                    <td><label>Status:</label></td>
                    <Td>{{ $vehicle->contract_status }}</td>
                  </tr>
                  <tr>
                    <td><label>Data ważności:</label></td>
                    <td>{{ $vehicle->end_leasing }}</td>
                  </tr>
                  <tr>
                    <td><label>Saldo:</label></td>
                    <td></td>
                  </tr>
                 </table>
              </div>
            </div>


            
            <div class="col-sm-6 col-md-4  col-lg-3 item-m">
              <div class="panel panel-default small">
                 <div class="panel-heading ">Dane pojazdu</div>
                 
                 <table class="table">
                  <tr>
                    <td><label>Rejestracja:</label></td>
                    <Td>{{ $vehicle->registration }}</td>
                  </tr>
                  <tr>
                    <td><label>Nr umowy leasingowej:</label></td>
                    <td>{{ $vehicle->nr_contract }}</td>
                  </tr>
                  <tr>
                    <td><label>VIN:</label></td>
                    <td>{{ $vehicle->VIN }}</td>
                  </tr>
                  <tr>
                    <td><label>Marka i model:</label></td>
                    <td>{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                  </tr>
                  <tr>
                    <td><label>Silnik:</label></td>
                    <td>{{ $vehicle->engine }}</td>
                  </tr>
                  <tr>
                    <td><label>Rok produkcji:</label></td>
                    <td>{{ $vehicle->year_production }}</td>
                  </tr>
                  <tr>
                    <td><label>Data pierwszej rejestracji:</label></td>
                    <td>{{ $vehicle->first_registration }}</td>
                  </tr>
                  <tr>
                    <td><label>Przebieg:</label></td>
                    <td>{{ $vehicle->mileage }}</td>
                  </tr>
                 </table>
              </div>
            </div>

            <div class="col-sm-6 col-md-4 col-lg-3 item-m">
              <div class="panel panel-default small">
                 <div class="panel-heading ">Dane właściciela</div>
                 
                 <table class="table">
                  <tr>
                    <td><label>Nazwa:</label></td>
                    <Td>{{ $owner->name }}</td>
                  </tr>
                 @if($owner->old_name)
                     <tr>
                         <td><label>Dawna nazwa:</label></td>
                         <Td>{{ $owner->old_name }}</td>
                     </tr>
                 @endif
                  <tr>
                    <td><label>Kod pocztowy:</label></td>
                    <td>{{ $owner->post }}</td>
                  </tr>
                  <tr>
                    <td><label>Miato:</label></td>
                    <td>{{ $owner->city }}</td>
                  </tr>
                  <tr>
                    <td><label>Ulica:</label></td>
                    <td>{{ $owner->street }}</td>
                  </tr>
                 </table>
              </div>
            </div>

            <div class="col-sm-6 col-md-4 col-lg-3 item-m">
              <div class="panel panel-default small">
                 <div class="panel-heading ">Dane klienta</div>
                 <?php $client = $notification->vehicle()->first()->client()->first();?>
                 <table class="table">
                  <tr>
                    <td><label>Nazwa:</label></td>
                    <Td>{{ $client->name }}</td>
                  </tr>
                  <tr>
                    <td><label>NIP:</label></td>
                    <Td>{{ $client->NIP }}</td>
                  </tr>
                  <tr>
                    <td><label>Regon:</label></td>
                    <Td>{{ $client->REGON }}</td>
                  </tr>
                     <tr>
                         <td><label>Kod klienta:</label></td>
                         <Td>{{ $client->firmID }}</td>
                     </tr>
                  <tr>
                    <Td colspan="2"><span class="sm-title">Adres rejestrowy:</span></td>
                  </tr>
                  <tr>
                    <td><label>Kod pocztowy:</label></td>
                    <td>{{ $client->registry_post }}</td>
                  </tr>
                  <tr>
                    <td><label>Miato:</label></td>
                    <td>{{ $client->registry_city }}</td>
                  </tr>
                  <tr>
                    <td><label>Ulica:</label></td>
                    <td>{{ $client->registry_street }}</td>
                  </tr>
                  <tr>
                    <Td colspan="2">
                      <span class="sm-title">Adres kontaktowy:</span>
                    </td>
                  </tr>
                  <tr>
                    <td><label>Kod pocztowy:</label></td>
                    <td>{{ $client->correspond_post }}</td>
                  </tr>
                  <tr>
                    <td><label>Miato:</label></td>
                    <td>{{ $client->correspond_city }}</td>
                  </tr>
                  <tr>
                    <td><label>Ulica:</label></td>
                    <td>{{ $client->correspond_street }}</td>
                  </tr>
                  <tr>
                    <td><label>Telefon:</label></td>
                    <td>{{ $client->phone }}</td>
                  </tr>
                  <tr>
                    <td><label>Email:</label></td>
                    <td>{{ $client->email }}</td>
                  </tr>
                 </table>
              </div>
            </div>

            <div class="col-sm-6 col-md-4 col-lg-3 item-m">
              <div class="panel panel-default small">
                 <div class="panel-heading overflow">
                  <span class="pull-left">Dane zgłaszającego</span>
                 </div>
                 <table class="table">
                  <tr>
                    <td><label>Imię:</label></td>
                    <td>{{ $notification->notifier_name }}</td>
                  </tr>
                  <tr>
                    <td><label>Nazwisko:</label></td>
                    <Td>{{ $notification->notifier_surname }}</td>
                  </tr>
                  
                  <tr>
                    <td><label>Telefon:</label></td>
                    <td>{{ $notification->notifier_phone }}</td>
                  </tr>
                  <tr>
                    <td><label>Email:</label></td>
                    <td>{{ $notification->notifier_email }}</td>
                  </tr>
                  
                 </table>
              </div>
            </div>
        </div>
        <div class="row">
            
              <div class="col-sm-6  item-m">
                <div class="panel panel-default small">
                   <div class="panel-heading ">Informacja wewnętrzna:
                   </div>
                   <table class="table">
                    <?php if($notification->info != 0){?>
                    <tr>
                      <td>{{ $info->content }}</td>
                    </tr>
                    <?php }?>
                   </table>
                </div>
              </div>
          </div>

          
      </div>
      
      <div class="tab-pane fade in " id="documentation">
        <div class="row marg-btm">   
          <div class="col-sm-12">           
          {{ Form::open( [ 'url' => URL::route('dok.notifications.postDocument', array($notification->id)) , 'class' => 'dropzone fileUploads' , 'id' => 'fileForm', 'files'=>true ] ) }}
            <div class="fallback">
                <input name="file" type="file" multiple />
            </div>
          {{ Form::close() }} 
          </div>
        </div>
        <div class="row">
          <div class="col-sm-8 col-lg-6 col-sm-offset-2 col-lg-offset-3">
          <table class="table table-hover" >
            @foreach($documents as $k => $v)
              <tr>
                <td width="10px">{{++$k}}.</td>
                <td width="50px">
                  @if($v->type == 2)
                    <a href="{{ URL::route('dok.notifications.downloadDoc', array($v->id)) }}" target="_blank" class="fa fa-floppy-o blue pointer md-ico"></a>
                  @else
                    <a href="{{ URL::route('dok.notifications.downloadGenerateDoc', array($v->id)) }}" target="_blank" class="fa fa-floppy-o blue pointer md-ico"></a>
                  @endif
                </td>
                <td>
                  @if($v->type == 2)                  
                  {{ Config::get('definition.dokFileCategory.'.$v->category) }}<br>
                  <i>{{ $v->name }}</i>
                  @else
                  {{ Config::get('definition.dokDocumentCategory.'.$v->category) }}
                    @if($v->name != '')
                      <br>
                      <i>{{ $v->name }}</i>
                    @endif
                  @endif
                </td>
                <Td>
                  {{ $v->user->name }}
                </td>
                <Td>
                  {{substr($v->created_at, 0, -3)}}
                </td>
                <Td>
                    @if($v->type == 2)
                      <button type="button" class="btn btn-danger modal-open-sm" target="{{ URL::route('dok.notifications.getDelDoc', array($v->id)) }}"  data-toggle="modal" data-target="#modal-sm">usuń</button>
                    @else
                      <button type="button" class="btn btn-danger modal-open" target="{{ URL::route('dok.notifications.getDelDocConf', array($v->id)) }}"  data-toggle="modal" data-target="#modal">usuń</button>
                    @endif
                </td>
              </tr>
            @endforeach
          </table>
          </div>
        </div>
      </div>
      
      
      <div class="tab-pane fade in " id="gen_docs">
        <div class="row">
          <div class="col-sm-6 col-sm-offset-3">
          <table class="table table-hover" >
          @foreach(Config::get('definition.dokDocumentCategory') as $k =>$v)
              <tr>
                <td >
                  <strong>{{$v}}</strong>
                </td>
                
                <Td>
                  <p class="btn btn-primary btn-sm modal-open generate_doc" target="{{ URL::route('injuries-generate-docs-info', array($notification->id, $k)) }}" data-toggle="modal" data-target="#modal" ><i class="fa fa-file-text-o"></i><span> generuj dokument</span></p></div>
                </td>
              </tr>
          @endforeach   
          </table>
          </div>     
        </div>
      </div>

      <div class="tab-pane fade in active" id="communicator">
        
        <div class="panel">
          <div class="panel-body">
            <div class="clearfix">
              <h3 class="media-heading"><small>
                <span class="label label-primary">DOK</span>
                <span class="label label-success">Infolinia</span>
                
                </small>
                <div class="pull-right">
                  <span class="btn btn-warning btn-sm create-chat modal-open" target="{{ URL::route('dok.notifications.chat.create', array($notification->id)) }}" data-toggle="modal" data-target="#modal">
                    <i class="fa fa-comment-o"></i> Dodaj temat/zadanie
                  </span>
                </div>
              </h3>

              
            </div>          
            <ul class="timeline">
              <?php $lp = 0;?>
              @foreach($chat as $k => $conversation)
                <?php $status = get_receivers($conversation->status); ?>
                @if($status[get_chat_group()-1] == 1)
                  
                  <li
                  @if($lp % 2 == 1)
                    class="timeline-inverted"
                  @endif
                  >
                    <div class="timeline-badge 
                    @if($conversation->user->typ() == 1 )
                      primary
                    @elseif($conversation->user->typ() == 3 )
                      success
                    @endif
                    "><i class="fa fa-comments-o"></i>
                    </div>
                    <div class="timeline-panel">
                      <div class="timeline-heading">
                          <h4 class="timeline-title">
                            <div class="pull-right">

                              @if(get_chat_group() == 1 && $conversation->active == 0)
                                <span class="btn btn-primary btn-xs modal-open " target="{{ URL::route('dok.notifications.chat.close', array($conversation->id)) }}" data-toggle="modal" data-target="#modal">
                                  <i class="fa fa-times-circle-o"></i> Zamknij rozmowę
                                </span>
                                @if($conversation->deadline == '')
                                <span class="btn btn-warning btn-xs modal-open " target="{{ URL::route('dok.notifications.chat.deadline', array($conversation->id)) }}" data-toggle="modal" data-target="#modal">
                                  <i class="fa fa-clock-o"></i> Ustaw termin
                                </span>
                                @endif
                              @endif
                              @if($conversation->active == 0)
                              <span class="btn btn-warning btn-xs create-chat modal-open" target="{{ URL::route('dok.notifications.chat.replay', array($conversation->id)) }}" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-reply"></i> Odpowiedz
                              </span>
                              @endif
                            </div>

                            <div class="pull-left">
                              {{ $conversation->topic }}
                            </div>

                            @if($conversation->deadline != '')
                              <div class="pull-left deadline
                              @if($conversation->deadline == date('Y-m-d'))
                                green
                              @elseif(strtotime($conversation->deadline) < time())
                                red
                              @else
                                grey
                              @endif
                              ">
                                <i class="fa fa-clock-o"></i> {{$conversation->deadline}}
                              </div>
                            @endif
                            
                            
                          </h4>
                      </div>
                      <div class="timeline-body">
                        <ul class="chat">
                          @foreach($conversation->messages as $k2 => $message)
                            <?php $status = get_receivers($message->status); ?>
                            @if($status[get_chat_group()-1] == 1)
                              <li class="left clearfix
                              @if( ($status[0] == 1 && $message->dok_read  == '' && get_chat_group() == 1) || ($status[2] == 1 && $message->info_read  == '' && get_chat_group() == 3))
                                lightBlue
                              @endif
                              ">
                                <div class="pull-left">
                                  <div class="chat-message
                                    @if($message->user->typ() == 1 )
                                      message-primary
                                    @elseif($message->user->typ() == 3 )
                                      message-success
                                    @endif
                                    " >
                                    <i class="fa fa-comment-o "></i>
                                  </div>

                                </div>
                                <div class="chat-body clearfix">
                                  <p class="pull-right timeline-timer">
                                      <small class="text-muted" style="padding-top: 2px;">                                  
                                        <i class="fa fa-clock-o fa-fw"></i> {{ substr($message->created_at, 0, -3) }}
                                      </small>

                                      @if($status[0] == 1 && $message->user->typ() != 1 && get_chat_group() != 1)
                                      <span class="time-container">
                                        <span class="label label-primary  legend-label" >
                                          @if($message->dos_read == '')
                                            <i class="fa fa-thumbs-o-down"></i>
                                          @else
                                            <i class="fa fa-thumbs-o-up"></i>
                                          @endif
                                        </span>
                                        @if($message->dok_read == '')
                                          <small class="text-muted ">...</small>
                                        @else
                                          <small class="text-muted ">{{ substr($message->dok_read, 0, -3) }}</small>
                                        @endif
                                      </span>
                                      @endif

                                      @if($status[2] == 1 && $message->user->typ != 3 && get_chat_group() != 3)
                                      <span class="time-container">
                                        <span class="label label-success  legend-label">
                                            @if($message->info_read == '')
                                              <i class="fa fa-thumbs-o-down"></i>
                                            @else
                                              <i class="fa fa-thumbs-o-up"></i>
                                            @endif                                       
                                        </span>
                                        @if($message->info_read == '')
                                          <small class="text-muted ">...</small>
                                        @else
                                          <small class="text-muted ">{{ substr($message->info_read, 0, -3) }}</small>
                                        @endif  
                                      </span>
                                      @endif

                                     
                                  </p>

                                  <p >
                                    {{ nl2br($message->content) }}
                                  </p>

                                  <div class="footer">
                                    
                                      <small class="text-muted">
                                        {{ $message->user->name }}
                                      </small>
                                    
                                  </div>
                                </div>
                              </li>
                            @endif
                          @endforeach
                        </ul>
                      </div>
                    </div>
                  </li>
                  <?php $lp++;?>
                @endif
              @endforeach
            </ul>
          </div>
        </div>

      </div>
      <div class="tab-pane fade in " id="history">
        <div class="row">
          <div class="col-sm-12 marg-btm">
          </div>
        </div>
        <?php foreach ($history as $k => $v) {?>
          <p class="clearfix ">
            <strong>{{substr($v->created_at,0,-3)}} - {{$v->user->name}}:</strong>
            {{ $v->history_type->content}}
            <em>
            @if($v->value == '-1')
              {{$v->dok_history_content->content}}              
            @else
              {{$v->value}}
            @endif
            </em>
            <hr class="short" />
          </p>
        <?php }?>
      </div>
  </div>
{{ Form::token() }}
<!-- normal modal -->
<div class="modal fade " id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      
    </div>
  </div>
</div>

<!-- small modal -->
<div class="modal fade bs-example-modal-sm" id="modal-sm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      
    </div>
  </div>
</div>
@stop

@section('headerJs')
  @parent
    <script type="text/javascript">

        function readCommunicator(){
          $.ajax({
            url: "<?php echo  URL::route('dok.notifications.chat.checkConversation');?>",
            data: {
                dok_notification_id: "<?php echo $notification->id;?>",
              _token: $('input[name="_token"]').val()
            },
            dataType: "json",
            type: "POST"
          });
        }

        $(document).ready(function() {

          $("form").validate();

          var $container = $('#notification-data .row');
          var hash = window.location.hash;

          $container.masonry({
            itemSelector: '.item-m'
          });

          $('#info_tabs a[href="' + hash + '"]').tab('show');
          if(hash == '#communicator') readCommunicator();
          else if(hash == '#notification-data'){
              setTimeout(function(){
                $container.masonry({
                  itemSelector: '.item-m'
                });
              }, 200);
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
                else if(e.target.hash == '#notification-data'){
                    setTimeout(function(){
                        var $container = $('#notification-data .row');
                        $container.masonry({
                            itemSelector: '.item-m'
                        });
                    }, 200);

                }

            });



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

          $("form").submit(function(e) {
               var self = this;
               e.preventDefault();

               if($("form").valid()){
                self.submit();
               }
               return false; //is superfluous, but I put it here as a fallback
          });

          $('#modal, #modal-sm').on('click', '#set-injury', function(){
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
                            $('#set-injury').attr('disabled',"disabled");
                          }
                      },
                      'json'
                  );
              return false;
            }
         });

         

          var filesA = new Array();
          Dropzone.options.fileForm = {
            init: function () {
              this.on("complete", function (file) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                  hrf="<?php echo URL::route('dok.notifications.getDocumentSet');?>";         
                  $.post( hrf, { "files": JSON.stringify(filesA), "_token" : $('input[name="_token"]').val() }, function( data ) {
                    $('#modal .modal-content').html(data);
                    $('#modal').modal({
                      keybord:false,
                      backdrop:false
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

          $('#modal').on('click', '#cancel_docs', function(){
            $.post(
                "<?php echo URL::route('dok.notifications.setDocumentDel');?>",
                
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
                  e.preventDefault();
              }
          });

          $('#priority').on('change', function(){
            $.ajax({
                type: "POST",
                url: "<?php echo  URL::route('dok.notifications.setPriority', array($notification->id));?>",
                data: {
                  priority: $(this).prop('checked') ? 1 : 0,
                  _token: $('input[name="_token"]').val()
                },
                success: function( data ) {
                    if(data.code == '0'){
                        $('#response-alert-info').html(data.message).fadeIn(300).delay(2500).fadeOut(300, function(){
                            $(this).html('');
                        });
                    }

                },
                dataType: 'json'
            });
          });

        });
        
    </script>

@stop      