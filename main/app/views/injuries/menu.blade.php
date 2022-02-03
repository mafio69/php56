<div class="row">
<div class="col-sm-12">
  <ul  class="nav nav-pills nav-injuries btn-sm">
    @if(Auth::user()->can('zlecenia_(szkody)#szkody_nieprzetworzone'))
        <li class="<?php if(Request::segment(2) == 'unprocessed') echo 'active'; ?> ">
          <a href="{{ URL::route('injuries-unprocessed' ) }}">Nieprzetworzone <span class="badge">{{ isset($counts[-1]) ? $counts[-1] : 0 }}</span></a>
        </li>
        <li class="<?php if(Request::segment(2) == 'ea') echo 'active'; ?> ">
            <a href="{{ URL::action('InjuriesController@getIndexEa') }}">Nieprzetworzone EA <span class="badge">{{ isset($counts[-100]) ? $counts[-100] : 0 }}</span></a>
        </li>
    @endif

    <li class="separated">|</li>

        @if(Auth::user()->can('zlecenia_(szkody)#szkody_zarejestrowane'))
      <li class="<?php if(Request::segment(2) == 'new') echo 'active'; ?> ">
            <a href="{{ URL::route('injuries-new' ) }}">
                Zarejestrowana
                <span class="badge">{{ isset($counts[0]) ? $counts[0] : 0 }}</span>
            </a>
      </li>
      <li class="<?php if(Request::segment(2) == 'inprogress') echo 'active'; ?>">
        <a href="{{ URL::route('injuries-inprogress' ) }}" >
            W obsłudze
            <span class="badge">{{ $counts[10] + $counts[11] + $counts[13] + $counts[14] }}</span>
        </a>
      </li>
        <li class="<?php if(Request::segment(2) == 'refused') echo 'active'; ?> ">
            <a href="{{ URL::route('injuries-refused' ) }}" >
                Odmowa
                <span class="badge">{{ $counts[20] + $counts[22] }}</span>
            </a>
        </li>
      <li class="<?php if(Request::segment(2) == 'completed') echo 'active'; ?> ">
            <a href="{{ URL::route('injuries-completed' ) }}" >Zakończone
            <span class="badge">{{ $counts[15] + $counts[16] + $counts[17] + $counts[18] + $counts[19] + $counts[21] + $counts[23] + $counts[24] + $counts[25] + $counts[26]  + $counts[38] }}</span>
            </a>
      </li>
    @endif
    <li class="separated">|</li>
        @if(Auth::user()->can('zlecenia_(szkody)#szkody_calkowite'))
      <li class="<?php if(REQUEST::segment(2) == 'total') echo 'active'; ?>">
        <a href="{{ URL::route('injuries-total' ) }}" >Szkoda całkowita
            <span class="badge">{{ $counts[30] + $counts[31] + $counts[32] + $counts[33] }}</span></a>
      </li>
    @endif

      <li class="<?php if(REQUEST::segment(2) == 'theft') echo 'active'; ?>">
        <a href="{{ URL::route('injuries-theft' ) }}" >Kradzież
            <span class="badge">{{ $counts[40] + $counts[41] + $counts[42] + $counts[43] }}</span></a>
      </li>

      <li class="<?php if(REQUEST::segment(2) == 'total-finished') echo 'active'; ?>">
        <a href="{{ URL::route('injuries-total-finished' ) }}" >Zakończone totalnie <span class="badge">{{ $counts['-7'] + $counts[34] + $counts[35] + $counts[36] + $counts[37] + $counts[44] + $counts[45] + $counts[46] + $counts[47] }}</span></a>
      </li>

    <li class="separated">|</li>

        @if(Auth::user()->can('zlecenia_(szkody)#szkody_anulowane'))
      <li class=" @if(Request::segment(2) == 'canceled') {{ 'active' }} @endif">
        <a href="{{ URL::route('injuries-canceled' ) }}" >Szkody anulowane </a>
      </li>
      <li class=" @if(Request::segment(2) == 'deleted') {{ 'active' }} @endif">
        <a href="{{ URL::route('injuries-deleted' ) }}" >Usunięte </a>
      </li>
    @endif

  </ul>
</div>

<div class="col-sm-12 search-box">
    <form method="post" id="search-adv-form" action="{{ URL::route('injuries.getSearch') }}">
        <ul class="nav nav-pills nav-injuries pull-right">
            @if(in_array(Request::segment(2), ['inprogress', 'completed']) )
                <li>
                    <div class="btn-group marg-right" data-toggle="buttons">
                        <label class="btn btn-sm btn-default filter @if(Input::has('garage_in_group')) active @endif">
                            <input name="garage_in_group" value="1" type="checkbox" autocomplete="off"
                                   @if(Input::has('garage_in_group')) checked @endif>
                            <i class="fa fa-wrench blue tips " title="serwis w grupie"></i>
                        </label>
                        <label class="btn btn-sm btn-default filter @if(Input::has('garage_without_group')) active @endif">
                            <input name="garage_without_group" value="1" type="checkbox" autocomplete="off"
                                   @if(Input::has('garage_without_group')) checked @endif>
                            <i class="fa fa-wrench red tips" title="serwis poza grupą"></i>
                        </label>
                        <label class="btn btn-sm btn-default filter @if(Input::has('proceed_without_garage')) active @endif">
                            <input name="proceed_without_garage" value="1" type="checkbox" autocomplete="off"
                                   @if(Input::has('proceed_without_garage')) checked @endif>
                            <i class="fa fa-ban tips" title="procedowane bez serwisu"></i>
                        </label>
                        <label class="btn btn-sm btn-default filter @if(Input::has('to_settle')) active @endif">
                            <input name="to_settle" value="1" type="checkbox" autocomplete="off"
                                   @if(Input::has('to_settle')) checked @endif>
                            <i class="fa fa-usd blue tips " title="do rozliczenia"></i>
                        </label>
                        <label class="btn btn-sm btn-default filter @if(Input::has('if_cfm')) active @endif">
                            <input name="if_cfm" value="1" type="checkbox" autocomplete="off"
                                   @if(Input::has('if_cfm')) checked @endif>
                            <i class="fa fa-credit-card blue  tips" title="CFM"></i>
                        </label>
                        <label class="btn btn-sm btn-default filter @if(Input::has('if_vip')) active @endif">
                            <input name="if_vip" value="1" type="checkbox" autocomplete="off"
                                   @if(Input::has('if_vip')) checked @endif>
                            <i class="fa fa-star text-warning  tips" title="klient VIP"></i>
                        </label>
                    </div>
                </li>
                <li class="separated">|</li>
            @endif
            <li>
                @if(Auth::user()->can('zlecenia_(szkody)#wyszukaj_szkode'))
                    @if(Input::has('term'))

                        <div class="label label-primary pull-right " style="margin-left:10px; font-size:14px;">
                            {{ Input::get('term') }}
                            <span class="badge marg-left search-clear tips pointer" title=""
                                  data-original-title="wyczyść wyszukiwanie">
                            <i class="fa fa-remove "></i>
                        </span>
                        </div>
                    @endif
                    <a class="pull-right" style="padding: 0px;">
                        <span class="btn btn-xs btn-primary show-search">
                            <i class="fa fa-fw fa-search  font-large"></i>
                            wyszukaj szkodę
                        </span>
                    </a>
                @endif
            </li>
        </ul>
        @if(Auth::user()->can('zlecenia_(szkody)#wyszukaj_szkode'))
            <div class="panel panel-default search-adv" style="    min-width: 500px;">
                <div class="panel-heading">
                    <h4 class="panel-title">Filtrowanie zgłoszeń</h4>
                </div>
                <div class="panel-body">
                    <div class="form-group" style="margin-bottom:0px;">
                        {{Form::token()}}
                        <div class="row ">
                            <div class="col-sm-12 marg-btm">
                                <input class="form-control" name="search_term" placeholder="wprowadź szukaną frazę"
                                       @if(Input::has('term'))
                                       value="{{ Input::get('term') }}"
                                        @endif
                                >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 marg-btm">
                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="case_nr" value="1"
                                               @if(Input::has('case_nr'))
                                               checked
                                                @endif
                                        >nr sprawy
                                    </label>
                                </div>

                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="registration" value="1"
                                               @if(Input::has('registration'))
                                               checked
                                                @endif
                                        >rejestracja
                                    </label>
                                </div>

                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="injury_nr" value="1"
                                               @if(Input::has('injury_nr'))
                                               checked
                                                @endif
                                        >nr szkody
                                    </label>
                                </div>

                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="leasing_nr" value="1"
                                               @if(Input::has('leasing_nr'))
                                               checked
                                                @endif
                                        >nr umowy leasingowej
                                    </label>
                                </div>

                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="firmID" value="1"
                                               @if(Input::has('firmID'))
                                               checked
                                                @endif
                                        >kod klienta
                                    </label>
                                </div>

                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="VIN" value="1"
                                               @if(Input::has('VIN'))
                                               checked
                                                @endif
                                        >VIN
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 marg-btm">

                                <div class="btn-group">
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="surname" value="1"
                                               @if(Input::has('surname'))
                                               checked
                                                @endif
                                        >nazwisko zgł./kierowcy
                                    </label>
                                </div>

                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="client" value="1"
                                               @if(Input::has('client'))
                                               checked
                                                @endif
                                        >nazwa firmy
                                    </label>
                                </div>

                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="NIP" value="1"
                                               @if(Input::has('NIP'))
                                               checked
                                                @endif
                                        >NIP firmy
                                    </label>
                                </div>

                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="invoice_number" value="1"
                                               @if(Input::has('invoice_number'))
                                               checked
                                                @endif
                                        >nr FV
                                    </label>
                                </div>

                                <div class="btn-group" >
                                    <label class="btn btn-info btn-check btn-xs">
                                        <input type="checkbox" name="global" value="1"
                                               @if(Input::has('global'))
                                               checked
                                                @endif
                                        >wyszukaj globalnie
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-10 col-sm-offset-1">
                                <button class="btn btn-primary btn-sm pull-right" id="search-adv" disabled
                                        style="width:100%"><i class="fa fa-search"></i> szukaj
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </form>
</div>
</div>

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            $('.search-clear').on('click', function () {
                $('#search-adv-form input:checkbox, #search-adv-form input[name="search_term"]').each(function () {
                    $(this).val('');
                    $(this).removeAttr('checked');
                });
                $('#search-adv-form #search-adv').click();
            });

            $('.btn-check input').change(function () {
                if ($(this).prop('checked')) {
                    $(this).closest('.btn').addClass('active');
                } else {
                    $(this).closest('.btn').removeClass('active');
                }
                var checked = false;
                $('.btn-check input').each(function () {
                    if ($(this).prop('checked') && $(this).attr('name') != 'global' && $('input[name=search_term]').val() != '') {
                        checked = true;
                    }
                });
                if (checked) {
                    $('#search-adv').prop('disabled', false);
                } else {
                    $('#search-adv').prop('disabled', true);
                }
            }).change();
            $('input[name=search_term]').focusout(function () {
                var checked = false;
                $('.btn-check input').each(function () {
                    if ($(this).prop('checked') && $(this).attr('name') != 'global' && $('input[name=search_term]').val() != '') {
                        checked = true;
                    }
                });
                if (checked) {
                    $('#search-adv').prop('disabled', false);
                } else {
                    $('#search-adv').prop('disabled', true);
                }
            });

            $('#search-adv-form').on('click', '#search-adv', function () {
                $.ajax({
                    type: "POST",
                    url: $('#search-adv-form').prop('action'),
                    data: $('#search-adv-form').serialize(),
                    assync: false,
                    cache: false,
                    success: function (data) {
                        self.location = data;
                    }
                });

                return false;
            });

            $('#search-adv-form .filter input').on('change', function () {
                $.ajax({
                    type: "POST",
                    url: $('#search-adv-form').prop('action'),
                    data: $('#search-adv-form').serialize(),
                    assync: false,
                    cache: false,
                    success: function (data) {
                        self.location = data;
                    }
                });

                return false;
            });

            $('input[name="search_term"]').bind("keypress", function (e) {
                if (e.which == 13) {
                    $.ajax({
                        type: "POST",
                        url: $('#search-adv-form').prop('action'),
                        data: $('#search-adv-form').serialize(),
                        assync: false,
                        cache: false,
                        success: function (data) {
                            self.location = data;
                        }

                    });

                    return false;
                }
            });

            $('#search_global').focusout(function () {
                setTimeout(function () {
                    if ($('#search_global').val().length == 7) {
                        $.ajax({
                            url: "<?php echo URL::route('injuries-search-getAll');?>",
                            data: {
                                registration: $('#search_global').val(),
                                _token: $('input[name="_token"]').val()
                            },
                            type: "POST",
                            async: false,
                            cache: false,
                            complete: function (data) {
                                if (data.responseText == '-1') {
                                    $('#modal-sm .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel">Komunikat</h4></div><div class="modal-body">Nie znaleziono szkód dla podanego numeru rejestracji.</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button></div>')
                                    $('#modal-sm').modal('show');
                                    $('#injuries-container').html('');
                                } else {
                                    $('.nav-injuries li').removeClass('active');
                                    $('#injuries-container').html(data.responseText);
                                }
                            }
                        });
                    } else {
                        $('#modal-sm .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel">Komunikat</h4></div><div class="modal-body">Niepoprawny format rejestracji (wymagane 7 znaków).</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button></div>')
                        $('#modal-sm').modal('show');
                        $('#injuries-container').html('');
                    }
                }, 100);
            }).autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo URL::route('vehicle-registration-getList');?>",
                        data: {
                            term: request.term,
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: "json",
                        type: "POST",
                        success: function (data) {
                            response($.map(data, function (item) {
                                return item;
                            }));
                        }
                    });
                },
                minLength: 2,
                open: function (event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                },
                select: function (event, ui) {
                    $(this).focusout();
                }
            }).bind("keypress", function (e) {
                if (e.which == 13) $(this).focusout();
            });
        });
    </script>
@stop
