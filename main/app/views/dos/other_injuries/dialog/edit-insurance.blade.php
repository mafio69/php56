 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych polisy leasingowej</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('dos.other.injuries.setEditInjuryInsurance', array($id)) }}" method="post"  id="dialog-injury-form">

        {{Form::token()}}
       <div class="form-group">
            <div class="row">
              <div class="col-sm-12 marg-btm">
                <label >Suma ubezpieczenia [zł]:</label>
                {{ Form::text('insurance', $object->insurance, array('class' => 'form-control required', 'placeholder' => 'suma ubezpieczenia'))  }}
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 marg-btm">
                <label >Wkład własny [zł]:</label>
                {{ Form::text('contribution', $object->contribution, array('class' => 'form-control required', 'placeholder' => 'wkład własny'))  }}
              </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label>[netto/brutto]:</label>
                    <select name="netto_brutto" class="form-control "  >
                        <option value="1"
                        @if($object->netto_brutto == 1)
                          selected
                        @endif
                        >netto</option>
                        <option value="2"
                        @if($object->netto_brutto == 2)
                          selected
                        @endif
                        >brutto</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label for="gap">GAP:</label>
                    <select name="gap" class="form-control">
                        @foreach(Config::get('definition.insurance_options_definition') as $k => $option)
                        <option value="{{$k}}"
                            @if($object->gap == $k)
                            selected
                            @endif
                        >{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label for="legal_protection">Ochrona prawna:</label>
                    <select name="legal_protection" class="form-control">
                        @foreach(Config::get('definition.insurance_options_definition') as $k => $option)
                        <option value="{{$k}}"
                            @if($object->legal_protection == $k)
                            selected
                            @endif
                        >{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
       </div>

  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
  </div>
