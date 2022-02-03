 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych polisy AC</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('injuries-setEditInjuryInsurance', array($id)) }}" method="post"  id="dialog-injury-form">

        {{Form::token()}}
       <div class="form-group">
           <div class="row">
               <div class="col-sm-12 marg-btm">
                   <label>TU</label>
                   {{ Form::select('insurance_company_id', $insuranceCompanies, $policy->insurance_company_id, ['class' => 'form-control']) }}
               </div>
           </div>
            <div class="row">
              <div class="col-sm-12 marg-btm">
                <label >Suma ubezpieczenia [zł]:</label>
                {{ Form::text('insurance', $policy->insurance, array('class' => 'form-control required', 'placeholder' => 'suma ubezpieczenia'))  }}
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 marg-btm">
                <label >Wkład własny [zł]:</label>
                {{ Form::text('contribution', $policy->contribution, array('class' => 'form-control required', 'placeholder' => 'wkład własny'))  }}
              </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label>[netto/brutto]:</label>
                    <select name="netto_brutto" class="form-control "  >
                        <option value="1"
                        @if($policy->netto_brutto == 1)
                          selected
                        @endif
                        >netto</option>
                        <option value="2"
                        @if($policy->netto_brutto == 2)
                          selected
                        @endif
                        >brutto</option>
                        <option value="3"
                        @if($policy->netto_brutto == 3)
                            selected
                        @endif
                        >
                            netto +50%
                        </option>
                    </select>
                </div>
            </div>
            <div class="row">
              <div class="col-sm-12 marg-btm">
                <label >Nr polisy:</label>
                {{ Form::text('nr_policy', $policy->nr_policy, array('class' => 'form-control', 'placeholder' => 'numer polisy'))  }}
              </div>
            </div>
           <div class="row">
               <div class="col-sm-12 marg-btm">
                   <label>Zakres ubezpieczenia:</label>
                   {{ Form::text('risks', $policy->risks, ['class' => 'form-control', 'placeholder' => 'zakres ubezpieczenia']) }}
               </div>
           </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label for="gap">GAP:</label>
                    <select name="gap" class="form-control">
                        @foreach(Config::get('definition.insurance_options_definition') as $k => $option)
                        <option value="{{$k}}"
                            @if($policy->gap == $k)
                            selected
                            @endif
                        >{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                @if(! $injury->injuryGap)
                    <div class="col-sm-12 marg-btm" id="gap_insurance_company_id" style="display: none;">
                        {{ Form::select('gap_insurance_company_id',$gapInsuranceCompanies, null, ['class' => 'form-control']) }}
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label for="legal_protection">Ochrona prawna:</label>
                    <select name="legal_protection" class="form-control">
                        @foreach(Config::get('definition.insurance_options_definition') as $k => $option)
                        <option value="{{$k}}"
                            @if($policy->legal_protection == $k)
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
  <script>
  $('[name="gap"]').change(function(){
    if($('[name="gap"] option:selected').val()==1){
        $('#gap_insurance_company_id').show();
    }
    else{
        $('#gap_insurance_company_id').hide();
    }
  });
  </script>
