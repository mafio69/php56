  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Wyszukaj serwis</h4>
  </div>
  <div class="modal-body" style="overflow:hidden; padding-bottom:0px; ">
        <div class="row">
			<div class="col-sm-12">
				<input class="form-control input-sm" id="branchName" type="text" placeholder="nazwa warsztatu"/>
			</div>

			<div class="col-sm-12 marg-btm "><h6>Serwisy dostępne w podanych kryteriach wyszukiwania:</h6></div>
			<div class="col-sm-12 marg-btm  searched_com" id="searched_com">
                <div class="list-group"></div>
            </div>
	  	</div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
  </div>


<script type="text/javascript">
    function generateList(){
        $('#searched_com .list-group').html('')
        var term = $('#branchName').val();


        $.each(source, function (i, item) {
            if(term == '' || (item.short_name).toUpperCase().indexOf(term.toUpperCase()) !== -1) {
                myLatLng = new google.maps.LatLng({lat: item.lat, lng: item.lng});
                if(map.getBounds().contains(myLatLng)) {
                    var groups = [];
                    $.each(item.branchPlanGroups, function (k, branchPlanGroup) {
                        if (branchPlanGroup.plan_group !== null) {
                            groups.push(branchPlanGroup.plan_group.name);
                        }
                    });

                    $('#searched_com .list-group').append('' +
                        '<li class="list-group-item" onclick="showDetails(' + item.id + ')">' +
                        ((item.suspended) ? '<span class="label label-danger"><i class="fa fa-exclamation-triangle fa-fw"></i></span> ' : '') +
                        item.short_name +
                        '<span class="marg-left label label-info small">' + item.address + '</span>' +
                        '<span class="badge">' + groups.join(', ') + '</span>' +
                        '</li>');
                }
            }
        });
    }
  $(document).ready(function(){
      generateList();
	$('#branchName').on('keyup', function(){
		generateList();
	});


  });
</script>
