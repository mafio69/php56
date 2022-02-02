<?php //wniosek o handlowy ubytek wartości pojazdu ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="templates-src/css/notification-{{ $owner_group->name }}.css" rel="stylesheet">
    <title></title>
</head>
<style>
    * {
        font-family: Arial, Helvetica, sans-serif;
    }
    div {
        font-size: 11pt;
    }
    p {
        font-size: 11pt
    }
    span {
        border-bottom: lightgrey solid 1px;
    }
</style>
<body style="margin: 2cm">

            @include('injuries.docs_templates.modules.place')
        <div style="text-align: center; margin-top: 2.5cm">
                DYSPOZYCJA PRZELEWU
        </div>
        <p style="margin-bottom: 1.0cm">
            BENEFICJENT:<br><br>
            <?php $invoice = InjuryInvoices::find(Input::get('invoices'));?>
            @if(!is_null($invoice->branch))
                {{$invoice->branch->short_name}}<br>
                {{$invoice->branch->street}}<br>
                {{$invoice->branch->code}} {{$invoice->branch->city}}
            @endif
        </p>
        
        <div>
            <p style="margin-bottom: 1.5cm">

                @if($injury->compensations->count()>0)
                <?php $sumCompensation = 0; $sum_counter = 0;?>
                    @foreach($injury->compensations as $k => $compensation)
                       @if($compensation->receive_id == 2)
                       <?php $sum_counter++;?>                              
                        KWOTA:
                            @if(!is_null($compensation->compensation))
                                @if($compensation->injury_compensation_decision_type_id == 7)
                                    <?php  $compensation->compensation = abs($compensation->compensation) * -1; ?>
                                @endif
                                <?php $sumCompensation += $compensation->compensation; ?>
                                {{ number_format(checkIfEmpty($compensation->compensation, null, 0), 2, ",", " ") }} zł
                            @else
                            {{ number_format(0, 2, ",", " ") }} zł
                            @endif
                            wpływ do IGL, decyzja wypłaty z dnia: {{!is_null($compensation->date_decision)?$compensation->date_decision:'---'}}<br>
                        @endif
                    @endforeach
                    @if($sum_counter>1)ŁĄCZNIE: {{number_format($sumCompensation, 2, ",", " ")}} zł @endif
                @endif
            </p>
            <p>
                Zwrot na rachunek BENEFICJENTA: {{Input::has('accounts')?$inputs['accounts']:'---'}}<br>
                z tytuły szkody nr: {{$injury->injury_nr}} <br>
                nr umowy: {{$injury->vehicle->nr_contract}}
            </p>

        </div>
            <table style="width: 100%; font-weight:normal; margin-top:50pt;">           
                <tbody>
                    <tr>
                        <td style="width:50%; ">ZATWIERDZIŁ</td>
                        <td style="text-align:center;">SPORZĄDZIŁ</td>
                    </tr>
                    <tr>
                        <td style="width:50%; "></td>
                        <td style="text-align:center;">
                            @include('modules.signatures')
                        </td>
                    </tr>
                </tbody>
        </table>

</body>
</html>
