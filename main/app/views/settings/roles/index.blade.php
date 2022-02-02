@extends('layouts.main')

@section('header')
Role użytkowników
@stop

@section('main')
	<div class="row">
		@foreach($modules as $module)
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="panel panel-default ">
                    <div class="panel-heading">

						<h3 class="panel-title">Moduł {{ $module->name }}</h3>
					</div>
                    <div class="panel-body">
                        <table class="table  table-hover table-condensed" id="users-table">
                            <thead>
                                <th >lp.</th>
                                <th >Nazwa</th>
                                <th ></th>
								<th></th>
                            </thead>
                            @foreach ($roles[$module->id] as $k => $role)
                                <tr class="odd gradeX">
                                    <td class="vertical-middle">{{++$k}}.</td>
                                    <Td class="vertical-middle">{{$role->name}}</td>
                                    <td>
                                        <button target="{{ URL::route('roles-edit', array($role->id, $role->module_id)) }}" class="btn btn-warning edit-role btn-xs" data-toggle="modal" data-target="#modal-roles"><i class="fa fa-pencil"></i> edytuj</button>
                                    </td>
									<td>
										<a href="{{ url('settings/roles/history', [$role->id]) }}" class="btn btn-xs btn-info">
											<i class="fa fa-history fa-fw" aria-hidden="true"></i> historia
										</a>
									</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


<!-- Modal -->
<div class="modal fade" id="modal-roles" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

    </div>
  </div>
</div>

@stop


@section('headerJs')
	@parent
	<script type="text/javascript">
	    $(document).ready(function() {
	    	$('#add-role').click(function(){
		       	var hrf=$(this).attr('target');

				$.get( hrf, function( data ) {
				  $('#modal-roles .modal-content').html(data);
				});
	       })

	       $('#modal-roles').on('click', '#create-role', function(){
	       		$('#create-role-form').validate();

                var btn = $(this);
                btn.attr('disabled', 'disabled');

	       		if($('#create-role-form').valid() ){
					$.post(
			            $('#create-role-form').prop( 'action' ),

			            $('#create-role-form').serialize()
			            ,
			            function( data ) {
			                if(data == '0') location.reload();
			                else{
			                	$('<label for="name" class="error">'+data+'</label>').insertAfter( "#modal-roles .modal-body input[name='name']" );
			                	btn.removeAttr('disabled');
			                }
			            },
			            'json'
			        );
					return false;
	       		}else{
	       		    btn.removeAttr('disabled');
	       		}

	       });

            $('#modal-roles').on('hidden.bs.modal', function (e) {
                $('#modal-roles .modal-content').html('');
            })

	       $('.table').on('click', '.edit-role', function(){
		       	var hrf=$(this).attr('target');
				$.get( hrf, function( data ) {
				  $('#modal-roles .modal-content').html(data);
				});
	       });

	       $('#modal-roles').on('click', '#save-role', function(){
	       		$('#edit-role-form').validate();

	       		if($('#edit-role-form').valid() ){
					$.post(
			            $('#edit-role-form').prop( 'action' ),

			            $('#edit-role-form').serialize()
			            ,
			            function( data ) {
			                if(data == '0') location.reload();
			                else{
			                	$('<label for="name" class="error">'+data+'</label>').insertAfter( "#modal-roles .modal-body input[name='name']" );
			                }
			            },
			            'json'
			        );
					return false;
	       		}
	       });
	    });
    </script>

@stop
