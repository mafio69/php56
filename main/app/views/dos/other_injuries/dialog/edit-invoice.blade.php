 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja faktury</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('dos.other.injuries.setInvoice', array($id)) }}" method="post"  id="dialog-injury-form">

      {{Form::token()}}
      <div class="form-group">
        @if($invoice->injury_files->category == 4)
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Faktura korygowana:</label>
            <select name="parent_id" class="form-control">
              <option value="0">wybierz</option>
              @foreach(InjuryInvoices::where('injury_id', '=', $invoice->injury_id)->where('active','=','0')->where('parent_id', '=', '0')->where('id', '!=', $id)->get() as $k => $v)
                <option value="{{$v->id}}" 
                  @if($v->id == $invoice->parent_id) 
                   select="selected"
                  @endif
                >
                  {{$v->invoice_nr}}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        @endif
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Odbiorca faktury:</label>
            <select name="invoicereceives_id" class="form-control">
              @foreach(Invoicereceives::get() as $k => $v)
                <option value="{{$v->id}}" 
                  @if($v->id == $invoice->invoicereceives_id) 
                   selected
                  @endif
                >
                  {{$v->name}}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Nr faktury:</label>
            {{ Form::text('invoice_nr', $invoice->invoice_nr, array('class' => 'form-control  ',  'placeholder' => 'nr faktury')) }} 
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Data wystawienia:</label>
            {{ Form::text('invoice_date', ($invoice->invoice_date == '0000-00-00') ? '' : $invoice->invoice_date , array('class' => 'form-control  ',  'placeholder' => 'data wystawienia')) }} 
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <div class="clearfix">
              <label >Termin płatności:</label>
            </div>
            <div class="col-sm-6">
              {{ Form::text('payment_date', ($invoice->payment_date == '0000-00-00') ? '' : $invoice->payment_date , array('class' => 'form-control  tips',  'placeholder' => 'termin płatności - data', 'title' => 'termin płatności - data')) }}
            </div>
            <div class="col-sm-6">
              <?php
                $invoice_date = strtotime($invoice->invoice_date);
                $expireDay = strtotime($invoice->payment_date);
                $timeToPay = ($expireDay - $invoice_date)/ 86400;
              ?>
              {{ Form::text('payment_date_days', ($invoice->payment_date == '0000-00-00') ? '' : $timeToPay , array('class' => 'form-control tips',  'placeholder' => 'termin płatności w dniach', 'title' => 'termin płatności w dniach')) }}  
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Kwota netto:</label>
            {{ Form::text('netto', money_format("%.2n",$invoice->netto), array('class' => 'form-control  number',  'placeholder' => 'kwota netto')) }} 
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Kwota VAT:</label>
            {{ Form::text('vat', money_format("%.2n",$invoice->vat), array('class' => 'form-control  number',  'placeholder' => 'kwota vat')) }} 
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Wartość brutto:</label>
            {{ Form::text('brutto', money_format("%.2n",$invoice->vat+$invoice->netto), array('class' => 'form-control', 'disabled' => 'disabled',  'placeholder' => 'wartość brutto')) }} 
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <div class="checkbox ">
              <label>
                <input type="checkbox" name="commission" value="1"
                @if($invoice->commission == 1)
                  checked="checked"
                @endif
                >
                Licz do prowizji
              </label>
            </div>
          </div>
        </div>
        <div class="row" id="base_netto_content"
        @if($invoice->commission == 0)
          style="display:none;"
        @endif
        >
          <div class="col-sm-12 marg-btm">
            <label >Podstawa netto:</label>
            {{ Form::text('base_netto', ($invoice->base_netto == 0) ? money_format("%.2n",$invoice->netto) : money_format("%.2n",$invoice->base_netto), array('class' => 'form-control number',  'placeholder' => 'kwota podstawy netto')) }} 
          </div>
        </div>


      </div> 
              
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
  </div>

  <script type="text/javascript">
    $(document).ready(function(){
      $('input[name="commission"]').on('click', function(){
        if ($(this).is (':checked')){
          $('#base_netto_content').show();
          $('input[name="base_netto"]').val( $('input[name="netto"]').val() );
        }else
          $('#base_netto_content').hide();
      });

      $('input[name="netto"], input[name="vat"]').on('keyup', function(){
        if( $('input[name="netto"]').val() != '' )
          netto = parseFloat($('input[name="netto"]').val());
        else
          netto = parseFloat(0);

        if( $('input[name="vat"]').val() != '' )
          vat = parseFloat($('input[name="vat"]').val());
        else
          vat = parseFloat(0);

        $('input[name="brutto"]').val( parseFloat(netto + vat).toFixed(2)  );
      });

      $('input[name="invoice_date"], input[name="payment_date"]').datepicker({ showOtherMonths: true, selectOtherMonths: true,
        'changeMonth':true
      });

      $('input[name="payment_date"]').on('change', function(){
        end_date = new Date( $(this).val()+' 00:00:00' );
        start_date = new Date( $('input[name="invoice_date"]').val()+' 00:00:00' );
        var diff = Math.abs(end_date-start_date);
        var one_day = 1000*60*60*24;
        diff = Math.round(diff/one_day);
        $('input[name="payment_date_days"]').val(diff);
      });

      $('input[name="payment_date_days"]').on('keyup', function(){
        if($(this).val() != ''){
          start_date = new Date( $('input[name="invoice_date"]').val()+' 00:00:00' );
          diff = parseInt($(this).val());
          start_date.setDate(start_date.getDate() + diff);

          var dd = start_date.getDate();
          var mm = start_date.getMonth() + 1;
          var y = start_date.getFullYear();

          $('input[name="payment_date"]').val(y+'-'+mm+'-'+dd);
        }
      });

      $('input[name="base_netto"]').on('keyup', function(){
        $('#base_error').remove();
        if($(this).val() == '')
          $('<label for="base_netto" id="base_error" class="error">Pole wymagene.</label>').insertAfter($(this));
        else{
          base = parseFloat($('input[name="netto"]').val());
          base_netto = parseFloat($(this).val());
          if(base< base_netto)
            $('<label for="base_netto" id="base_error" class="error">Podstawa netto nie może być większa od wartości netto faktury.</label>').insertAfter($(this));
        }
      });
        
    });

  </script>