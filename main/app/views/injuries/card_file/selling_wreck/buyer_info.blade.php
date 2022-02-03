<div class="panel-heading">
    Dane nabywcy
</div>
<div class="panel-body">
    <div class="row">
        <div class="col-sm-12 ">
            <form role="form"  id="buyer_data">
                <div class="form-group form-group-sm col-sm-12 col-md-10 col-md-offset-1 form-horizontal" id="alert_buyer_confirm-group"
                @if( $injury->wreck && !in_array($injury->wreck->buyer, [2, 7]) )
                    style="display: none;"
                @endif
                >
                    {{ Form::confirmation(
                            'Potwierdzenie odkupu wraku przez nabywcę',
                            'alert_buyer',
                            ($injury->wreck) ? URL::route('injuries.info.setAlert', array('alert_buyer', $injury->wreck->id,'InjuryWreck',  'Termin otrzymania potwierdzenia odkupu')) : '',
                            ($injury->wreck) ? URL::route('injuries.info.setAlert_buyer_confirm', array($injury->wreck->id)) : '',
                            ($injury->wreck) ? $injury->wreck : null,
                            '',
                            'potwierdź zwrot',
                            'wreck_alert',
                            ($disabled|| ($injury->wreck->pro_forma_request!='0000-00-00' && $injury->wreck->pro_forma_request_confirm!='0000-00-00'))?array('disabled' => 'disabled'):array(),
                            array('col-sm-5','col-sm-4'),
                            true,
                            false
                        )
                    }}
                </div>

                <div class="row">
                        <div class="col-sm-12">
                            <h4 class="page-header marg-top-min"></h4>
                        </div>
                        <div id="buyer-search-container"
                            @if(
                                $injury->wreck
                                && $injury->wreck->buyer != 1
                                && (
                                    $injury->wreck->pro_forma_request == '0000-00-00'
                                    ||
                                    (
                                        in_array(Auth::user()->login, ['przem_k', 'justynan'])
                                        &&
                                        $injury->wreck->pro_forma_request_confirm == '0000-00-00'
                                        &&
                                        ($injury->wreck->alert_buyer_confirm == '0000-00-00' || $injury->wreck->buyer != 2)
                                    )
                                )
                            )
                                style="display: block;"
                            @else
                                style="display: none;"
                            @endif>
                            <div class="col-lg-2 col-lg-offset-1 text-center">
                                <label>Wyszukaj nabywcę</label>
                            </div>
                            <div class="form-group col-lg-4">
                                {{  Form::text('search_buyer_name', '', array('class' => 'form-control search-buyer', 'data-col' => 'name', 'placeholder' => 'wg nazwy'))  }}
                            </div>
                            <div class="form-group col-lg-3">
                                {{   Form::text('search_buyer_nip', '', array('class' => 'form-control search-buyer', 'data-col' => 'nip', 'placeholder' => 'wg nr NIP'))  }}
                            </div>
                            <div class="col-sm-12 col-md-8 col-md-offset-2" role="alert" style="display: none;" id="searching-buyer-info">
                                <p class="alert alert-warning">Brak w systemie nabywcy o podanych parametrach wyszukiwania.</p>
                                <a href="{{ URL::to('injuries/buyers/create') }}?referrer={{ Request::decodedPath() }}" class="btn btn-block btn-primary btn-sm marg-btm"><i class="fa fa-plus fa-fw"></i> dodaj nowego nabywcę</a>
                            </div>
                            <hr class="height bg-primary"/>
                        </div>

                    @if($injury->wreck)
                        {{ Form::hidden('buyer_id', $injury->wreck->buyer_id, array('id' => 'buyer_id')) }}

                        <div class="col-sm-12" id="buyer-info-container">
                            @if($injury->wreck->buyerInfo)
                                @include('injuries.card_file.partials.buyer-info', ['buyer' => $injury->wreck->buyerInfo])
                            @endif
                        </div>
                    @endif

                </div>

            </form>

        </div>
    </div>
</div>

@section('headerJs')
    @parent
    @if($injury->wreck)
    <script type="text/javascript">
        $(document).ready(function(){
            $('.search-buyer').autocomplete({
                source: function( request, response ) {
                    var input = this.element;
                    var col_name = $(input).data('col');
                    $.ajax({
                        url: "{{ URL::route('injuries.info.search-buyer') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            col_name: col_name,
                            term: request.term,
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            if(data.length > 0) {
                                $('#searching-buyer-info').hide();
                                response($.map(data, function (item) {
                                    return item;
                                }));
                            }else{
                                $('#searching-buyer-info').show();
                            }
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    var buyer_id = ui.item.id;
                    $('#buyer_id').val(buyer_id);
                    $.ajax({
                        url: "{{ URL::route('injuries.info.set-buyer') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            buyer_id: buyer_id,
                            wreck_id: "{{ $injury->wreck->id }}"
                        },
                        type: "POST"
                    });
                    $.ajax({
                        url: "{{ URL::route('injuries.info.buyer-info') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            buyer_id: buyer_id,
                        },
                        dataType: "html",
                        type: "POST",
                        success: function( data ) {
                            $('#buyer-info-container').html(data);
                            $('#invoice_panel').show();
                        }
                    });
                },
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                }
            }).on('keyup', function(e){
                //no eneter or esc
                if (e.which != 13 && e.which != 27) {
                    $('#buyer-info-container').html('');
                    $('#searching-buyer-info').hide();
                    $('#buyer_id').val('');
                }
            });

            $('#buyer_id').on('change', function(){
                var buyer_id = $(this).val();
                if(buyer_id != '') {
                    $.ajax({
                        url: "{{ URL::route('injuries.info.buyer-info') }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            buyer_id: buyer_id,
                        },
                        dataType: "html",
                        type: "POST",
                        success: function (data) {
                            $('#buyer-info-container').html(data);
                            $('#invoice_panel').show();
                        }
                    });
                }
            });
        });
    </script>
    @endif
@endsection
