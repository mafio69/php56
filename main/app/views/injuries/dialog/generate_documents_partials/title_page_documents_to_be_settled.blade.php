<style>
    .panel-success {
            border-color: #d6e9c6;
        }
        .panel-success > .panel-heading {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .panel-success > .panel-heading + .panel-collapse > .panel-body {
            border-top-color: #d6e9c6;
        }
        .panel-success > .panel-heading .badge {
            color: #dff0d8;
            background-color: #3c763d;
        }
        .panel-success > .panel-footer + .panel-collapse > .panel-body {
            border-bottom-color: #d6e9c6;
    }
</style>

<div class="row">
    <div class="col-sm-6 marg-btm border-right">
        
    <div class="alert alert-info" role="alert">
        <span>Faktury przekazane</span>
        <table class="table table-hover" id='invoices-select' name='invoice_id' style="margin: 5px">
            @foreach ($injury->forwardedInvoices as $invoice)
                <tr>
                    <td style="width: 1px; white-space: nowrap">
                        <input class="selected-invoice" type="checkbox" id="{{$invoice->id}}" class="" name="invoices[]" value="{{$invoice->id}}"
                    data-invoice-nr="{{$invoice->invoice_nr}}" data-invoice-date="{{$invoice->invoice_date}}"/>
                    </td>
                    <td style="text-align:left;">
                        <b>{{$invoice->invoice_nr.' z dnia '.$invoice->invoice_date}}</b>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    
    </div>
    <div class="col-sm-6 marg-btm">
        <div class="panel-group" id="accordion-cessions" role="tablist" aria-multiselectable="true">
            @foreach($injury->forwardedInvoices as $invoice)
                <div class="panel panel-default" id="panel_{{$invoice->id}}">
                    <div class="panel-heading" role="tab" id="heading_{{$invoice->id}}">
                    <h5 class="panel-title pointer" data-toggle="collapse" data-parent="#accordion" href="#collapse_{{$invoice->id}}" aria-expanded="true" aria-controls="collapseOne">
                            <i class="fa fa-arrows-v pull-right"></i>
                            Faktura {{$invoice->invoice_nr}} z dnia {{$invoice->invoice_date}}
                        </h5>
                    </div>
                    <div id="collapse_{{$invoice->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_{{$invoice->id}}">
                        <div class="panel-body">
                            <div class="alert alert-info">
                                <span>Dokumenty wybrane przy przekazywaniu</span>
                                <table class="table table-hover" id='forwarded-docs_{{$invoice->id}}'>
                                </table>
                            </div>
                    
                            <div class="alert alert-info">
                                <span>Uwagi do faktury</span>
                                {{ Form::textarea('description_'.$invoice->id, '', array('class' => 'form-control description', 'id'=>'description_'.$invoice->id, 'name' => 'description['.$invoice->id.']','placeholder' => 'Uwagi', 'style' => 'margin-left: 5px; height: 100px')) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
    </div>

    <div class="col-sm-12">
        <br>Potwierdź wygenerowanie dokumentu.
    </div>
</div>

<script>

var documentsFetched = [];

function fetchForwardDocuments(invoiceID) {
    if(typeof documentsFetched[invoiceID] === 'undefined') {
        $.ajax({
            url: '/injuries/get-invoiceforward-documents/'+invoiceID ,
            type:"GET",
            dataType:"json",

            success:function(data) {
                $('#forwarded-docs').empty();
                var duplicates = [];
                var duplicatesCountById = [];
                var totalCounter = 0;
                var htmlData = ""
                $.each(data, function(key, value) {
                    if(value.injury_invoice_forward_document_type_id > 3) {
                        totalCounter++;
                        htmlData += "<tr><td style='width: 1px; white-space: nowrap'><input type='checkbox' checked id='test" + value.id + "' class='doc-checkbox' name='document_types[" + value.injury_invoice_id + "][]' value='" + value.type.name + "'/></td><td style='text-align:left;'><b>" + value.type.name + "</b></td></tr>";
                    } else if (value.injury_invoice_forward_document_type_id == 2) {
                        $.each(value.compensations, function(document_key, compensation) {
                            htmlData +=  "<tr><td style='width: 1px; white-space: nowrap'><input type='checkbox' checked id='test" + compensation.id + "' class='doc-checkbox' name='document_types[" + value.injury_invoice_id + "][]' value='Decyzja z dnia " + compensation.date_decision + " na kwotę " + compensation.compensation + " zł'/></td><td style='text-align:left;'><b> Decyzja z dnia " + compensation.date_decision + " na kwotę " + compensation.compensation + " zł</b></td></tr>";
                        });
                    }
                });
                if (totalCounter == 0) {
                    htmlData += "<tr><td style='width: 1px; white-space: nowrap'></td><td style='text-align:left;'><b>BRAK DOKUMENTÓW SPEŁNIAJĄCYCH KRYTERIA DLA WYBRANEJ FAKTURY</b></td></tr>";
                }

            $('#forwarded-docs_' + invoiceID).append(htmlData);
            documentsFetched[invoiceID] = htmlData;
            }
        });
    }
}

$('.doc-checkbox').change(function() {
    invoiceId = $('#invoices-select-for-docs').children("option:selected").val();
    documentsFetched[invoiceId] = $('#forwarded-docs').html();
});

$('.selected-invoice').change(function() {

    invoiceId = this.value
    if(this.checked) {
        $('#panel_' + invoiceId).addClass('panel-success');
        fetchForwardDocuments(invoiceId);
    } else {
        $('#panel_' + invoiceId).removeClass('panel-success');
    }
});

</script>