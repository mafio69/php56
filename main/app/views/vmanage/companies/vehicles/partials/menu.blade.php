<ul class="nav nav-tabs">
    @foreach($managed_fleet as $managed_company_name => $managed_company_id  )
        <li role="presentation" @if($company->id == $managed_company_id) class="active" @endif>
            <a href="{{ URL::action('VmanageVehiclesController@getIndex', [$managed_company_id])}}">{{$managed_company_name}}</a>
        </li>
    @endforeach
</ul>

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
            <h4 class="panel-title">Filtrowanie pojazdów</h4>
        </div>
        <div class="panel-body">
            <div class="form-group" style="margin-bottom:0px;">
                {{ Form::open(array('url' => URL::action('VmanageVehiclesController@postSearch'), 'id' => 'search-adv-form')) }}
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
                                <input type="checkbox" name="brand" value="1"
                                @if(Input::has('brand'))
                                       checked
                                        @endif
                                        >marka
                            </label>
                        </div>

                        <div class="btn-group" >
                            <label class="btn btn-info btn-check btn-xs">
                                <input type="checkbox" name="model" value="1"
                                @if(Input::has('model'))
                                       checked
                                        @endif
                                        >model
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
                                <input type="checkbox" name="vin" value="1"
                                @if(Input::has('vin'))
                                       checked
                                        @endif
                                        >VIN
                            </label>
                        </div>
                        <div class="btn-group" >
                            <label class="btn btn-info btn-check btn-xs">
                                <input type="checkbox" name="nr_contract" value="1"
                                       @if(Input::has('nr_contract'))
                                       checked
                                        @endif
                                >nr umowy leas.
                            </label>
                        </div>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-info btn-check btn-xs">
                                <input type="checkbox" name="vmanage_user" value="1"
                                @if(Input::has('vmanage_user'))
                                       checked
                                        @endif
                                        >bieżący użytkownik
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <button class="btn btn-primary btn-sm pull-right" id="search-adv" style="width:100%;"> <i class="fa fa-search"></i> szukaj </button>
                    </div>
                </div>
                {{ Form::close() }}
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
