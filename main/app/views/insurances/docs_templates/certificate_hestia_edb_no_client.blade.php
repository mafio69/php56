<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="templates-src/css/backup/notification.css" rel="stylesheet">
    <title></title>
</head>
<body>
<div class="body content body-margin-big">
    <div style="display:block; ">
        <div style="width:100%; display:inline-block;">
            <img src="{{ 'templates-src/hestia.jpg' }}" style="height: 65px;"/>
        </div>
    </div>
    <div style="display:block;">
        <h1 style="text-align: center;">CERTYFIKAT UBEZPIECZENIA</h1>
        <h2 style="text-align: center;">NR {{ $policy->insurance_number }}</h2>
    </div>
    <div style="display:block; margin-top: 20px;">
        <table style="width: 100%;" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 50%;">
                    <strong>Numer umowy:</strong>
                    <br/>
                    {{ $policy->leasingAgreement->nr_contract }}
                </td>
                <td style="width: 50%; text-align: right;">
                    Ubezpieczenie obsługuje:
                    <br/>
                    <strong>Europejski Dom Brokerski sp. z o.o.</strong>
                </td>
            </tr>
        </table>
    </div>
    <div style="display:block; margin-top: 20px;">

        Umowa ubezpieczenia zawarta na podstawie Umowy Generalnej nr {{ $policy->generalContract() }} zawartej pomiędzy
        STU ERGO Hestia S.A., a {{ checkIfEmpty('1', $ideaA) }} oraz na podstawie poniższych warunków ubezpieczenia.

    </div>
    <div style="display:block; margin-top: 20px;">
        <table style="width: 100%;" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 25%;">
                    <span style="width:15px;height:15px;border:1px solid #000; padding: 1px; text-align: center; display: inline-block">
                        @if($policy->insuranceType->months == 12 && $inputs['insurances'] == 1) X @endif
                    </span>
                    Umowa roczna
                </td>
                <td style="width: 25%;">
                    <span style="width:15px;height:15px;border:1px solid #000; padding: 1px; text-align: center; display: inline-block">
                        @if($policy->insuranceType->months > 12 && $inputs['insurances'] == 1) X @endif
                    </span>
                    Umowa wieloletnia
                </td>
                <td style="width: 25%;">
                    <span style="width:15px;height:15px;border:1px solid #000; padding: 1px; text-align: center; display: inline-block">
                        @if($inputs['insurances'] == 1) X @endif
                    </span>
                    Ubezpieczenie nowe
                </td>
                <td style="width: 25%;">
                    <span style="width:15px;height:15px;border:1px solid #000; padding: 1px; text-align: center; display: inline-block">
                        @if($inputs['insurances'] > 1) X @endif
                    </span>
                    Wznowienie
                </td>
            </tr>
        </table>
    </div>
    <div style="display:block; margin-top: 20px;">
        <table style="width: 100%;" cellspacing="0" cellpadding="0">
            <tr class="tr-grey">
                <td style="width: 25%;" class="strong">
                    Ubezpieczający/ Ubezpieczony:
                </td>
                <td style="width: 25%;" class="strong">
                    {{ checkIfEmpty('1', $ideaA) }} <br>
                    {{ checkIfEmpty('2', $ideaA) }} <br>
                    {{ checkIfEmpty('3', $ideaA) }} {{ checkIfEmpty('13', $ideaA) }}
                </td>
                <td style="width: 25%;">

                </td>
                <td style="width: 25%;">

                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 5px;"></td>
            </tr>
            <tr class="tr-grey">
                <td style="width: 25%;" class="strong">
                    Miejsce ubezpieczenia:
                </td>
                <td style="width: 75%;" colspan="3">
                    {{ $inputs['place'] }}
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 5px;"></td>
            </tr>
            <tr class="tr-grey">
                <td style="width: 25%;" class="strong">
                    Okres ubezpieczenia:
                </td>
                <td style="width: 75%;" colspan="3">
                    od: {{ $policy->date_from }}, godz. 00:00 do: {{ $policy->date_to }} godz. 23:59
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 5px;"></td>
            </tr>
            <tr class="tr-grey">
                <td style="width: 25%;" class="strong">
                    Przedmiot ubezpieczenia:
                </td>
                <td style="width: 25%;" class="strong">
                    Suma ubezpieczenia w zł
                </td>
                <td style="width: 25%;" class="strong">
                    Stawka w ‰
                </td>
                <td style="width: 25%;" class="strong">
                    Składka w zł
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 5px;"></td>
            </tr>
            <tr class="tr-grey">
                <td style="width: 25%;">
                    @foreach($policy->leasingAgreement->objects as $k => $object)
                        {{ $object->name }}<br>
                    @endforeach
                </td>
                <td style="width: 25%;">
                    @if($policy->leasingAgreement->net_gross == 2)
                        {{ number_format($policy->leasingAgreement->loan_gross_value,2,"."," ") }} zł brutto
                    @else
                        {{ number_format($policy->leasingAgreement->loan_net_value,2,"."," ") }} zł netto
                    @endif
                </td>
                <td style="width: 25%;">
                    xxxxx
                </td>
                <td style="width: 25%;">
                    xxxxx
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 10px;"></td>
            </tr>
            <tr class="tr-grey">
                <td style="width: 25%;" class="strong">
                    Składka płatna przez Korzystającego w kwocie:
                </td>
                <td style="width: 25%;">
                    xxxxx
                </td>
                <td style="width: 25%;" class="strong">
                    Termin płatności:
                </td>
                <td style="width: 25%;">
                    xxxxx
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 10px;"></td>
            </tr>
            <tr class="tr-grey">
                <td style="width: 100%;" colspan="4" class="strong">
                    Składka płatna przelewem na konto xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 10px;"></td>
            </tr>
            <tr class="tr-grey">
                <td style="width: 100%;" colspan="4" class="strong">
                    Franszyza redukcyjna:
                    <?php
                        $group_rate = $policy->leasingAgreement->insurance_group_row()->first();
                        if($group_rate && $group_rate->rate){
                            if($group_rate->rate->deductible_value){
                                echo number_format($group_rate->rate->deductible_value,2,"."," ").' zł';
                            }
                            elseif($group_rate->rate->deductible_percent){
                                if($policy->leasingAgreement->net_gross == 2){
                                    $loan_value = $policy->leasingAgreement->loan_gross_value;
                                }else{
                                    $loan_value = $policy->leasingAgreement->loan_net_value;
                                }

                                echo number_format( (($group_rate->rate->deductible_percent * $loan_value)/100),2,"."," ").' zł';
                            }
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 10px;"></td>
            </tr>
            <tr class="tr-grey">
                <td style="width: 100%;" colspan="4" class="strong">
                    Opcje dodatkowe: {{ $inputs['extra_options'] }}
                </td>
            </tr>
        </table>
    </div>
    <div class="strong" style="margin-top: 10px;">
        Otrzymałem przed zawarciem umowy ogólne warunki ubezpieczenia odpowiednie do przedmiotu i zakresu ubezpie- czenia oraz i informację o wymaganych minimalnych zabezpieczeniach i trybie postępowania w przypadku powstania szkody”. Potwierdzam, iż zapoznałem się z ich postanowieniami.
    </div>

    <div style="margin-top: 20px;">
        <table >
            <tr>
                <td>
                    {{ date('d.m.Y') }}
                </td>
                <td>
                    <img src="{{ 'templates-src/ergo_hestia.png' }}" style="height: 120px;"/>
                </td>
            </tr>
        </table>
    </div>
    <div style="font-size: 0.8em;">
        Data, podpis i pieczęć obsługującego ubezpieczenia
    </div>
</div>

</body>
</html>
