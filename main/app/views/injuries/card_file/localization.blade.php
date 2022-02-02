@if(Auth::user()->can('kartoteka_szkody#lokalizacja_zdarzenia'))
  <div class="tab-pane fade" id="localization">
    <div class="row">
      <div class="col-md-12 col-lg-12 ">
        <div class="form-group">
          <label>
            <i class="fa fa-pencil-square-o pull-right tips modal-open" target="{{ URL::route('injuries-getEditInjuryMap', array($injury->id)) }}" data-toggle="modal" data-target="#modal" title="edytuj" style="font-size: 20px;cursor: pointer;margin: 2px 0px 0px 10px;"></i>
            Adres zdarzenia:
          </label>
          <p class="form-control">{{ $injury->event_post}} {{ $injury->event_city }} - {{ $injury->event_street }}</p>
        </div>
      </div>

    </div>

    <div id="map-canvas" style="width:100%; height:400px;  "></div>
  </div>
@endif

