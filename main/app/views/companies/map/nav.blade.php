<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <div class="row">
              <form class="navbar-form text-center" role="search" id="nav-form" >
                  {{ Form::token() }}
                  <div class="form-group">
                    <button class="btn btn-sm btn-primary" id="distance_use"><i class="fa fa-arrows-h"></i> Zmierz odległość</button>
                  </div>
                  <div class="form-group">
                      <label>
                        <input type="checkbox" class="check_group" name="range_use">
                        Zasięg warsztatów:
                      </label>
                      <input id="range" name="range" data-slider-id='rangeSlider' type="text" data-slider-min="1" data-slider-max="50" data-slider-step="1" data-slider-value="20"/>
                  </div>
                  <div class="form-group separator">
                  |
                  </div>
                  @foreach($groups as $group_id => $group_name)
                  <div class="checkbox marg-right">
                      <label>
                          <input type="radio" @if($group_id==1) checked @endif class="check_group" name="groups" value="{{$group_id}}"> {{ $group_name }}
                      </label>
                  </div>
                  @endforeach
                  <div class="checkbox marg-right">
                      <label>
                          <input type="radio" class="check_group" name="groups" value="0"> Pozostałe
                      </label>
                  </div>
                  <div class="form-group separator">
                      |
                  </div>
                  <div class="form-group form-group-sm">
                      <label class="marg-right">Typy warsztatów: </label>
                      {{ Form::select('typeGarages[]', $typegarages, null, ['class' => 'form-control', 'id' => 'typeGarages', 'multiple'])}}
                  </div>


            </div>
{{--            <div class="row">--}}
{{--                <div class="form-group col-sm-4">--}}
{{--                    <label  class="marg-right">Wyszukaj miejsce na mapie:</label>--}}
{{--                    <input name="search" class="form-control input-sm" id="search" placeholder="Wyszukaj miejsce" autocomplete="off"/>--}}
{{--                </div>--}}
{{--                <div class="form-group col-sm-4">--}}
{{--                    <label  class="marg-right">Obsługiwane marki osobowe:</label>--}}
{{--                    <input name="brands_c" class="form-control input-sm" id="brands_c" multiple ="multiple"  />--}}
{{--                </div>--}}
{{--                <div class="form-group col-sm-4">--}}
{{--                    <label  class="marg-right">Obsługiwane marki ciężarowe:</label>--}}
{{--                    <input name="brands_t" class="form-control input-sm" id="brands_t" multiple ="multiple "  />--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            --}}
            <div class="row">
                <div class="form-group col-sm-3">
                    <label  class="marg-right">Wyszukaj miejsce na mapie:</label>
                    <input name="search" class="form-control input-sm" id="search" placeholder="Wyszukaj miejsce" autocomplete="off"/>
                </div>
                <div class="form-group col-sm-3">
                    <label  class="marg-right">Obsługiwane marki osobowe:</label>
                    <input name="brands_c" class="form-control input-sm" id="brands_c" multiple ="multiple"  />
                </div>
{{--                TODO: Filtrowanie po autoryzacji--}}
                <div class="form-group col-sm-3">
                    <label  class="marg-right">Autoryzacje:</label>
                    <input name="authorizations" class="form-control input-sm" id="authorizations" multiple ="multiple"  />
                </div>
                <div class="form-group col-sm-3">
                    <label  class="marg-right">Obsługiwane marki ciężarowe:</label>
                    <input name="brands_t" class="form-control input-sm" id="brands_t" multiple ="multiple "  />
                </div>
            </div>
{{--            --}}

            <div class="row" style="margin-bottom:5px">
              <div class="btn-group" data-toggle="buttons">
                <button class="btn btn-info btn-xs active">
                  <input type="checkbox" autocomplete="off" checked name="markers[1]" class="markers"> <img src="/images/markers/blue.png" style="height:20px;padding:2px"> Obsługa samochodów osobowych
                </button>
                <button class="btn btn-info btn-xs active">
                  <input type="checkbox" autocomplete="off" checked name="markers[2]" class="markers"> <img src="/images/markers/red.png" style="height:20px;padding:2px"> Obsługa pojazdów ciężarowych
                </button>
                <button class="btn btn-info btn-xs active">
                  <input type="checkbox" autocomplete="off" checked name="markers[3]" class="markers"> <img src="/images/markers/purple.png" style="height:20px;padding:2px"> Obsługa samochodów osobowych i  pojazdów ciężarowych
                </button>
                <button class="btn btn-info btn-xs active">
                  <input type="checkbox" autocomplete="off" checked name="markers[4]" class="markers"> <img src="/images/markers/yellow.png" style="height:20px;padding:2px"> Niezdefiniowane typy serwisu
                </button>
                <button class="btn btn-info btn-xs active">
                  <input type="checkbox" autocomplete="off" checked name="markers[5]" class="markers"> <img src="/images/markers/black.png" style="height:20px;padding:2px"> Serwis zawieszony
                </button>
              </div>
            </div>
            </form>
        </div>
    </div>
</nav>
