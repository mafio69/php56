<div class="row">
    <div class="col-sm-12 marg-btm">
        <span>Wybór faktury</label>
        <select class="form-control" id='invoices-select' name='invoices' style="margin: 5px">
            @foreach ($injury->activeInvoices as $invoice)
                <option value='{{$invoice->id}}'>{{$invoice->invoice_nr.' z dnia '.$invoice->invoice_date}}</option>
            @endforeach
        </select>

        <span>Numery rachunków przypisane do faktury</span>
            <select class="form-control" id='accounts' name='accounts'  style="margin: 5px">
        </select>

        <br>Potwierdź wygenerowanie dokumentu.
    </div>
</div>

<script>

function fetchAccounts() {
    invoiceID = $('#invoices-select').children("option:selected").val();
    if(invoiceID) {
        $.ajax({
            url: '/injuries/get-invoice-bank-accounts/'+invoiceID,
            type:"GET",
            dataType:"json",

            success:function(data) {
                $('#accounts').empty();
                $.each(data, function(key, value) {   
                    $('#accounts')
                    .append($("<option></option>")
                    .attr("value",value['account_number'])
                    .text(value['account_number'])); 
                });
            }
        });
    } else {
        $('#accounts').empty();
    }
}


$('#invoices-select').on('change', function(){
        fetchAccounts();
});

$(document).ready(function() {
    fetchAccounts();
});
</script>