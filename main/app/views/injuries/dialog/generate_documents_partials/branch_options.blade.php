<?php
    $branches_enabled = false;
    //pokazuj tylko jezeli do faktur przypisane są inne oddziały niz przypisany do szkody
    foreach($injury->activeInvoices as $invoice) {
        if($invoice->branch_id && $invoice->branch_id != $injury->branch_id) {
            $branches_enabled = true;
            break;
        }
    }
    ?>

@if($branches_enabled)
    <div style="margin-bottom: 20px; margin-top: 10px">
            <h5>Wybierz serwis</h5>
            <select class="form-control" id='branch' name='branch'>
                @if($injury->branch)
                    <option value='{{$injury->branch_id}}'>
                        {{'Oryginalnie przypisany serwis '.$injury->branch->name}}
                    </option>
                @endif
                @foreach ($injury->activeInvoices as $key => $invoice)
                    @if($invoice->branch && $invoice->branch->id != $injury->branch_id)
                        <option value='{{$invoice->branch_id}}'>
                            {{'Faktura '.$invoice->invoice_nr.' z dnia '.$invoice->invoice_date}}
                        </option>
                    @endif
                @endforeach
            </select>
            <div style="margin-top: 10px" id="branch_data" style="dispaly: none">
                <b>Nazwa: </b><span id="branch_name"></span><br>
                <b>NIP: </b><span id="branch_nip"></span><br>
                <b>Adres: </b><span id="branch_address"></span>
            </div>
    </div>
<script>
    var branches = [];

    function selectBranch() {
        var selected_id =  $('#branch').children("option:selected"). val();
        $.each(branches, function(key, value){
            if(value.id == selected_id) {
                $('#branch_data').show();
                $('#branch_name').text(value.short_name);
                $('#branch_nip').text(value.nip);
                $('#branch_address').text(value.street + ' ' + value.code + ', ' + value.city);
            }
        })
    }

    $(document).ready(function (){
            if('{{$injury->branch}}') branches.push(JSON.parse('{{$injury->branch}}'));
            invoices = JSON.parse('{{$injury->activeInvoices}}');
            $.each(invoices, function(key, value){
                if(value.branch_id) branches.push(value.branch);           
            })
            selectBranch();
    })

    $('#branch').change(function () {
        selectBranch();
    })
</script>
@endif