  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przypisanie/edycja serwisu</h4>
  </div>
  <div class="modal-body" style="overflow:hidden; padding-bottom:0px; ">
  	<form action="{{ URL::route('injuries-setBranch', array($injury->id)) }}" method="post"  id="assign-branch-form">
        <input type="hidden" id="id_warsztat" name="id_warsztat" >
        {{Form::token()}}
  		<div class="row">

	  		<div class="col-sm-12 marg-btm">
	    		<input class="form-control input-sm" id="branchName" type="text" placeholder="nazwa warsztatu"/>
	    	</div>

  			<div class="col-sm-12 marg-btm">
  				<h6>Serwisy dostępne w podanych kryteriach wyszukiwania:</h6>
  			</div>

	  		<div class="row searched_com col-sm-12 marg-btm" id="searched_com">
	  		</div>

			<div class="col-sm-12 marg-btm">
				<div class="checkbox">
					<label>
						<input type="checkbox"  name="dont_send_sms" value="1"> nie wysyłaj powiadomienia SMS o przypisanym warsztacie
					</label>
				</div>
			</div>
  		</div>
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-branch">Przypisz serwis</button>
  </div>


<script type="text/javascript">





  $(document).ready(function(){


  	$(document).on('click', '.bt_com_search', function(){
		$('.bt_com_search').removeClass('active').removeClass('green');
		$(this).addClass('active').addClass('green');
		$('#id_warsztat').val($(this).attr('id'));
	});




	$('#branchName').on('keyup', function() {
        term = $(this).val();
        if (term.length > 2){
            $.ajax({
                url: "<?php echo URL::route('injuries-assignBranchesNameList', array($injury->id) ); ?>",
                data: {
                    "term": term,
                    "_token": $('input[name="_token"]').val()
                },
                dataType: "json",
                type: "POST",
                success: function (data) {

                    markers = new Array(data.length);
                    companies = new Array(data.length);

                    if (data.length != 0) {
                        id_warsztat = $('#id_warsztat').val();
                        if ($("#searched_com").hasClass('done')) {
                            $("#searched_com").accordion('destroy');
                        }
                        $('#searched_com').html('');
                        for (i in data) {
                            $('#searched_com').append(data[i].dataText);
                        }
                        $('#searched_com').accordion({
                            collapsible: true,
                            heightStyle: "content",
                            active: false
                        }).addClass('done');


                    } else {
                        $('#searched_com').html('nie ma serwisu o podanej nazwie');
                    }
                }
            });
        }
	});



  });
  //google.maps.event.addDomListener(window, 'load', initialize);
</script>
