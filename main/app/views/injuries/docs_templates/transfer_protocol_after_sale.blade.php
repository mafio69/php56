<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ url('templates-src/css/notification.css') }}" rel="stylesheet">
    <link href="{{ url('templates-src/css/notification-'.$documentTemplate->slug.'.css') }}" rel="stylesheet">
    <title></title>
</head>
<body>
<div class="body content body-margin-big">
    <div class="t-body t-body-size-14">
        <h3 class="text-center">Protokół Przekazania <br /> <span class="small">Übergabeprotokoll<br />
            Certificate of delivery</span></h3>

        <table class="border-outside2 font-size-12 non-padding">
            <tr>
                <td width="100">
                    Przekazujący:<br />
                    <span class="small">Übergebender:<br />
                    Transferor:</span>
                </td>
                <td>
                    {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }},
                    {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}
                </td>
            </tr>
            <tr>
                <td>
                    Przyjmujący:<br />
                    <span class="small">Übernehmender:<br />
                    Recipient:</span>
                </td>
                <td>
                    @if($injury->wreck&&$injury->wreck->buyerInfo)
                        {{ $injury->wreck->buyerInfo->name }}<br/>
                        NIP: {{ $injury->wreck->buyerInfo->nip  }}<br/>
                        REGON: {{ $injury->wreck->buyerInfo->regon  }}
                    @endif
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    @if($injury->wreck&&$injury->wreck->buyerInfo)
                        {{ $injury->wreck->buyerInfo->address_code }} {{ $injury->wreck->buyerInfo->address_city  }}<br/>
                        {{ $injury->wreck->buyerInfo->address_street  }}
                    @endif
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    @if($injury->wreck&&$injury->wreck->buyerInfo)
                        Telefon: {{ $injury->wreck->buyerInfo->phone }}<br/>
                        Email: {{ $injury->wreck->buyerInfo->email  }}<br/>
                        Osoba kontaktowa: {{ $injury->wreck->buyerInfo->contact_person  }}
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2">Przekazujący przekazuje, a przyjmujący przyjmuje: </td>
            </tr>
            <tr>
                <td>Nazwa przedmiotu </td>
                <td>{{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}</td>
            </tr>
            <tr>
                <td>Numer seryjny: </td>
                <td>{{ ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin }}</td>
            </tr>
            <tr>
                <td>Nr rej.:</td>
                <td>{{ $injury->vehicle->registration }}</td>
            </tr>
            <tr>
                <td>Rok produkcji:</td>
                <td>{{ $injury->vehicle->year_production }}</td>
            </tr>
        </table><br />

        <p class="font-size-14">Wraz z przedmiotem przekazano:<br />
            - dowód rejestracyjny&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TAK/NIE*<br />
            - polisę ubezpieczeniową&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TAK/NIE*<br />
            - kartę pojazdu&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TAK/NIE*<br />
            - instrukcję obsługi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TAK/NIE*<br />
            - książkę serwisową&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TAK/NIE*<br />
            - inne.................................................................................................</p>

        <p class="small-font"><b>Uwaga:</b> Przyjmujący oświadcza, że odebrał przedmiot, zapoznał się z jego stanem technicznym i potwierdza, że jego zużycie odpowiada normalnej eksploatacji, w związku z czym nie wnosi żadnych uwag, a strony umowy wyłączają odpowiedzialność z tytułu rękojmi za wady fizyczne rzeczy sprzedanej.</p>

        <p class="small-font"><b>Bemerkung:</b> Der Übernehmende bestätigt, dass er das Fahrzeug übernommen, sich mit seinem technischen Zustand bekannt gemacht hat und diesbezüglich keine Ansprüche geltend machen wird. Es gilt keine Gewährleistung für Sachmängel des verkauftern Objektes.</p>

        <p class="small-font"><b>Comment:</b> Recipient states, that they received the item and familiarised themselves with its technical condition. They confirm that wear and tear of the received product results from normal operation and therefore do not make any further comment. Seller will not be held reponsible for any physical defects found in the product. </p>

        <table class="table-size-12">
            <tr>
                <td>Przekazujący</td>
                <td class="text-right">Przyjmujący</td>
            </tr>
        </table>
        <br/>
        <br/>
        <br/>

        <p class="text-center">..................................................<br />Miejsce i data przekazania </p>

        <p class="text-right">*Niepotrzebna skreślić </p>
    </div>
</div>

</body>
</html>
