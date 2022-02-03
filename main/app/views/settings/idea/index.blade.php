@extends('layouts.main')

@section('header')
	
Dane Idei wykorzystywane w generowanych dokumentach

@stop

@section('main')
<style>
	.owner-block{
		border-left: 1px solid #dddddd;
	}
	#header_row2 { display: none; position: fixed; top: 0px;  z-index: 1; }
	#header_row2 div:first-child { margin-left: 0px; }
	#header_row2 div { float: left; padding: 8px; height: 50px; background-color: #F0F0F0; text-align: center; font-size: 11px;  font-weight: bold; border-right: 1px solid white; }

	.fixed-column {
		position: fixed;
		display: inline-block;
		width: auto;
		border-right: 1px solid #ddd;
		background: white;
	}

</style>
	<div id="header_row2" style="display:none;">
		<div >lp.</div>
		<div >Opis pola</div>
		@foreach($owners as $owner)
			<div>{{ $owner->name }}
				@if($owner->old_name)
					({{ $owner->old_name }})
				@endif
			</div>
		@endforeach
	</div>
	<table class="table table-hover" id="table_list" style="width: {{ ($owners->count() * 220) + 150 }}px;">
			<thead id="header_row">
				<th width="35">lp.</th>
				<th width="100">Opis pola</th>
				@foreach($owners as $owner)
					<th width="220">
						{{ $owner->name }}
						@if($owner->old_name)
							({{ $owner->old_name }})
						@endif
					</th>
				@endforeach
			</thead>
			
            @foreach ($parameters as $k => $parameter)
				<tr>
					<td width="20">{{++$k}}.</td>
					<Td width="30">{{$parameter->name}}</td>
                    @foreach($owners as $owner)
					    <Td width="220" class="owner-block">
                            @if( isset($settingsA[$owner->id][$parameter->id]) )
                            {{$settingsA[$owner->id][$parameter->id]->value}}
                            @else
                            ---
                            @endif
                            <button target="{{ URL::route('idea-edit', array($owner->id, $parameter->id)) }}" class="btn btn-warning btn-xs edit pull-right" data-toggle="modal" data-target="#modal">edytuj</button>
                        </td>
                    @endforeach
				</tr>
            @endforeach
		</table>



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
	    $(document).ready(function() {	
	    	

	       $('#modal').on('click', '#save', function(){
	       		$('#edit-form').validate();

	       		if($('#edit-form').valid() ){
	       			

					$.post(
			            $('#edit-form').prop( 'action' ),
			            
			            $('#edit-form').serialize()
			            ,
			            function( data ) {
			                if(data == '0') location.reload();
			                else{
			                	$('<label for="name" class="error">'+data+'</label>').insertAfter( "#modal .modal-body input[name='name']" );
			                } 
			            },
			            'json'
			        );

					return false;
	       		}

	       });	

	       $('.table').on('click', '.edit', function(){
		       	hrf=$(this).attr('target');	       	

				$.get( hrf, function( data ) {
				  $('#modal .modal-content').html(data);
				});
	       });




			var $table = $('#table_list');
			var $fixedColumn = $table.clone().removeAttr('id').removeAttr('style').insertBefore($table).addClass('fixed-column');

			$fixedColumn.find('th:not(:nth-child(1),:nth-child(2)),td:not(:nth-child(1),:nth-child(2))').remove();

			$fixedColumn.find('tr').each(function (i, elem) {
				$(this).height($table.find('tr:eq(' + i + ')').height());
			});

			$(window).scroll(function(){
				var left = 30 - $(this).scrollLeft();
				$("#header_row2").css("left", left+"px");
			});
			var table_width = $("#table_list").width();
			var top = $("#header_row").position().top;
			var glued = false;
			var top_offset = $("#table_list").offset().top;

			$("#header_row2").width(table_width+1);
			$("body").width(table_width+42);
			$(window).scroll(function() {
				var scroll = 0;
				if(window.pageYOffset != undefined) {
					scroll = window.pageYOffset;
				} else {
					var iebody = (document.compatMode && document.compatMode != "BackCompat")
							? document.documentElement : document.body;
					scroll = iebody.scrollTop;
				}

				$('.fixed-column').css('top', (top_offset - scroll)+ 'px');

				if(!glued && scroll >= top) {
					glued = true;
					$("#header_row2").show();
					$("#table_list #header_row th").each(function(i, e) {
						var w = $(this).width();

						$("#header_row2 div:nth-child("+(i+1)+")").width( (w - 1) );
					});
				} else if(scroll < top) {
					glued = false;
					$("#header_row2").hide();
				}
			});
	       
	    });
    </script>
  
@stop