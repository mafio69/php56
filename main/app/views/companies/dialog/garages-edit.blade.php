<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja warsztatu {{ $branch->short_name }}</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
<form action="{{ URL::to('company/garages/update-modal', array($branch->id) ) }}" method="post" id="dialog-form">
			{{Form::hidden('from_modal',1)}}
	  	<div class="form-group">
		    <label >Dane warsztatu:</label>
		    <div class="row">
		    	<div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('short_name', $branch->short_name, array('class' => 'form-control  tips', 'placeholder' => 'nazwa skrócona', 'title' => 'nazwa skrócona'))  }}
				</div>
			    <div class="col-md-4 col-lg-4 marg-btm tips"  title = "telefon">
			    	@if($company->type == 1)
			    		{{ Form::text('phone', $branch->phone, array('class' => 'form-control  required', 'placeholder' => 'telefon'))  }}
			    	@else
			    		{{ Form::text('phone', $branch->phone, array('class' => 'form-control  ', 'placeholder' => 'telefon'))  }}
			    	@endif
				</div>
				<div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('email', $branch->email, array('class' => 'form-control   tips', 'placeholder' => 'email (oddzielone przecinkiem)', 'title' => 'email (oddzielone przecinkiem)'))  }}
			    </div>
			   	<div class="col-md-4 col-lg-4 marg-btm">
			    	<select name="priority" class="form-control">
			    		<?php for($i=0; $i<=9; $i++){
			    			echo '<option value="'.$i.'"';
			    			 if($branch->priority == $i){
			    			 echo 'selected';
			    			}

			    			echo '>priorytet '.$i.'</option>';
			    		}?>
			    	</select>
			    </div>
          <div class="col-md-8 col-lg-8 marg-btm">
            <select name="typeGarages_id[]" id="typeGarages2" class="form-control" multiple="multiple">
              <?php foreach($typegarages as $k => $v){
                echo '<option value="'.$v->id.'"';
                if( isset($typegaragesReSel[$v->id]) && $typegaragesReSel[$v->id] == 1){
                  echo 'selected';
                }
                echo '>'.$v->name.'</option>';
              }?>
            </select>
          </div>
			</div>
	  	</div>
	  	<div class="form-group">
		    <label >Ilość aut zastępczych:</label>
		    <div class="row">
		    	@foreach($typevehicles as $type)
		    	<div class="col-md-3 col-lg-3 marg-btm" >
		    		<input id="car{{$type->id}}" type="text" name="car{{$type->id}}" desc="{{$type->name}}" class="carSpinner"
		    		@if(isset($typevehiclesReSel[$type->id]))
		    			value="{{$typevehiclesReSel[$type->id]}}"
		    		@else
		    			value="0"
		    		@endif
		    		>
				</div>
				@endforeach
			</div>
	  	</div>
	  	<div class="form-group">
	  		<div class="row">
	  			<div class="col-md-3 col-lg-3 marg-btm">
	  				<div class="checkbox">
						<label>
						  <input type="checkbox" id="tug" name="tug"
						  @if($branch->tug == 1)
						   checked
						  @endif
						  > Posiada holownik
						</label>
					</div>
	  			</div>
	  			<div class="col-md-3 col-lg-3 marg-btm">
	  				<div class="checkbox">
						<label>
						  <input type="checkbox" id="tug24h" name="tug24h"
						  @if($branch->tug == 1)
						  	@if($branch->tug24h == 1)
						   		checked
						   	@endif
						  @else
						  	disabled="disabled"
						  @endif
						  > Dostępność holownika 24h
						</label>
					</div>
	  			</div>
        	<div class="col-md-3 col-lg-3 marg-btm">
            <div class="checkbox">
              <label>
                <input type="checkbox" name="suspended"
                @if($branch->suspended == 1)
                 checked="checked"
                @endif> Zawieszony
              </label>
            </div>
          </div>
	  		</div>
	  	</div>
	  	<div  class="form-group">
	  		<label >Obsługiwane marki osobowe:</label>

	  		<input name="brands_o" class="form-control" id="brands_o2" multiple ="multiple "
	  		value="
	  		@foreach($brands as $v)
	  			@if($v->typ == 1)
	  				{{$v->id}},
	  			@endif
	  		@endforeach
	  		"
	  		 />

	  	</div>

	  	<div  class="form-group">
	  		<label >Obsługiwane marki ciężarowe:</label>
	  		<input name="brands_c" class="form-control" id="brands_c2" multiple ="multiple "
	  		value="
	  		@foreach($brands as $v)
	  			@if($v->typ == 2)
	  				{{$v->id}},
	  			@endif
	  		@endforeach
	  		"
	  		 />
	  	</div>

		<div class="form-group">
		    <label >Uwagi:</label>
		    {{ Form::textarea('remarks', $branch->remarks, array('class' => 'form-control  ', 'placeholder' => 'uwagi'))  }}
		</div>
			{{Form::token()}}

	</div>
</form>

	<script type="text/javascript">




		var delay = ( function() {
			var timer = 0;
			return function(callback, ms) {
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();


		function movieFormatResult(brand) {
	        var markup = "<table class='movie-result'><tr><td>"+brand.name+"</td></tr></table>";
	        return markup;
	    }

	    function movieFormatSelection(movie) {
	        return movie.name;
	    }

      $(document).ready(function(){

			$("form").submit(function(e) {
			     var self = this;
			     e.preventDefault();
			     if($("form").valid()){
			     	self.submit();
			     }
			     return false; //is superfluous, but I put it here as a fallback
			});

	      	$('#typeGarages2').multiselect({
	            buttonWidth: $('input[name="email"]').width()+25,
	            buttonText: function(options) {
	                if (options.length === 0) {
	                    return 'Wybierz typy warsztatu <b class="caret"></b>';
	                }
	                else {
	                	var count = 0;
	                    var selected = '';
	                    options.each(function() {
	                        selected += $(this).text() + ', ';
	                        count++;
	                    });
	                    if(count > 2)
	                    	return 'wybrano ' +count+ ' typy <b class="caret"></b>';
	                    else
	                    	return selected.substr(0, selected.length -2) + ' <b class="caret"></b>';
	                }
	            }
	        });

	        initialize();

	    	$('#mapShow').on('change', function(){

	      		if($('#mapShow').is(':checked')){
		      		$('#map-canvas').show();
		      		$('#correctMap').parent().parent().show();
		  			initialize();

		      		lokalizacja_wyszukiwanie();


		      		$('#city, #street').keyup(function() {
						delay( function() {
							lokalizacja_wyszukiwanie();
						}, 500);
					});

		      		$('#correctMap').on('change', function(){
		      			if($('#correctMap').is(':checked')){
		      				correlation = 1;
		      			}else{
		      				correlation = 0;
		      			}

		      		}).change();

	  			}else{
	  				$('#map-canvas').hide();
	  				$('#correctMap').parent().parent().hide();
	  				begin = 0;
	  			}
	      	}).change();

	      	$('#city, #street').keyup(function() {
				delay( function() {
					lokalizacja_wyszukiwanie();
				}, 500);
			});

	      	$('#test').click(function(){
	      		console.log($('#typeGarages').val());
	      	});

	    	$('#brands_o2').select2({
	    		placeholder: "Wybierz obsługiwane marki samochodów",
    			minimumInputLength: 2,
    			multiple:true,
    			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			        url: "<?php echo  URL::to('companies/brands-list', array(1) );?>",
			        dataType: 'json',
			        type: "GET",
			        data: function (term, page) {
			            return {
			                q: term,
			                _token: $('input[name="_token"]').val()
			            };
			        },
			        results: function (data) {

			            return {results: data};
			        }

			    },
			    initSelection: function(element, callback) {
			        var id=$(element).val();
			        if (id!=="") {
			            $.ajax("<?php echo  URL::to('companies/brands-list-connect');?>", {
			            	type: "GET",
			                data: {
			                    _token: $('input[name="_token"]').val(),
			                    q: id
			                },
			                dataType: "json"
			            }).done(function(data) { callback(data); });
			        }
			    },
			});

	    	$('#brands_c2').select2({
	    		placeholder: "Wybierz obsługiwane marki samochodów",
    			minimumInputLength: 2,
    			multiple:true,
    			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			        url: "<?php echo  URL::to('companies/brands-list', array(2) );?>",
			        dataType: 'json',
			        type: "GET",
			        data: function (term, page) {
			            return {
			                q: term,
			                _token: $('input[name="_token"]').val()
			            };
			        },
			        results: function (data) {

			            return {results: data};
			        }

			    },
			    initSelection: function(element, callback) {
			        var id=$(element).val();
			        if (id!=="") {
			            $.ajax("<?php echo  URL::to('companies/brands-list-connect');?>", {
			            	type: "GET",
			                data: {
			                    _token: $('input[name="_token"]').val(),
			                    q: id
			                },
			                dataType: "json"
			            }).done(function(data) { callback(data); });
			        }
			    },
			});

			$('.carSpinner').TouchSpin({
                min: 0,
                max: 1000000000,
                stepinterval: 1,
                prefix: ' '
            }).each(function(){
            	desc = $(this).attr('desc');
            	$(this).prev().html(desc).css('border-right','0px');
            });

            $('#tug').change(function(){
            	if($(this).is(':checked'))
            		$('#tug24h').removeAttr('disabled');
            	else
            		$('#tug24h').attr('disabled', 'disabled');
            });

		  $('#code').on('change keyup paste click', function(){
			  var $code = $(this).val();
			  if($code.length == '6'){
				  $.ajax({
					  type: 'GET',
					  url: '/company/garages/check-voivodeship',
					  data: {'code': $code},
					  assync:false,
					  cache: false,
					  dataType: 'json',
					  success: function(data) {
						  if(data.status == 'ok')
						  {
							  $('#voivodeship_id').val( data.voivodeship_id );
						  }
					  }
				  });
			  }
		  });

      });



    </script>

		</div>
		<div class="modal-footer">
		    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
		    <button type="button" class="btn btn-primary" id="set-spec">Zapisz</button>
		</div>
