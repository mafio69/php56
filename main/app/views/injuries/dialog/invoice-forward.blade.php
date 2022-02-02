<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przekazanie faktury</h4>
</div>

<div class="modal-body" style="overflow:hidden;">

    
    <form id="forward-invoice">

        {{Form::token()}}
        
        @if($invoice->injury_files->category == 3)
        Dokumenty przekazane z FV:
                <table class="table table-hover">
        @foreach($injuryInvoiceForwardDocumentTypes as $dT)
        {{$dT->id}}
                    @if($dT->id != 1 && $dT->id != 3)
                        <tr>
                            <td style="width: 1px; white-space: nowrap">
                                <input type="checkbox" id="test{{$dT->id}}" class="" name="document_types[]" value="{{$dT->id}}"/>
                            </td>
                            <td style="text-align:left;">
                                <b>{{$dT->name}}</b>
                                @if($dT->id == 2)
                                <div id="compensations" style="display: none;">
                                <table class="table" style="margin: 0px">
                                @foreach($compensations as $compensation)
                                        <tr>
                                            <td style="width: 1px; white-space: nowrap">
                                                <input type="checkbox" id="test{{$compensation->id}}" class="" name="compensations[]" value="{{$compensation->id}}"/>
                                            </td>
                                            <td style="text-align:left;">
                                            <b>
                                                @if($compensation->injury_compensation_decision_type_id == 7)
                                                    <?php  $compensation->compensation = abs($compensation->compensation) * -1; ?>
                                                @endif
                                                {{$compensation->date_decision}} na kwotę {{number_format(checkIfEmpty($compensation->compensation, null, 0), 2, ",", " ") }} zł
                                            </b>
                                            </td>
                                        </tr>
                                @endforeach
                                </table>
                                </div>
                            @endif
                            </td>
                        </tr>
                    @endif
        @endforeach
                </table>
        @endif
        
        {{-- Potwierdź przekazanie faktury. --}}

        {{-- <div class="alert alert-info" role="warning" style="margin: 10px" id="subimt_agreement">
            <span></span>
            <div class='center'>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="submit_agreement">
                    <label class="form-check-label" for="submit_agreement">Przekaż bez sprawdzania rachunków bankowych z białą listą</label>
                  </div>
        
            </div>
        </div> --}}
    @if(count($invoice->assignedBankAccountNumbers)>0)
    <div class="col-sm-12 alert alert-danger" role="alert" id="company_alert" style="margin: 10px; {{$invoice->assignedBankAccountNumbers->first()->if_user_insert?"":"display: none;"}}"> 
            <div class='center'>
                <span id="alert_message">Wybrano rachunek z poza białej listy. Czy na pewno chcesz przekazać FV?</span>
                <div class="form-check" style="margin: 10px">
                    <input type="checkbox" class="form-check-input" id="submit_agreement">
                    <label class="form-check-label" for="submit_agreement">Rozumiem, przekaż mimo to</label>
                  </div>
        
          </div>
      </div>
    @else
        <div class="col-sm-12 alert alert-warning" role="alert" id="company_alert" style="margin: 10px;">
            <div class='center'>
                <span>Nie wybrano numeru konta</span>
                <div class="form-check" style="margin: 10px">
                    <input type="checkbox" class="form-check-input" id="submit_agreement">
                    <label class="form-check-label" for="submit_agreement">Rozumiem, przekaż mimo to</label>
                </div>
          </div>
      </div>
    @endif
    <div class="col-sm-12">
        <p class="alert alert-danger" style="display: none;" id="response_message"></p>
    </div>
    <input type="hidden" name="forward_confirmed">
    <input type="hidden" name="allow_confirmed">
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
        <button id="submit" type="submit" class="btn btn-primary" {{count($invoice->assignedBankAccountNumbers)>0?($invoice->assignedBankAccountNumbers->first()->if_user_insert?"disabled":""):""}}>Potwierdź</button>
    </div>
    
</form>

</div>
<script type="text/javascript">
$(document).ready(function(){
    $('#forward-invoice').submit(function(request){
            $('#submit').attr("disabled", true);
            request.preventDefault();
            var submit_again = $(document.activeElement.getAttribute('id'));
            var submit_agreement = $('#submit_agreement').is(":checked");
            $('input[name="forward_confirmed"]').val(  (submit_again.selector == 'submit-again' || submit_agreement) ? 1 : 0 );

            $('input[name="allow_confirmed"]').val(  $('#allow').is(':checked'));
            $.ajax({
                url: "<?php echo  URL::to('/injuries/invoice/forward/'.$invoice->id);?>",
                dataType: "json",
                data: $('#forward-invoice').serialize(),
                type: "POST",
                success: function(data) {
                    if(data.status==500) {
                        $('#response_message').text(data.message).show().append('<div class="form-check text-center" style="margin: 10px"><input type="checkbox" class="form-check-input" id="allow" value="1"><label class="form-check-label" for="allow"> Rozumiem, przekaż mimo to</label></div>');
                        $('#submit').attr("disabled", false);
                        $('#company_alert').show();
                    }
                    if(data.status==200) {
                        $('#invoice-forward-modal').attr('hide')
                        location.reload();
                    }                   
                }
            });
        });

        $('#submit_agreement').on('change', function () {
            var accepted = $('#submit_agreement').is(':checked');
            if(accepted) {
                $('#submit').removeAttr("disabled");
            } else {
                $('#submit').attr("disabled", true);
            }
        });
    });

    $('#test2').change(function () {
        if(this.checked) $('#compensations').show();
        else $('#compensations').hide();
    })
</script>
