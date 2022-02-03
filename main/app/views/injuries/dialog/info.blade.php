<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="myModalLabel">Informacje o szkodzie</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#injury-data" data-toggle="tab">Dane szkody i pojazdu</a></li>
    <li><a href="#localization" data-toggle="tab">Lokalizacja zdarzenia</a></li>
    <li><a href="#damage" data-toggle="tab">Uszkodzenia</a></li>
    <li><a href="#documentation" data-toggle="tab">Dokumentacja</a></li>
    <li><a href="#photos" data-toggle="tab">Zdjęcia</a></li>
    <li><a href="#history" data-toggle="tab">Historia</a></li>
    <li><a href="#step_history" data-toggle="tab">Historia statusów</a></li>
  </ul>  
  <div class="tab-content">
      <div class="tab-pane fade in active" id="injury-data">
          
          <div class="row">
            
            <div class="col-sm-6 col-md-4">
              <div class="panel panel-default small">
                <div class="panel-heading ">Dane szkody:<i class="fa fa-pencil-square-o pull-right tips" title="edytuj"></i></div>
                <table class="table">
                  <tr>
                    <td><label>Typ szkody:</label></td>
                    <td>{{ $injury->injuries_type()->first()->name }}</td>
                  </tr>
                  <tr>
                    <td><label>Odbiór odszkodowania:</label></td>
                    <td>{{ $injury->receive()->first()->name }}</td>
                  </tr>
                  <tr>
                    <td><label>Data zdarzenia:</label></td>
                    <Td>{{ $injury->date_event }}</td>
                  </tr>
                  <Tr>
                    <td><label>Zawiadomiono policję:</label></td>
                    <Td><?php if($injury->police == 1) echo 'tak'; else echo 'nie';?></td>
                  </tr>
                </table>
              </div>
            </div>
            <div class="col-sm-6 col-md-4">
              <div class="panel panel-default small">
                 <div class="panel-heading ">Dane pojazdu</div>
                 <table class="table">
                  <tr>
                    <td><label>Rejestracja:</label></td>
                    <Td>{{ $injury->vehicle()->first()->registration }}</td>
                  </tr>
                  <tr>
                    <td><label>Nr umowy leasingowej:</label></td>
                    <td>{{ $injury->vehicle()->first()->nr_contract }}</td>
                  </tr>
                  <tr>
                    <td><label>VIN:</label></td>
                    <td>{{ $injury->vehicle()->first()->VIN }}</td>
                  </tr>
                  <tr>
                    <td><label>Marka i model:</label></td>
                    <td>{{ $injury->vehicle()->first()->brand }} {{ $injury->vehicle()->first()->model }}</td>
                  </tr>
                  <tr>
                    <td><label>Silnik:</label></td>
                    <td>{{ $injury->vehicle()->first()->engine }}</td>
                  </tr>
                  <tr>
                    <td><label>Rok produkcji:</label></td>
                    <td>{{ $injury->vehicle()->first()->year_production }}</td>
                  </tr>
                  <tr>
                    <td><label>Data pierwszej rejestracji:</label></td>
                    <td>{{ $injury->vehicle()->first()->first_registration }}</td>
                  </tr>
                  <tr>
                    <td><label>Przebieg:</label></td>
                    <td>{{ $injury->vehicle()->first()->contribution }}</td>
                  </tr>
                 </table>
              </div>
            </div>
          </div> 
          
          <?php if($injury->info != 0){?>
          <div class="row">
            <div class="col-md-12 col-lg-12 ">
              <div class="form-group">
                <label>Informacja wewnętrzna:</label>
                <p class="form-control">{{ $info->content }}</p>
              </div>
            </div>
          </div>
          <?php }?>
          <?php if($injury->remarks != 0){?>
          <div class="row">
            <div class="col-md-12 col-lg-12 ">
              <div class="form-group">
                <label>Uwagi do zlecenia:</label>
                <p class="form-control">{{ $remarks->content }}</p>
              </div>
            </div>
          </div>
          <?php }?>
          
            
      </div>
     
      <div class="tab-pane fade" id="localization">
          <div class="row">
            <div class="col-md-12 col-lg-12 ">
              <div class="form-group">
                <label>Adres zdarzenia:</label>
                <p class="form-control">{{ $injury->event_post}} {{ $injury->event_city }} - {{ $injury->event_street }}</p>
              </div>
            </div>
            
          </div> 

          <div id="map-canvas" style="width:100%; height:400px;  "></div>
      </div>

      <div class="tab-pane fade in " id="damage">
          <form id="form_damage">
            {{Form::token()}}
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-hover">
                        <?php for($i = 0 ; $i < ($ct_damage/2); $i++){?>
                        <tr >
                            <td>
                                <input type="checkbox" id="uszkodzenia_check<?php echo $damage[$i]['id'];?>" class="uszkodzenia_check" name="uszkodzenia[]" value="<?php echo $damage[$i]['id'];?>" <?php if(isset($damageInjury[$damage[$i]['id']])){?> checked="checked" <?php }?> />
                            </td>
                            <td class="check" style="text-align:left; padding-left:5px;">
                                <b><label for="uszkodzenia_check<?php echo $damage[$i]['id'];?>"><?php echo $damage[$i]['name'];?></label></b>
                            </td>
                            <?php if($damage[$i]['param'] != 0){?>
                            <td  class="check">
                                <label for="strona<?php echo $damage[$i]['id'];?>l">
                                    <?php switch($damage[$i]['param']){
                                            case 1: 
                                                echo 'lewy:';
                                                break;
                                            case 2:
                                                echo 'lewe:';
                                                break;
                                            case 3:
                                                echo 'lewa:';
                                                break;                          
                                    }?>
                                </label>
                                <input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>l" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="1" <?php if(isset($damageInjury[$damage[$i]['id']][1])){?> checked="checked" <?php }?>/>
                            </td>
                            <td class="check">
                                <label for="strona<?php echo $damage[$i]['id'];?>r">
                                    <?php switch($damage[$i]['param']){
                                            case 1: 
                                                echo 'prawy:';
                                                break;
                                            case 2:
                                                echo 'prawe:';
                                                break;
                                            case 3:
                                                echo 'prawa:';
                                                break;
                                    }?>
                                </label>
                                <input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>r" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="2" <?php if(isset($damageInjury[$damage[$i]['id']][2])){?> checked="checked" <?php }?>/>
                            </td>
                            <?php }else{?>
                            <td colspan="2"></td>
                            <?php }?>                  
                        </tr>
                        <?php }?>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-hover">
                        <?php for($i ; $i < $ct_damage; $i++){?>
                        <tr>
                            <td>
                                <input type="checkbox" id="uszkodzenia_check<?php echo $damage[$i]['id'];?>" class="uszkodzenia_check" name="uszkodzenia[]" value="<?php echo $damage[$i]['id'];?>" <?php if(isset($damageInjury[$damage[$i]['id']])){?> checked="checked" <?php }?>/>
                            </td>
                            <td class="check" style="text-align:left; padding-left:5px;">
                                <b><label for="uszkodzenia_check<?php echo $damage[$i]['id'];?>"><?php echo $damage[$i]['name'];?></label></b>
                            </td>
                            <?php if($damage[$i]['param'] != 0){?>
                            <td  class="check">
                                <label for="strona<?php echo $damage[$i]['id'];?>l">
                                    <?php switch($damage[$i]['param']){
                                            case 1: 
                                                echo 'lewy:';
                                                break;
                                            case 2:
                                                echo 'lewe:';
                                                break;
                                            case 3:
                                                echo 'lewa:';
                                                break;                          
                                    }?>
                                </label>
                                <input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>l" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="1" <?php if(isset($damageInjury[$damage[$i]['id']][1])){?> checked="checked" <?php }?>/>
                            </td>
                            <td class="check">
                                <label for="strona<?php echo $damage[$i]['id'];?>r">
                                    <?php switch($damage[$i]['param']){
                                            case 1: 
                                                echo 'prawy:';
                                                break;
                                            case 2:
                                                echo 'prawe:';
                                                break;
                                            case 3:
                                                echo 'prawa:';
                                                break;
                                    }?>
                                </label>
                                <input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>r" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="2" <?php if(isset($damageInjury[$damage[$i]['id']][2])){?> checked="checked" <?php }?>/>
                            </td>
                            <?php }else{?>
                            <td colspan="2"></td>
                            <?php }?>                  
                        </tr>
                        <?php }?>
                    </table>
                </div>
            </div>        
        </form>

      </div>

      <div class="tab-pane fade in " id="documentation">
      </div>

      <div class="tab-pane fade in " id="photos">
        <div class="row">
          <div class="col-sm-12">
            <div class="panel panel-default">
              <div class="panel-heading "><label>Przyjęcie:</label></div> 
              <div class="row">   
                <div class="col-sm-12">           
                {{ Form::open( [ 'url' => URL::route('injuries-post-image', array($injury->id, 1)) , 'class' => 'dropzone imageUploads' , 'id' => 'imgBefore', 'files'=>true ] ) }}
                  <div class="fallback">
                      <input name="file" type="file" multiple />
                  </div>
                {{ Form::close() }} 
                </div>
              </div>
              <div class="row marg-top-min">
                <div class="col-sm-12">
                <?php foreach ($imagesBefore as $k => $v) {?>
                  <div class="col-sm-4 col-md-2">
                      <div class="thumbnail">
                          <div class="image-container">
                              <a href="/uploads/images/full/{{$v->file}}" data-lightbox="image-before" >
                                <img src="/uploads/images/thumb/{{$v->file}}" alt="">
                              </a>
                          </div>
                          <div class="caption">
                              <button type="button" class="btn btn-danger btn-xs del-img" target="{{ URL::route('injuries-getDelImage', array($v->id)) }}" >usuń</button>
                          </div>
                      </div>
                  </div>
                <?php }?>
                </div>
              </div>
            </div> 
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="panel panel-default">
              <div class="panel-heading "><label>W trakcie:</label></div> 
              <div class="row">   
                <div class="col-sm-12">           
                {{ Form::open( [ 'url' => URL::route('injuries-post-image', array($injury->id, 2)) , 'class' => 'dropzone imageUploads' , 'id' => 'imgInprogress',  'files'=>true ] ) }}
                  <div class="fallback">
                      <input name="file" type="file" multiple />
                  </div>
                {{ Form::close() }} 
                </div>
              </div>
              <div class="row marg-top-min">
                <div class="col-sm-12">
                <?php foreach ($imagesInprogress as $k => $v) {?>
                  <div class="col-sm-4 col-md-2">
                      <div class="thumbnail">
                          <div class="image-container">
                              <a href="/uploads/images/full/{{$v->file}}" data-lightbox="image-inprogress" >
                                <img src="/uploads/images/thumb/{{$v->file}}" alt="">
                              </a>
                          </div>
                          <div class="caption">
                              <button type="button" class="btn btn-danger btn-xs del-img" target="{{ URL::route('injuries-getDelImage', array($v->id)) }}"  >usuń</button>
                          </div>
                      </div>
                  </div>
                <?php }?>
                </div>
              </div>
            </div> 
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="panel panel-default">
              <div class="panel-heading "><label>Po naprawie:</label></div> 
              <div class="row">   
                <div class="col-sm-12">           
                {{ Form::open( [ 'url' => URL::route('injuries-post-image', array($injury->id, 3)) , 'class' => 'dropzone imageUploads' , 'id' => 'imgAfter',  'files'=>true ] ) }}
                  <div class="fallback">
                      <input name="file" type="file" multiple />
                  </div>
                {{ Form::close() }} 
                </div>
              </div>
              <div class="row marg-top-min">
                <div class="col-sm-12">
                <?php foreach ($imagesAfter as $k => $v) {?>
                  <div class="col-sm-4 col-md-2">
                      <div class="thumbnail">
                          <div class="image-container">
                              <a href="/uploads/images/full/{{$v->file}}" data-lightbox="image-after" >
                                <img src="/uploads/images/thumb/{{$v->file}}" alt="">
                              </a>
                          </div>
                          <div class="caption">
                              <button type="button" class="btn btn-danger btn-xs del-img" target="{{ URL::route('injuries-getDelImage', array($v->id)) }}" >usuń</button>
                          </div>
                      </div>
                  </div>
                <?php }?>
                </div>
              </div>
            </div> 
          </div>
        </div>
      </div>

      <div class="tab-pane fade in " id="history">
      </div>

  </div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>



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
          center: new google.maps.LatLng(52.528846,17.071874),        
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


    $(document).ready(function() {     
      lat = "<?php echo $injury->lat; ?>";
      lng = "<?php echo $injury->lng; ?>";
      $('.nav-tabs a:eq(1)').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        setTimeout(function(){
          initialize(lat, lng);
        }, 300);        
      });

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
          data: $("#form_damage").serialize(),
          success: function(data) {       
          }
        });
      });

      $('.del-img').on('click', function(){        
        el = $(this);
        hrf=$(this).attr('target');         
        $.post( hrf, function( data ) {
          if(data == 0){
            el.parent().parent().parent().remove();
          }

        });
      });

      var myDropzone = new Dropzone("form#imgBefore");
      var myDropzone2 = new Dropzone("form#imgInprogress");
      var myDropzone3 = new Dropzone("form#imgAfter");

    });
    
  </script>