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
</style>
<body>
<div class="body content body-margin-big">
        <p class=" text-right" style="margin-top: 0px;">{{ checkIfEmpty('13', $ideaA) }}, {{date('d-m-Y')}}</p><br />

        <h4 class="text-center">DOTYCZY POJAZDU: {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}} nr rej.: {{ $injury->vehicle->registration }}<br />
            umowa nr.: {{ $injury->vehicle->nr_contract }}; typ szkody: {{ $injury->injuries_type()->first()->name }};  nr szkody: {{ $injury->injury_nr }}
        </h4>

        <p class="block text-justify">Szanowni Państwo,<br /><br />

            działając jako

                Centrum Asysty Szkodowej


            firmy {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} oraz w związku z zakwalifikowaniem przez Ubezpieczyciela Państwa szkody jako całkowitej, uprzejmie informujemy, iż w przypadku wystąpienia kosztów parkowania, w zakresie niepokrytym przez Ubezpieczyciela obciążają one Państwa jako Użytkownika pojazdu.</p>

        <p class="block">Poniżej podajemy wartości przyjęte przez Ubezpieczyciela:  </p>

        <p class="block">
            Wartość pojazdu bezpośrednio przed zdarzeniem :

            @if($injury->step == 10 || $injury->step == 0)
                {{ $inputs['value_undamaged'] }} {{ Config::get('definition.net_gross')[$inputs['value_undamaged_net_gross']] }}
            @else
              @if($injury->wreck)
                {{ $injury->wreck->value_undamaged }} {{ ($injury->wreck->value_undamaged_net_gross == 0) ? 'netto/brutto' : Config::get('definition.net_gross')[$injury->wreck->value_undamaged_net_gross] }} {{ Config::get('definition.currencies')[$injury->wreck->value_undamaged_currency] }}
              @endif
            @endif

            <br />
            Wartość pojazdu po zdarzeniu :

            @if($injury->step == 10 || $injury->step == 0)
                {{ $inputs['value_repurchase'] }} {{ Config::get('definition.net_gross')[$inputs['value_repurchase_net_gross']] }}
            @else
              @if($injury->wreck)
                {{ $injury->wreck->value_repurchase }} {{ ($injury->wreck->value_repurchase_net_gross == 0) ? 'netto/brutto' : Config::get('definition.net_gross')[$injury->wreck->value_repurchase_net_gross] }} {{ Config::get('definition.currencies')[$injury->wreck->value_repurchase_currency] }}
              @endif
            @endif

            <br />

            Wysokość odszkodowania :
            @if($injury->step == 10 || $injury->step == 0)
                {{ $inputs['value_compensation'] }} {{ Config::get('definition.net_gross')[$inputs['value_compensation_net_gross']] }}
            @else
              @if($injury->wreck)
                {{ $injury->wreck->value_compensation }} {{ ($injury->wreck->value_compensation_net_gross == 0) ? 'netto/brutto' : Config::get('definition.net_gross')[$injury->wreck->value_compensation_net_gross] }} {{ Config::get('definition.currencies')[$injury->wreck->value_compensation_currency] }}
              @endif
            @endif
            <br />
        </p>

        <p class="block  text-justify">Uprzejmie informuję, iż Ubezpieczyciel wskazał oferenta na zakup pozostałości. Oferta jest ważna do dnia
            @if($injury->step == 10 || $injury->step == 0)
                {{ $inputs['expire_tenderer'] }}
            @else
              @if($injury->wreck)
                {{ $injury->wreck->expire_tenderer }}
              @endif
            @endif
            .
        </p>

        <p class="block">W załączeniu przesyłam kosztorys z Towarzystwa Ubezpieczeń. </p>

        <p class="block  text-justify">Jednocześnie informujemy, iż w przypadku szkody całkowitej z OC sprawcy, można naprawić pojazd do 100% wartości, jednakże należy ten fakt zgłosić do Ubezpieczyciela, jeszcze przed rozpoczęciem naprawy oraz ustalić koszty naprawy. Ten obowiązek spoczywa na Państwu - jako na korzystającym. Kosztorys naprawy musi zostać zaakceptowany przez Ubezpieczyciela i wtedy możliwe będzie rozpoczęcie naprawy pojazdu. Po skończonej naprawie prosimy o dosłanie do Ubezpieczyciela oraz do nas na adres: <a href="mailto:szkody@idealeasing.pl">szkody@idealeasing.pl</a> faktur potwierdzających ukończoną naprawę pojazdu. Faktury, powinny być zgodne z zaakceptowanym przez Ubezpieczyciela kosztorysem naprawy. </p>

        <p class="block  text-justify"><b>Na odpowiedź oczekujemy w terminie 7 dni od dnia dzisiejszego.</b> Brak odpowiedzi we wskazanym terminie, będzie skutkowało rozliczeniem szkody na zasadach szkody całkowitej, a umowa zgodnie z Ogólnymi Warunkami Umowy Leasingu zostanie zablokowana, a następnie rozliczona i zakończona.
        </p>

        <p class="block  text-justify">W sprawie rozliczenia umowy leasingu, proszę o kontakt z Działem Sprzedaży Ubezpieczeń Idea Leasing 801 199 199 (wew. 5). </p>


        <p class="block  text-justify">Poproszę o wskazanie miejsca postoju pojazdu, jak również o informację, czy zamierzają Państwo naprawiać pojazd i kontynuować Umowę Leasingu, czy rozliczyć szkodę jako całkowitą - oświadczenie to, poproszę przesłać do mnie w formie pisma na adres:
                CENTRUM ASYSTY SZKODOWEJ Sp. Z o.o.

            Ul. Gwiaździsta 66 , 54-413 Wrocław - na moje nazwisko. Dokument ten może być wysłany do nas w formie scanu (adres email: @if( !filter_var(Auth::user()->email, FILTER_VALIDATE_EMAIL) === false) <a href="mailto:{{ Auth::user()->email }}">{{ Auth::user()->email }}</a> @else ........................... @endif )

            , bez konieczności odsyłania pocztą tradycyjną, co znacznie przyspieszy dalsze postępowanie likwidacyjne </p>
        <p class="block  text-justify">W przypadku jeżeli Właściciel skorzysta z oferty Ubezpieczyciela na sprzedaż pozostałości, informujemy, iż w Państwa gestii leży pokrycie wszelkich kosztów parkingowych do momentu odbioru pojazdu przez nowego nabywcę</p>

        <p class="block  text-justify">W sprawach związanych z likwidacją szkody prosimy o kontakt pod numerem 801 199 199 w. 5. </p>
    </div>
</div>

</body>
</html>
