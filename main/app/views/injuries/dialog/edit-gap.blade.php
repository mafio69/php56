 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych GAP</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::to('injuries/edit-gap', array($id)) }}" method="post"  id="dialog-form">

        {{Form::token()}}
       <div class="form-group">
           <div class="row">
               <div class="col-sm-12 marg-btm">
                   <label>Zakład ubezpieczeń</label>
                   {{ Form::select('insurance_company_id', $insuranceCompanies, $injury->injuryGap->insurance_company_id, ['class' => 'form-control']) }}
               </div>
           </div>
            <div class="row">
              <div class="col-sm-12 marg-btm">
                <label >GAP:</label>
                {{ Form::select('gap_type_id', $gapTypes, $injury->injuryGap->gap_type_id , array('class' => 'form-control '))  }}
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 marg-btm">
                <label >Suma ubezpieczenia [zł]:</label>
                {{ Form::text('insurance_amount', $injury->injuryGap->insurance_amount, array('class' => 'form-control number ', 'placeholder' => 'suma ubezpieczenia'))  }}
              </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label>[netto/brutto]:</label>
                    <select name="netto_brutto" class="form-control "  >
                        <option value="1"
                        @if($injury->injuryGap->netto_brutto == 1)
                          selected
                        @endif
                        >netto</option>
                        <option value="2"
                        @if($injury->injuryGap->netto_brutto == 2)
                          selected
                        @endif
                        >brutto</option>
                    </select>
                </div>
            </div>
           <div class="row">
               <div class="col-sm-12 marg-btm">
                   <label >Prognoza GAP [zł]:</label>
                   {{ Form::text('forecast', $injury->injuryGap->forecast, array('class' => 'form-control number', 'placeholder' => 'prognoza GAP'))  }}
               </div>
           </div>
            <div class="row">
              <div class="col-sm-12 marg-btm">
                <label >Nr szkody w TU:</label>
                {{ Form::text('injury_number', $injury->injuryGap->injury_number, array('class' => 'form-control', 'placeholder' => 'numer szkody w TU'))  }}
              </div>
            </div>
       </div>

  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zapisz</button>
  </div>
