<div class="pull-left">
  <ul  class="nav nav-pills nav-injuries btn-sm">

      @if(Auth::user()->can('zlecenia#szkody_nieprzetworzone'))
        <li class="<?php if(Request::segment(4) == 'unprocessed') echo 'active'; ?> ">
          <a href="{{ URL::route('dos.other.injuries.unprocessed' ) }}">Nieprzetworzone
            <span class="badge">
              {{ sumByKeys($counts, [-1]) }}
            </span>
          </a>
        </li>
      @endif

      @if(Auth::user()->can('zlecenia#szkody_zarejestrowane'))
          <li class="<?php if(Request::segment(4) == 'new') echo 'active'; ?> ">
            <a href="{{ URL::route('dos.other.injuries.new' ) }}">
              Zarejestrowana
              <span class="badge">
                {{ sumByKeys($counts, [0]) }}
              </span>
            </a>
          </li>

          <li class="<?php if(Request::segment(4) == 'inprogress') echo 'active'; ?>">
            <a href="{{ URL::route('dos.other.injuries.inprogress' ) }}" >
              W obsłudze
              <span class="badge">
                {{ sumByKeys($counts, [10, 46]) }}
              </span>
            </a>
          </li>

          <li class="<?php if(Request::segment(4) == 'completed') echo 'active'; ?> ">
            <a href="{{ URL::route('dos.other.injuries.completed' ) }}" >Zakończone
              <span class="badge">
                {{ sumByKeys($counts, [15,17, 19, 20, 21, 41]) }}
              </span>
            </a>
          </li>
      @endif

      @if(Auth::user()->can('zlecenia#szkody_calkowite#kradzieze'))
          <li class="separated">|</li>
          <li class="<?php if(Request::segment(4) == 'total') echo 'active'; ?>">
            <a href="{{ URL::route('dos.other.injuries.total' ) }}" >Szkoda całkowita
              <span class="badge">
                {{ sumByKeys($counts, [25,26,27, 44]) }}
              </span>
            </a>
          </li>

          <li class="<?php if(REQUEST::segment(4) == 'theft') echo 'active'; ?>">
            <a href="{{ url('dos/other/injuries/theft') }}" >Kradzież
              <span class="badge">
                {{ sumByKeys($counts, [30,31,32, 45]) }}
              </span>
            </a>
          </li>

          <li class="<?php if(REQUEST::segment(4) == 'total-finished') echo 'active'; ?>">
            <a href="{{ URL::route('dos.other.injuries.total-finished' ) }}" >
              Zakończone totalnie
              <span class="badge">
                {{ sumByKeys($counts, [28,29,33,34, 42, 43]) }}
              </span>
            </a>
          </li>
      @endif

      @if(Auth::user()->can('zlecenia#szkody_anulowane'))
          <li class="separated">|</li>

          <li class="<?php if(REQUEST::segment(4) == 'ppi') echo 'active'; ?>">
            <a href="{{ URL::route('dos.other.injuries.ppi' ) }}" >
              PPI
              <span class="badge">
              {{ sumByKeys($counts, ['ppi']) }}
            </span>
            </a>
          </li>

        <li class="separated">|</li>

        <li class=" @if(Request::segment(4) == 'canceled') {{ 'active' }} @endif">
          <a href="{{ URL::route('dos.other.injuries.canceled' ) }}" >
            Szkody anulowane
            <span class="badge">
              {{ sumByKeys($counts, ['-10']) }}
            </span>
          </a>
        </li>
        <li class=" @if(Request::segment(2) == 'deleted') {{ 'active' }} @endif">
          <a href="{{ URL::route('dos.other.injuries.deleted' ) }}" >Usunięte
            <span class="badge">
            {{ $counts['-2'] }}
          </span>
        </a>
          
        </li>

      @endif
  </ul>
</div>


<div class="pull-right search-box">
  <div class="pull-right">
    @if(Input::has('term'))
      <span class="label label-primary pull-right " style="margin-left:10px; font-size:14px;">
      {{ Input::get('term') }}
      </span>
    @endif
    <i class="fa fa-search show-search font-xlarge  pull-right " ></i>
  </div>

  <div class="panel panel-default search-adv" >
    <div class="panel-heading">
      <h4 class="panel-title">Filtrowanie zgłoszeń</h4>
    </div>
    <div class="panel-body">
        <div class="form-group" style="margin-bottom:0px;">
          <form method="post" id="search-adv-form" action="{{ URL::route('dos.other.injuries.getSearch') }}" >
            {{Form::token()}}
            <div class="row ">
              <div class="col-sm-12 marg-btm">
                <input class="form-control" name="search_term" placeholder="wprowadź szukaną frazę"
                @if(Input::has('term'))
                  value ="{{ Input::get('term') }}"
                @endif
                >
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 marg-btm">
                <div class="btn-group" >
                  <label class="btn btn-info btn-check btn-xs" >
                    <input type="checkbox" name="case_nr" value="1"
                    @if(Input::has('case_nr'))
                      checked
                    @endif
                    >nr sprawy
                  </label>
                </div>

                <div class="btn-group">
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

              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 marg-btm">

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
                    <input type="checkbox" name="surname" value="1"
                    @if(Input::has('surname'))
                      checked
                    @endif
                    >nazwisko zgł.
                  </label>
                </div>

                <div class="btn-group" >
                  <label class="btn btn-info btn-check btn-xs">
                    <input type="checkbox" name="client" value="1"
                    @if(Input::has('client'))
                      checked
                    @endif
                    >nazwa firma
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
                <button class="btn btn-primary btn-sm pull-right" id="search-adv" style="width:100%"> <i class="fa fa-search"></i> szukaj </button>
              </div>
            </div>
          </form>
        </div>
    </div>
  </div>
</div>

@section('headerJs')
  @parent
    <script type="text/javascript">
      $(document).ready(function() {

        $('.btn-check input').change(function(){
          if( $(this).prop('checked') ){
            $(this).closest('.btn').addClass('active');
          }else{
            $(this).closest('.btn').removeClass('active');
          }
        }).change();

        $('#search-adv-form').on('click', '#search-adv', function(){
            $.ajax({
              type: "POST",
              url: $('#search-adv-form').prop( 'action' ),
              data: $('#search-adv-form').serialize(),
              assync:false,
              cache:false,
              success: function( data ) {
                self.location = data;
              }

            });

            return false;
        });
        $('input[name="search_term"]').bind("keypress", function(e) {
          if( e.which == 13 ){
            $.ajax({
              type: "POST",
              url: $('#search-adv-form').prop( 'action' ),
              data: $('#search-adv-form').serialize(),
              assync:false,
              cache:false,
              success: function( data ) {
                self.location = data;
              }

            });

            return false;
          }
        });

        $('#search_global').focusout(function(){
            setTimeout(function(){
              if( $('#search_global').val().length == 7){
                $.ajax({
                  url: "<?php echo  URL::route('injuries-search-getAll');?>",
                  data: {
                    registration: $('#search_global').val(),
                    _token: $('input[name="_token"]').val()
                  },
                  type: "POST",
                  async: false,
                  cache: false,
                  complete: function( data ) {
                    if(data.responseText == '-1'){
                      $('#modal-sm .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel">Komunikat</h4></div><div class="modal-body">Nie znaleziono szkód dla podanego numeru rejestracji.</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button></div>')
                      $('#modal-sm').modal('show');
                      $('#injuries-container').html('');
                    }else{
                      $('.nav-injuries li').removeClass('active');
                      $('#injuries-container').html(data.responseText);
                    }
                  }
                });
              }else{
                 $('#modal-sm .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel">Komunikat</h4></div><div class="modal-body">Niepoprawny format rejestracji (wymagane 7 znaków).</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button></div>')
                 $('#modal-sm').modal('show');
                 $('#injuries-container').html('');
              }
            }, 100);
        }).autocomplete({
            source: function( request, response ) {
              $.ajax({
                  url: "<?php echo  URL::route('vehicle-registration-getList');?>",
                  data: {
                    term: request.term,
                    _token: $('input[name="_token"]').val()
                  },
                  dataType: "json",
                  type: "POST",
                  success: function( data ) {
                      response( $.map( data, function( item ) {
                          return item;
                      }));
                  }
              });
            },
            minLength: 2,
            open: function(event, ui) {
              $(".ui-autocomplete").css("z-index", 1000);
            },
            select: function(event, ui) {
              $(this).focusout();
            }
        }).bind("keypress", function(e) {
          if( e.which == 13 ) $(this).focusout();
        });
      });
    </script>
@stop

