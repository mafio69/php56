<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="myModalLabel">Przypisanie serwisu</h4>
</div>
<div class="modal-body" style="overflow:hidden; padding-bottom:0px; ">
  <form action="" method="post"  id="dialog-form">
    <input type="hidden" id="id_warsztat" name="id_warsztat" >
    <input type="hidden" id="name_warsztat" name="name_warsztat" >
    <div id="data_warsztat" style="display:none;"></div>
    {{Form::token()}}
    <div class="row">
      {{--<div class="col-sm-6">
        <select class="form-control input-sm" id="typeCompany">
          <option value="1" selected>typ - przypisane</option>
          <option value="2">typ - wszystkie</option>
        </select>
      </div>--}}
      <div class="col-sm-12">
        <input class="form-control input-sm" id="branchName" type="text" placeholder="nazwa warsztatu" autocomplete="off"/>
      </div>

      <div class="col-sm-12 marg-btm">
        <h6>Serwisy dostępne w podanych kryteriach wyszukiwania:</h6>
      </div>

      <div class="searched_com col-sm-12 marg-btm" id="searched_com">
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 marg-btm">
        <div class="checkbox">
          <label>
            <input type="checkbox"  name="dont_send_sms" value="1" id="dont_send_sms"> nie wysyłaj powiadomienia SMS o przypisanym warsztacie
          </label>
        </div>
      </div>
    </div>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
  <button type="button" class="btn btn-primary" id="set-branch-special">Przypisz serwis</button>
</div>


<script type="text/javascript">





    $(document).ready(function(){


        $(document).on('click', '.bt_com_search', function(){
            $('.bt_com_search').removeClass('active').removeClass('green');
            $(this).addClass('active').addClass('green');
            $('#id_warsztat').val($(this).attr('id'));
            $('#name_warsztat').val($(this).data('name'));
//  var data = $('#'+$(this).attr('aria-controls')).html();
            $('#data_warsztat').html('<h4>'+$(this).data('name')+'</h4><p>Adres: '+$(this).data('address')+'</p>');
        });




        $('#branchName').on('keyup', function() {
            term = $(this).val();
            if (term.length > 2){
                $.ajax({
                    url: "<?php echo URL::route('injuries-assignBranchesNameList', array('','') ); ?>",
                    data: {
                        "term": term,
                        "type" : $('#typeCompany').val(),
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
