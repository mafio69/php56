<div class="col-sm-12">
    <h4 class="page-header marg-top-min">Dane leasingobiorcy</h4>
</div>
<div class="col-sm-12" id="client_container">
    <h5 class="page-header marg-top-min overflow text-center">Wyszukaj leasingobiorcę
        <p class="pull-right btn btn-sm btn-primary modal-open"
           target="{{ URL::to('insurances/create/create-client') }}" data-toggle="modal" data-target="#modal"><i
                    class="fa fa-plus"></i> dodaj nowego leasingobiorcę</p>
    </h5>
    <div class="form-group row">
        <div class="col-sm-6">
            <input class="form-control " id="client_name" name="client_name" placeholder="wg nazwy">
        </div>
        <div class="col-sm-3">
            <input class="form-control" id="client_NIP" name="client_NIP" placeholder="wg NIP">
        </div>
        <div class="col-sm-3">
            <input class="form-control" id="client_REGON" name="client_REGON" placeholder="wg REGON">
        </div>
    </div>
    <div class="row" >
        <div class="alert alert-warning col-sm-12 col-md-8 col-md-offset-2" role="alert" style="display: none;" id="searching_info">
        </div>
    </div>
    {{ Form::hidden('client_id', '', array('id' => 'client_id')) }}
    <div id="client_info_container" style="display: none;">
        <h4 class="marg-top">Dane leasingobiorcy</h4>
        <div class="form-group row">
            <div class="col-sm-12 col-md-6">
                <table class="table table-hover table-condensed">
                    <tr class="active">
                        <Td colspan="2">
                            <span class="sm-title">Dane rejestrowe:</span>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Nazwa:</label></td>
                        <td class="client_info" id="client_info_name"></td>
                    </tr>
                    <tr>
                        <td><label>NIP:</label></td>
                        <td class="client_info" id="client_info_NIP"></td>
                    </tr>
                    <tr>
                        <td><label>REGON:</label></td>
                        <td class="client_info" id="client_info_REGON"></td>
                    </tr>
                    <tr class="active">
                        <Td colspan="2"><span class="sm-title">Adres rejestrowy:</span></td>
                    </tr>
                    <tr>
                        <td><label>Kod pocztowy:</label></td>
                        <td class="client_info" id="client_info_registry_post"></td>
                    </tr>
                    <tr>
                        <td><label>Miato:</label></td>
                        <td class="client_info" id="client_info_registry_city"></td>
                    </tr>
                    <tr>
                        <td><label>Ulica:</label></td>
                        <td class="client_info" id="client_info_registry_street"></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-12 col-md-6">
                <table class="table table-hover table-condensed">
                    <tr class="active">
                        <Td colspan="2">
                            <span class="sm-title">Adres kontaktowy:</span>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Kod pocztowy:</label></td>
                        <td class="client_info" id="client_info_correspond_post"></td>
                    </tr>
                    <tr>
                        <td><label>Miato:</label></td>
                        <td class="client_info" id="client_info_correspond_city"></td>
                    </tr>
                    <tr>
                        <td><label>Ulica:</label></td>
                        <td class="client_info" id="client_info_correspond_street"></td>
                    </tr>
                    <tr class="active">
                        <Td colspan="2">
                            <span class="sm-title">Dane kontaktowe:</span>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Telefon:</label></td>
                        <td class="client_info" id="client_info_phone"></td>
                    </tr>
                    <tr>
                        <td><label>Email:</label></td>
                        <td class="client_info" id="client_info_email"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <hr class="height bg-primary"/>
</div>

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function(){
            $('#client_name, #client_NIP, #client_REGON').autocomplete({
                source: function( request, response ) {
                    var input = this.element;
                    var col_name = $(input).attr('name');
                    $.ajax({
                        url: "{{ URL::to('insurances/info-insurances/search-client') }}",
                        data: {
                            col_name: col_name,
                            term: request.term,
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            if(data.length > 0) {
                                $('#searching_info').text('').hide();
                                response($.map(data, function (item) {
                                    return item;
                                }));
                            }else{
                                $('#searching_info').text('Brak leasingobiorców o podanych parametrach wyszukiwania.').show();
                            }
                        }
                    });
                },
                minLength: 3,
                select: function(event, ui) {
                    var client_id = ui.item.id;
                    $('#client_id').val(client_id);
                    $.ajax({
                        url: "{{ URL::to('insurances/info-insurances/client-info') }}",
                        data: {
                            client_id: client_id,
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            $.each( data, function( key, value ) {
                                $('#client_info_'+key).html(value);
                            });
                            $('#client_info_container').show();
                        }
                    });
                },
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                }
            }).on('keyup', function(e){
                //no eneter or esc
                if (e.which != 13 && e.which != 27) {
                    $('#client_info_container').hide();
                    $('.client_info').html('');
                    $('#searching_info').hide().html('');
                    $('#client_id').val('');
                }

            });

            $('#modal').on('click', '#addClient', function(){
                var $btn = $(this).button('loading');
                if($('#dialog-form').valid()) {
                    $.ajax({
                        type: "POST",
                        url: $('#dialog-form').prop('action'),
                        data: $('#dialog-form').serialize(),
                        assync: false,
                        cache: false,
                        success: function (data) {
                            if (data.status == 'success'){
                                $.each( data.client, function( key, value ) {
                                    $('#client_info_'+key).html(value);
                                });
                                $('#searching_info').text('').hide();
                                $('#client_id').val(data.client.id);
                                $('#client_info_container').show();
                            }else if (data.status == 'error'){
                                $.notify({
                                    icon: "fa fa-minus",
                                    message: data.msg
                                },{
                                    type: 'danger',
                                    placement: {
                                        from: 'bottom',
                                        align: 'right'
                                    },
                                    delay: 2500,
                                    timer: 500
                                });
                            }

                            $('#modal').modal('hide');
                            $('#modal .modal-content').html('');
                        },
                        dataType: 'json'
                    });
                }else {
                    $btn.button('reset');
                }
                return false;
            });
        });
    </script>
@stop

