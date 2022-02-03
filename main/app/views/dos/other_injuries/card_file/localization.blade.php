<div class="tab-pane fade" id="localization">
  <div class="row">
    <div class="col-md-12 col-lg-12 ">
      <div class="form-group">
        <label>
          @if(Auth::user()->can('zlecenia#zarzadzaj'))
            <i class="fa fa-pencil-square-o pull-right tips modal-open" target="{{ URL::route('dos.other.injuries.getEditInjuryMap', array($injury->id)) }}" data-toggle="modal" data-target="#modal" title="edytuj" style="font-size: 20px;cursor: pointer;margin: 2px 0px 0px 10px;"></i>
          @endif
          Adres zdarzenia:
        </label>
        <p class="form-control">{{ $injury->event_post}} {{ $injury->event_city }} - {{ $injury->event_street }}</p>
      </div>
    </div>

  </div>

    @if($injury->lat != 0)
        <div id="map-canvas" style="width:100%; height:400px;"></div>
    @endif
</div>
