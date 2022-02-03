@extends('layouts.main')

@section('header')
	
Zarządzanie procesami zgłoszeń DOK

@stop

@section('main')

<div class="row">
	<div class="col-md-7">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
		  <li class="active"><a href="#structure" role="tab" data-toggle="tab"><i class="fa fa-sitemap"></i> Struktura procesów</a></li>
		  <li><a href="#list_all" role="tab" data-toggle="tab"><i class="fa fa-list-ul"></i> Wszystkie procesy</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">

		  <div class="tab-pane active" id="structure">
		  	<div class="tree">
				<ul>
				<?= $processesTree->htmlList(); ?>
				</ul>
			</div>
		  </div>

		  <div class="tab-pane" id="list_all">
		  	<table class="table table-hover">
		  		<thead>
		  			<th>lp.</th>
		  			<th>Nazwa procesu</th>
		  			<th>Waga</th>
		  			<th>Limit czasu [h]</th>
		  			<th>Przypisani pracownicy</th>
		  			<th></th>
		  		</thead>
		  		<?php $lp = 1;?>
		  		@foreach($processes as $process)
					@if( $process->processes->isEmpty() )
					<tr>
						<td>{{ $lp++ }}.</td>
						<td>{{ $process->name }}</td>
						<td>{{ $process->weight }}</td>
						<td>{{ $process->time_limit }}</td>
						<td>
							<small>
							@foreach($process->users as $user)
							{{ $user->user->name }},
							@endforeach
							</small>
						</td>
						<td><button type="button" class="btn btn-warning btn-xs edit_process" target="{{ URL::route('settings.processes.info', array($process->id)) }}"><i class="fa fa-pencil"></i> edytuj</button></td>
					</tr>
					@endif	  	
		  		@endforeach
		  	</table>
		  </div>

		</div>

		
	</div>
	<div class="col-md-5" id="process_info" >

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
	    	
	    	$('.tree li:has(ul)').addClass('parent_li').find(' > span');
		    $('.tree li.parent_li > span').on('click', function (e) {
		        var children = $(this).parent('li.parent_li').find(' > ul > li');
		        if (children.is(":visible")) {
		            children.hide('fast');
		            $(this).find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
		        } else {
		            children.show('fast');
		            $(this).find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
		        }
		        e.stopPropagation();
		    });

		    $('.tree li.parent_li > span').each( function (e) {
		        var children = $(this).parent('li.parent_li').find(' > ul > li');
		        if (children.is(":visible")) {
		            children.hide('fast');
		            $(this).find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
		        } else {
		            children.show('fast');
		            $(this).find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
		        }
		    });

		    $('.edit_process').click(function(){
	       		$.ajax({
                  type: "GET",
                  url: $(this).attr('target'),
                  assync:false,
                  cache:false,
                  success: function( data ) {
	                $('#process_info').html(data);
                  },
                });
	       	});

		   	$('#process_info').on('click', '.modal-open', function(){
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