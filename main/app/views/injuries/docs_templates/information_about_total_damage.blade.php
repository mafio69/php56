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
    }
    td, h4 {
        line-height: 1.5
    }

    .line {
        margin:0px;
        padding:0px;
    }
</style>
<body>
<div class="body content body-margin-big">
        <p class=" text-right" style="margin-top: 0px;">{{ checkIfEmpty('13', $ideaA) }}, {{date('d-m-Y')}}</p>

        <table class="letter">
            <tr>
                <td style="width: 9cm; height: 3cm; text-align: left; vertical-align: middle;">
                    <p class="block text-justify">
                        Szanowni Państwo
                    </p>
                </td>
            </tr>
        </table>

        <h4 class="text-center"><span  style="border-bottom: black solid 1px;">DOTYCZY POJAZDU: {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}  nr rej.: {{ $injury->vehicle->registration }}<br />
            umowa nr.: {{ $injury->vehicle->nr_contract }}; typ szkody: {{ $injury->injuries_type()->first()->name }};  nr szkody: {{ $injury->injury_nr }}</span>
        </h4>

        @if( endswith($injury->vehicle->nr_contract, 'P/SK') || endswith($injury->vehicle->nr_contract, '/P')  )
            <p class="block text-justify">
                Działając jako Centrum Asysty Szkodowej {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} oraz w związku z zakwalifikowaniem przez ubezpieczyciela Państwa szkody jako całkowitej, uprzejmie informujemy, iż w przypadku wystąpienia kosztów parkowania, w zakresie niepokrytym przez ubezpieczyciela obciążają one Państwa jako pożyczkobiorcę.
            </p>
            <p class="block text-justify">
                Informujemy, że w przypadku szkody całkowitej umowa pożyczki powinna być nadal spłacana zgodnie z harmonogramem, natomiast wypłacone odszkodowanie pozostaje na koncie rozrachunkowym jako zabezpieczenie spłaty pożyczki, zgodnie z § 17 ust. 3 Ogólnych Warunków Umowy Pożyczki (OWUP). Jednak istnieje możliwość złożenia wniosku o wcześniejsze zakończenie umowy  pożyczki, zgodnie z § 10 ust. 7 OWUP.. Wniosek ten należy przesłać na adres: {{ (isset($ideaA[4])) ? $ideaA[4] : 'szkody@idealeasing.pl' }}
            </p>
        @else
        <p class="block text-justify text-indent">
            Centrum Asysty Szkodowej Sp. z o. o działając jako pełnomocnik firmy {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}  w związku z zakwalifikowaniem przez Ubezpieczyciela szkody jako całkowitej, uprzejmie informujemy, iż w przypadku wystąpienia kosztów parkowania, w zakresie niepokrytym przez Ubezpieczyciela obciążają one Państwa jako Użytkownika pojazdu.
        </p>
        <p class="block text-justify text-indent" style="margin-bottom: 0cm">
            Informujemy, że ostateczne rozliczenie umowy, nastąpi po wypłacie odszkodowania oraz po sprzedaży pozostałości po przedmiocie leasingu. Kwota otrzymana z odszkodowania oraz kwota za sprzedaż pojazdu po szkodzie wejdą Państwu do rozliczenia umowy leasingu. W sprawie rozliczenia umowy leasingu. prosimy o kontakt z Działem Sprzedaży Ubezpieczeń 801 199 199 (wew. 5)
        </p>
        @endif

        @if( endswith($injury->vehicle->nr_contract, 'P/SK') || endswith($injury->vehicle->nr_contract, '/P')  )
            <p class="small-block ">Poniżej podajemy wartości przyjęte przez ubezpieczyciela do likwidacji szkody:</p>
        @else
            <p class="line text-indent">Poniżej podajemy wartości przyjęte przez Ubezpieczyciela: </p>
        @endif


        @if($injury->wreck)
        <p class="block">
            <p class="line text-indent">Wartość pojazdu bezpośrednio przed zdarzeniem : {{ $injury->wreck->value_undamaged }} {{ ($injury->wreck->value_undamaged_net_gross == 0) ? 'netto/brutto' : (isset(Config::get('definition.net_gross')[$injury->wreck->value_undamaged_net_gross])? Config::get('definition.net_gross')[$injury->wreck->value_undamaged_net_gross]:"")}}{{ (isset(Config::get('definition.currencies')[$injury->wreck->value_undamaged_currency])?Config::get('definition.currencies')[$injury->wreck->value_undamaged_currency]:"") }}
            </p>
            <p class="line text-indent">Wartość pojazdu po zdarzeniu : {{ $injury->wreck->value_repurchase }} {{ ($injury->wreck->value_repurchase_net_gross == 0) ? 'netto/brutto' : (isset(Config::get('definition.net_gross')[$injury->wreck->value_repurchase_net_gross])?Config::get('definition.net_gross')[$injury->wreck->value_repurchase_net_gross]:"") }} {{ (isset(Config::get('definition.currencies')[$injury->wreck->value_repurchase_currency])?Config::get('definition.currencies')[$injury->wreck->value_repurchase_currency]:"") }}</p>
            <p class="line text-indent">Wysokość odszkodowania : {{ $injury->wreck->value_compensation }} {{ ($injury->wreck->value_compensation_net_gross == 0) ? 'netto/brutto' : (isset(Config::get('definition.net_gross')[$injury->wreck->value_compensation_net_gross])?Config::get('definition.net_gross')[$injury->wreck->value_compensation_net_gross]:"") }} {{ (isset(Config::get('definition.currencies')[$injury->wreck->value_compensation_currency])?Config::get('definition.currencies')[$injury->wreck->value_compensation_currency]:"") }}</p>
        </p>
        @endif

        @if($injury->wreck&&$injury->wreck->if_tenderer)
            @if( endswith($injury->vehicle->nr_contract, 'P/SK') || endswith($injury->vehicle->nr_contract, '/P')  )
                <p class="block  text-justify">
                    Ubezpieczyciel wskazał oferenta na zakup pozostałości za cenę odpowiadającą ww. wartości pojazdu po zdarzeniu. Oferta ważna do dnia: {{ $injury->wreck->expire_tenderer }}.
                    <br/>
                    Po tym terminie sprzedaż wraku oferentowi nie będzie możliwa.
                </p>
            @else
            <p class="block  text-justify text-indent">
                <p class="line text-indent"><span style="border-bottom: black solid 1px">Ubezpieczyciel wskazał oferenta na zakup pozostałości. Oferta ważna do {{ $injury->wreck->expire_tenderer }}.</span>
                </p>
                <p class="line text-indent">Po tym terminie sprzedaż wraku  oferentowi nie będzie możliwa.</p>
            </p>
            @endif
        @else
        <p class="block  text-justify text-indent">
            <p class="line text-indent"><span style="border-bottom: black solid 1px">Aktualnie oczekujemy na wskazanie przez Ubezpieczyciela danych oferenta.</span>
            </p>
            <p class="line text-indent">Informujemy również, że w przypadku posiadania polisy obcej, Ubezpieczyciel ma prawo do odmowy pomocy w zagospodarowaniu pozostałości  po przedmiocie leasingu.</p>
        </p>
        @endif

        @if( endswith($injury->vehicle->nr_contract, 'P/SK') || endswith($injury->vehicle->nr_contract, '/P')  )
            <p class="block  text-justify">
                W tym miejscu wskazujemy, że w przypadku posiadania polisy obcej, ubezpieczyciel ma prawo do odmowy pomocy w zagospodarowaniu pozostałości  po przedmiocie pożyczki.
                Prosimy o  Państwa deklarację czy pojazd  pozostanie w Państwa posiadaniu lub czy są Państwo zainteresowani sprzedażą  pozostałości oferentowi aukcyjnemu. Deklarację należy odesłać na adres: {{ (isset($ideaA[4])) ? $ideaA[4] : 'szkody@idealeasing.pl' }} do dnia {{ $inputs['sending_date'] }}.
            </p>
            <p class="block  text-justify">
                W sprawach związanych z likwidacją szkody, prosimy o kontakt pod numerem 801 199 199 w. 5
                W sprawie pytań odnośnie wcześniejszego zakończenia umowy pożyczki prosimy o kontakt z Działem Sprzedaży Ubezpieczeń Idea Leasing  801 199 199 (wew. 5).
            </p>
        @else
            {{-- <p class="block  text-justify text-indent">W tym miejscu wskazujemy, że w przypadku posiadania polisy obcej, Ubezpieczyciel ma prawo do odmowy pomocy w zagospodarowaniu pozostałości po przedmiocie leasingu, co skutkuje zorganizowaniem aukcji wewnętrznej przez Centrum Asysty Szkodowej. Pojazd sprzedawany jest oferentowi za najwyżej uzyskaną kwotę z aukcji, która wchodzi Państwu do rozliczenia umowy leasingu. </p> --}}

            @if($injury->wreck)
            <p class="text-justify text-indent">
                Wraz z pismem o szkodzie całkowitym otrzymaliście Państwo deklarację, którą prosimy dokładnie wypełnić i zaznaczyć czy chcą Państwo skorzystać  z prawa pierwokupu. Wykup pojazdu za kwotę {{$injury->wreck->value_repurchase}} {{isset(Config::get('definition.net_gross')[$injury->wreck->value_repurchase_net_gross])?(Config::get('definition.net_gross')[$injury->wreck->value_repurchase_net_gross]):""}} 
                {{ isset(Config::get('definition.currencies')[$injury->wreck->value_repurchase_currency])?(Config::get('definition.currencies')[$injury->wreck->value_repurchase_currency]):"" }}. Wypełnioną deklarację, prosimy o zwrotne odesłanie <span style="border-bottom: black solid 1px">w jak najszybszym terminie, nie później niż do dnia {{ $inputs['sending_date'] }}r. na adres:</span> Centrum Asysty Szkodowej S.A Ul.  Gwiaździsta 66 , 54-413 Wrocław- na moje nazwisko. Deklaracja,  może być  wysłana do nas w formie skanu ( adres email: <u><a>{{$injury->leader!=null?$injury->leader->email:'---'}}</a></u>, bez konieczności  odsyłania pocztą tradycyjną, co znacznie przyspieszy dalsze postępowanie  likwidacyjne.
            </p>
            @endif

            <p class="text-justify text-indent">
                W przypadku rezygnacji   z przysługującego Państwu  prawa pierwokupu,  uruchomimy sprzedaż oferentowi aukcyjnemu, a Państwa   prosimy o zastosowanie  się do   obowiązku  przetransportowania  pozostałości   pojazdu  na plac {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} - zgodnie z załączonym pismem.
            </p>
            <p class="text-justify text-indent">
                W sprawach związanych z likwidacją szkody, prosimy o kontakt pod numerem 801 199 199 w. 5
            </p>
        @endif
        <br /><br />

        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:15px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>
</div>

</body>
</html>
