<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ url('templates-src/css/notification.css') }}" rel="stylesheet">
    <link href="{{ url('templates-src/css/notification-'.$documentTemplate->slug.'.css') }}" rel="stylesheet">
    <title></title>
</head>

<style>
    body, * {
        font-family: "Times New Roman", "Times", serif;
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-size: 11pt;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        font-size: 12pt;
        line-height: 1.5;
        margin-top: 0.5cm
    }

    b {
        font-size: 12pt
    }
    td {
        line-height: 1.5
    }
</style>

<body style="{{--font-family:'Times';--}}margin: 0.5cm">
    
    <div class="right" style="margin-bottom: 1.5cm; font-size: 12pt;">Wrocław, {{date("d.m.Y")}}<br><br></div>
    <table class="letter">
        <tr>
            <td style="width: 9cm; height: 3cm; text-align: left; vertical-align: middle;">
                <p class="block text-justify">
                    Szanowni Państwo
                </p>
            </td>
        </tr>
    </table>

    <div class="center"><b style="border-bottom: 1px solid black;">Dotyczy pojazdu
        {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}}
        {{$vehicle->brand}} nr rej: {{$vehicle->registration}}<br>umowa nr
        {{$vehicle->nr_contract}} typ szkody {{$injury->injuries_type()->first()->name}}
        szkoda nr
        {{$injury->injury_nr}}</b><br><br></div>
        <p class="block text-justify">
            &emsp;&emsp;&emsp;Centrum Asysty Szkodowej Sp. z o. o działając jako pełnomocnik firmy {{($owner->data()->where('parameter_id', 1)->first() ) ? $owner->data()->where('parameter_id', 1)
            ->first()->value : '---'}} w związku z zakwalifikowaniem
            przez
            Ubezpieczyciela Państwa szkody jako całkowitej, uprzejmie informujemy, iż w przypadku wystąpienia kosztów
            parkowania, w zakresie niepokrytym przez Ubezpieczyciela obciążają one Państwa jako Użytkownika pojazdu.<br><br>
            
            &emsp;&emsp;&emsp;Informujemy, że ostateczne rozliczenie umowy, nastąpi po wypłacie odszkodowania oraz po sprzedaży pozostałości
            po przedmiocie leasingu. Kwota otrzymana z odszkodowania oraz kwota za sprzedaż pojazdu po szkodzie wejdą
            Państwu do rozliczenia umowy leasingu.<br>
            <div style="margin-left: 1.15cm; ">
                Poniżej podajemy wartości przyjęte przez Ubezpieczyciela:
                <p class="block text-justify" style="margin-top: 1.0cm;">
                    <table style=" width: 100%; ">
                        <tr>
                            <td style="width: 60%;">Wartość pojazdu bezpośrednio przed zdarzeniem:</td>
                            <td>
                                {{$injury->wreck!=null?$injury->wreck->value_undamaged.' '.
                                Config::get('definition.currencies.'.$injury->wreck->value_undamaged_currency).' '.                    
                                Config::get('definition.compensationsNetGross.'.$injury->wreck->value_undamaged_net_gross):'---'}}.
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 60%;">Wartość pojazdu po zdarzeniu:</td>
                            <td >
                                {{$injury->wreck!=null?$injury->wreck->value_repurchase.' '.
                                Config::get('definition.currencies.'.$injury->wreck->value_repurchase_currency).' '.
                                Config::get('definition.compensationsNetGross.'.$injury->wreck->value_repurchase_net_gross):'---'}}.</td>
                            </tr>
                            <tr>
                                <td style="width: 60%;">Wysokość odszkodowania:</td>
                                <td>{{$injury->wreck!=null?$injury->wreck->value_compensation.' '.
                                    Config::get('definition.currencies.'.$injury->wreck->value_compensation_currency).' '.
                                    Config::get('definition.compensationsNetGross.'.$injury->wreck->value_compensation_net_gross):'---'}}.</td>
                                </tr>
                            </table><br>

                        </div>
                        <div>
                            @if(!is_null($injury->wreck))
                                <p class="block text-justify">
                                @if(!is_null($injury->wreck->if_tenderer))
                                &emsp;&emsp;&emsp;<span style="border-bottom: 1px solid black; font-size: 11pt">Ubezpieczyciel wskazał oferenta na zakup pozostałości. Oferta ważna do dnia:
                                {{$injury->wreck->expire_tenderer}}.
                                <br></span>
                                &emsp;&emsp;&emsp;<span style="font-size: 11pt">Po tym terminie sprzedaż wraku oferentowi nie będzie możliwa.</span><br>
                                @else
                                &emsp;&emsp;&emsp;<span style="border-bottom: 1px solid black; font-size: 11pt">Aktualnie oczekujemy na wskazanie przez Ubezpieczyciela danych oferenta.</span><br>
                                &emsp;&emsp;&emsp;<span style="font-size: 11pt">Informujemy również, że w przypadku posiadania polisy obcej, Ubezpieczyciel ma prawo do odmowy pomocy w
                                zagospodarowaniu pozostałości po przedmiocie leasingu.<span><br>
                                @endif
                                </p>
                            @endif
                        </div>
                            <p class="block text-justify">
                                &emsp;&emsp;&emsp;Wraz z pismem o szkodzie całkowitym otrzymaliście Państwo deklarację, którą prosimy dokładnie wypełnić i
                                zaznaczyć czy chcą Państwo skorzystać z prawa pierwokupu. Wykup pojazdu za kwotę
                                {{$injury->wreck!=null?$injury->wreck->value_repurchase.' '.
                                Config::get('definition.currencies.'.$injury->wreck->value_repurchase_currency).' '.
                                Config::get('definition.compensationsNetGross.'.$injury->wreck->value_repurchase_net_gross):'---'}}.
                                    Wypełnioną deklarację, prosimy o
                                        zwrotne odesłanie <span style="font-size: 11pt; border-bottom: 1px solid black;">w jak najszybszym terminie, nie później niż do dnia {{$inputs['delivery_deadline']}} r. na
                                        adres:</span> Centrum
                                        Asysty
                                        Szkodowej S.A Ul. Gwiaździsta 66 , 54-413 Wrocław- na moje nazwisko. Deklaracja, może być wysłana do nas w
                                        formie skanu ( adres email: {{!is_null($injury->leader)?$injury->leader->email:'---'}} ) ,bez konieczności
                                        odsyłania
                                        pocztą tradycyjną, co
                                        znacznie
                                        przyspieszy dalsze postępowanie likwidacyjne.<br>
                            </p>

                            <p class="block text-justify">
                                        &emsp;&emsp;&emsp;W przypadku rezygnacji   z przysługującego Państwu  prawa pierwokupu,  uruchomimy sprzedaż oferentowi aukcyjnemu, a Państwa   prosimy o zastosowanie  się do   obowiązku  przetransportowania  pozostałości   pojazdu  na plac {{ (isset($ideaA[1])) ? $ideaA[1] : '---'}} - zgodnie z załączonym pismem.<br><br>

                            </p>
                            <p class="block text-justify">
                                        &emsp;&emsp;&emsp;W sprawach związanych z likwidacją szkody, prosimy o kontakt pod numerem 801 199 199 w. 5.<br><br><br>
                                    </p>
                                    <table style="width: 100%; font-size: 9pt; font-weight:normal;  ">
                                        <tbody>
                                            <tr>
                                                <td style="width:50%; "></td>
                                                <td style="text-align:center;">Z poważaniem</td>
                                            </tr>
                                            <tr>
                                                <td style="width:50%; "></td>
                                                <td style="text-align:center;">
                                                    @include('modules.signatures')
                                                </td>
                                            </tr>
                                        </tbody>
                                        
                                    </table>
                                </div>
                            </body>
                            </html>