 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych szkody</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('dos.other.injuries.setEditInjury', array($id)) }}" method="post"  id="dialog-form">

        {{Form::token()}}
       <div class="form-group">

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Typ szkody: <button type="button" class="btn btn-primary btn-xs" id="offender_info" data-toggle="modal" data-target="#modal-offender" style="display:none;"> dane sprawcy</button></label>
            <select name="injuries_type" id="injuries_type" class="form-control" >
              <?php foreach($injuries_type as $k => $v){?>
                <option value="{{ $v->id }}"
                  @if($injury->injuries_type_id == $v->id)
                   selected
                  @endif
                >{{ $v->name }} </option>
              <?php }?>
            </select>
          </div>
        </div>

        <div class="offender"
          @if($injury->injuries_type_id != 2 && $injury->injuries_type_id != 4 && $injury->injuries_type_id != 5)
            style="display:none;"
            <?php $offender = array();?>
          @else
            <?php $offender = $injury->offender()->first()->toArray();?>

          @endif
        >

          <h4 class="inline-header"><span><label>Dane sprawcy:</label></span></h4>
          <div class="row">
            <div class="col-md-6 marg-btm">
              {{ Form::text('offender_surname', isset($offender['surname']) ? $offender['surname'] : '', array('class' => 'form-control tips upper', 'placeholder' => 'nazwisko', 'title' => 'nazwisko'))  }}
            </div>
              <div class="col-md-6  marg-btm">
                {{ Form::text('offender_name', isset($offender['surname']) ? $offender['name'] : '', array('class' => 'form-control tips upper',  'placeholder' => 'imię', 'title' => 'imię'))  }}
              </div>
          </div>

          <h4 class="inline-header"><span>Adres zameldowania:</span></h4>
          <div class="row">
              <div class="col-md-6  marg-btm">
                {{ Form::text('offender_post', isset($offender['post']) ? $offender['post'] : '', array('class' => 'form-control tips upper',  'placeholder' => 'kod pocztowy', 'title' => 'kod pocztowy'))  }}
              </div>
              <div class="col-md-6  marg-btm">
                {{ Form::text('offender_city', isset($offender['city']) ? $offender['city'] : '', array('class' => 'form-control tips upper',  'placeholder' => 'miasto', 'title' => 'miasto'))  }}
              </div>
          </div>
          <div class="row">
              <div class="col-md-6  marg-btm">
                {{ Form::text('offender_street', isset($offender['street']) ? $offender['street'] : '', array('class' => 'form-control tips upper',  'placeholder' => 'ulica', 'title' => 'ulica'))  }}
              </div>
          </div>

          <h4 class="inline-header"><span>Dane pojazdu:</span></h4>
          <div class="row">
              <div class="col-md-6  marg-btm">
                {{ Form::text('offender_registration', isset($offender['registration']) ? $offender['registration'] : '', array('class' => 'upper  tips form-control ',  'placeholder' => 'nr rejestracyjny', 'title' => 'nr rejestracyjny'))  }}
              </div>
              <div class="col-md-6  marg-btm">
                {{ Form::text('offender_car', isset($offender['car']) ? $offender['car'] : '', array('class' => 'form-control tips upper',  'placeholder' => 'marka i model pojazdu', 'title' => 'marka i model pojazdu'))  }}
              </div>
          </div>
          <div class="row">
              <div class="col-md-6  marg-btm">
                {{ Form::text('offender_oc_nr', isset($offender['oc_nr']) ? $offender['oc_nr'] : '', array('class' => 'form-control tips upper',  'placeholder' => 'nr polisy OC', 'title' => 'nr polisy OC'))  }}
              </div>
              <div class="col-md-6  marg-btm">
                {{ Form::text('offender_zu', isset($offender['zu']) ? $offender['zu'] : '', array('class' => 'form-control tips upper',  'placeholder ' => 'nazwa ZU', 'title' => 'nazwa ZU'))  }}
              </div>
          </div>
          <div class="row">
              <div class="col-md-6  marg-btm">
                {{ Form::text('offender_expire', isset($offender['expire']) ? $offender['expire'] : '', array('class' => 'form-control tips',  'placeholder' => 'data ważności polisy', 'title' => 'data ważności polisy'))  }}
              </div>
              <div class="col-md-6  marg-btm">
              <select name="offender_owner" class="form-control tips" title="czy sprawca jest właścicielem pojazdu"  >
                <option value="1">Sprawca jest właścicielem pojazdu:</option>
                <option value="1"
                @if(isset($offender['owner']) && $offender['owner'] == 1)
                  selected
                @endif
                >tak</option>
                <option value="0"
                @if(isset($offender['owner']) && $offender['owner'] == 0)
                  selected
                @endif
                >nie</option>
              </select>
              </div>
          </div>
          <div class="row">
              <div class="col-md-12  marg-btm">
                {{ Form::textarea('offender_remarks', isset($offender['remarks']) ? $offender['remarks'] : '', array('class' => 'form-control tips',  'placeholder' => 'uwagi', 'style' => 'height:50px;', 'title' => 'uwagi'))  }}
              </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Odbiór odszkodowania:</label>
            <select name="receives" id="receives" class="form-control required" >
              <option value="">---wybierz---</option>
              <?php foreach($receives as $k => $v){?>
                <option value="{{$v->id}}"
                  @if($injury->receive_id == $v->id)
                    selected
                  @endif
                >{{ $v->name }}</option>
              <?php }?>
            </select>
          </div>
        </div>

        <div class="row receiver" @if($injury->receive_id != 1) style="display:none;" @endif>
            <div class="col-sm-6">
                {{ Form::text('receiver_name', $injury->receiver_name, array('class' => 'form-control tips marg-top ', 'placeholder' => 'nazwa warsztatu', 'title' => 'nazwa warsztatu'))  }}
            </div>
            <div class="col-sm-6">
                {{ Form::text('receiver_address', $injury->receiver_address, array('class' => 'form-control tips marg-top ', 'placeholder' => 'adres warsztatu', 'title' => 'adres warsztatu'))  }}
            </div>
            <div class="col-sm-12">
                <hr>
            </div>
        </div>

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Odbiór faktury:</label>
            <select name="invoicereceives" id="invoicereceives" class="form-control required" >
              <option value="">---wybierz---</option>
              <?php foreach($invoicereceives as $k => $v){?>
                <option value="{{$v->id}}"
                  @if($injury->invoicereceives_id == $v->id)
                    selected
                  @endif
                >{{ $v->name }}</option>
              <?php }?>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6 marg-btm">
            <label >Data zdarzenia:</label>
            {{ Form::text('date_event', $injury->date_event, array('class' => 'form-control required', 'id'=>'date_event', 'placeholder' => 'data zdarzenia'))  }}
          </div>
          <div class="col-sm-12 col-md-6 marg-btm">
            <label >Zakład ubezpieczeń:</label>
            <select name="insurance_company_id" id="insurance_company_id" class="form-control" >
              <?php foreach($insurance_company as $k => $v){?>
                <option value="{{$v->id}}"
                  @if($injury->object->insurance_company_id == $v->id)
                    selected
                  @endif
                >{{ $v->name }}</option>
              <?php }?>
            </select>
          </div>
          <div class="col-sm-12 col-md-6 marg-btm">
            <label >Nr szkody:</label>
            {{ Form::text('injury_nr', $injury->injury_nr, array('class' => 'form-control upper', 'id'=>'injury_nr', 'placeholder' => 'nr szkody'))  }}
          </div>
          <div class="col-sm-12 col-md-6 marg-btm">
            <label >Zawiadomiono policję:</label>
            <select name="police" id="police" class="form-control" >
              <option value="-1"
                    @if($injury->police == '-1')
                    selected
                    @endif
                >nie ustalono</option>
              <option value="0"
                @if($injury->police == 0)
                  selected
                @endif
              >nie</option>
              <option value="1"
                @if($injury->police == 1)
                  selected
                @endif
              >tak</option>
            </select>
          </div>
        </div>
        <div class="row police"
          @if($injury->police == '-1')
            style="display:none;"
          @endif
        >
          <div class="col-sm-12 col-lg-4">
            {{ Form::text('police_nr', $injury->police_nr, array('class' => 'form-control tips marg-top upper', 'id'=>'police_nr', 'placeholder' => 'nr zgłoszenia policji', 'title' => 'nr zgłoszenia policji'))  }}
          </div>

          <div class="col-sm-12 col-lg-4">
            {{ Form::text('police_unit', $injury->police_unit, array('class' => 'form-control tips marg-top upper', 'id'=>'police_unit', 'placeholder' => 'jednostka policji', 'title' => 'jednostka policji' ))  }}
          </div>

          <div class="col-sm-12 col-lg-4">
            {{ Form::text('police_contact', $injury->police_contact, array('class' => 'form-control tips marg-top upper', 'id'=>'police_contact',  'placeholder' => 'kontakt z policją', 'title' => 'kontakt z policją'))  }}
          </div>
            <div class="col-sm-12">
                <hr>
            </div>
        </div>

        <div class="row">
          <div class="col-sm-12">
            <label>Rodzaj zdarzenia:</label>
            <div class="row">
            <?php foreach ($type_incident as $k => $v) {?>
              <div class="col-md-6 ">
                <div class="radio">
                <label>
                  <input type="radio" name="zdarzenie" id="zdarzenie{{ $v->id }}" value="{{ $v->id }}"
                  @if($injury->type_incident_id == $v->id)
                    checked
                  @endif
                  >
                  {{ $v->name }}
                </label>
                </div>
              </div>
            <?php }?>
            </div>
          </div>
        </div>

       </div>

  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zapisz</button>
  </div>

  <script type="text/javascript">
    $(document).ready(function(){
      $('#date_event').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd" });
      $('input[name=offender_expire]').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,dateFormat: "yy-mm-dd" });

      $('#police').change(function(){
        if($(this).val() == 1){
          $('.police').show();
        }else
          $('.police').hide();
      });

      $('#injuries_type').change(function(){
        if($(this).val() == 2 || $(this).val() == 4 || $(this).val() == 5){
          $('.offender').show();
        }else
          $('.offender').hide();
      }).change();

      $('#receives').on('change', function () {
          if($(this).val() == 1)
          {
              $('.receiver').show();
          }else{
              $('.receiver').hide();
          }
      });
    });

  </script>
