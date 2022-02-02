<?php //wniosek o naprawę szkody całkowitej ?>
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
<body>
<div id="body" style="margin-left: 0.5cm; margin-right: 0.5cm">
    <div class="page" id="content">

        <div style="padding-left: 1cm; padding-right: 1cm">


            @include('injuries.docs_templates.modules.place')

            @include('injuries.docs_templates.modules.injury_info')

            <p>
                Szanowni Państwo,<br><br>

                Centrum Asysty Szkodowej Sp. z o.o. jako pełnomocnik firmy {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}
                w związku z zakwalifikowaniem przez Ubezpieczyciela Państwa szkody jako całkowitej, uprzejmie informuje,
                iż w przypadku wystąpienia kosztów parkowania, w zakresie niepokrytym przez Ubezpieczyciela obciążają
                one Państwa jako Użytkownika pojazdu.

                Poniżej podajemy wartości przyjęte przez Ubezpieczyciela:<br>

            </p>

            <table style=" width: 100%; ">
                <tr>
                    <td style="width: 60%;">Wartość pojazdu bezpośrednio przed zdarzeniem:</td>
                    <td>
                        {{$injury->wreck!=null?$injury->wreck->value_undamaged.' '
                .( Config::get('definition.compensationsNetGross.'.$injury->wreck->value_undamaged_net_gross)):'---'}}
            </td>
                </tr>
                <tr>
                    <td style="width: 60%;">Wartość pojazdu po zdarzeniu:</td>
                    <td >{{$injury->wreck!=null?$injury->wreck->value_repurchase.' '
                .( Config::get('definition.compensationsNetGross.'.$injury->wreck->value_repurchase_net_gross)):'---'}}</td>
                </tr>
                <tr>
                    <td style="width: 60%;">Wysokość odszkodowania:</td>
                    <td >{{$injury->wreck!=null?$injury->wreck->value_compensation.' '
                .( Config::get('definition.compensationsNetGross.'.$injury->wreck->value_compensation_net_gross)):'---'}}</td>
                </tr>
            </table>
            
            <p>
                Uprzejmie informuję, iż Ubezpieczyciel wskazał oferenta na zakup pozostałości. Oferta jest ważna do
                dnia {{$injury->wreck->expire_tenderer}}.
            </p>
            <p>
                W załączeniu przesyłam kosztorys z Towarzystwa Ubezpieczeń.<br><br>

                Jednocześnie informujemy, iż w przypadku szkody całkowitej z OC sprawcy, <u>można naprawić pojazd do
                    100% wartości</u>, jednakże należy ten fakt zgłosić do Ubezpieczyciela, jeszcze przed rozpoczęciem
                naprawy oraz ustalić koszty naprawy. Ten obowiązek spoczywa na Państwu- jako na korzystającym.
                Kosztorys naprawy musi zostać zaakceptowany przez Ubezpieczyciela i wtedy możliwe będzie rozpoczęcie
                naprawy pojazdu. Po skończonej naprawie prosimy o dosłanie do Ubezpieczyciela oraz do nas na adres:
                <u><a>szkody@ideagetin.pl</a></u> faktur potwierdzających ukończoną naprawę pojazdu. Faktury, powinny
                być zgodne z
                zaakceptowanym przez Ubezpieczyciela kosztorysem naprawy.<br><br>
                <u><b>Na odpowiedź oczekujemy w terminie 7 dni od dnia dzisiejszego.</b></u> Brak odpowiedzi we
                wskazanym
                terminie, będzie skutkowało rozliczeniem szkody na zasadach szkody całkowitej, a umowa zgodnie z
                Ogólnymi Warunkami Umowy Leasingu zostanie zablokowana a następnie rozliczona i zakończona.<br><br>

                Poproszę o wskazanie miejsca postoju pojazdu, jak również o informację, czy zamierzają Państwo
                naprawiać pojazd i kontynuować Umowę Leasingu, czy rozliczyć szkodę jako całkowitą
                -oświadczenie to, poproszę przesłać do mnie w formie pisma na adres: Centrum Asysty Szkodowej Sp. z o
                .o. Ul. Gwiaździsta 66 , 54-413 Wrocław- na moje nazwisko. Dokument ten może być wysłany do nas w
                formie skanu (adres email: <u><a>{{$injury->leader!=null?$injury->leader->email:'---'}}</a></u> ) bez
                konieczności
                odsyłania pocztą tradycyjną, co znacznie przyspieszy dalsze postępowanie likwidacyjne.<br><br>

                W przypadku jeżeli Właściciel skorzysta z oferty Ubezpieczyciela na sprzedaż pozostałości,
                informujemy, iż w Państwa gestii leży pokrycie wszelkich kosztów parkingowych do momentu odbioru
                pojazdu przez nowego nabywcę.<br><br>

                W sprawach związanych z likwidacją szkody prosimy o kontakt pod numerem 801 199 199 w. 5.

            </p>
        </div>

    </div>
</div>

</body>
</html>