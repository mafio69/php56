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
      <h4 class="panel-title">Filtrowanie kart</h4>
    </div>
    <div class="panel-body">
        <div class="form-group" style="margin-bottom:0px;">
          <form method="post" id="search-adv-form" action="{{ URL::route('settings.liquidation_cards', array('setSearch') ) }}" >
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
                    <input type="checkbox" name="card_nr" value="1"
                    @if(Input::has('card_nr'))
                      checked
                    @endif
                    >nr karty
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
                    <input type="checkbox" name="nr_contract" value="1"
                    @if(Input::has('nr_contract'))
                      checked
                    @endif
                    >nr umowy
                  </label>
                </div>

                <div class="btn-group" >
                  <label class="btn btn-info btn-check btn-xs">
                    <input type="checkbox" name="expiration_date" value="1"
                    @if(Input::has('expiration_date'))
                      checked
                    @endif
                    >data ważności
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

      });
    </script>
@stop