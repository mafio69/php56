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


    @import url('https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700&display=swap');
    * {
        /*font-family: "Times New Roman", "Times", serif;*/
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-family: Lato, sans-serif;
        font-size: 11pt;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        font-size: 11pt;
        line-height: 1.1;
        margin-top: 5px;
    }
</style>


<body>
<div id="body">
<div class="right">Miejscowość …………………………… data ……………<br><br></div>
<p class="left" style="line-height: 1.5">Dane właściciela (Leasingodawcy):
    <br><br>
    {{($owner->data()->where('parameter_id', 1)->first() ) ? $owner->data()->where('parameter_id', 1)->first()->value : '---'}}
    <br>
    {{($owner->data()->where('parameter_id', 2)->first() ) ? $owner->data()->where('parameter_id', 2)->first()->value : '---'}}
    {{($owner->data()->where('parameter_id', 3)->first() ) ? $owner->data()->where('parameter_id', 3)->first()->value : '---'}}
    <br>
    {{($owner->data()->where('parameter_id', 13)->first() ) ? $owner->data()->where('parameter_id', 13)->first()->value : '---'}}
    <br><span style="font-size:11px;border-bottom: 1px solid black;"><a>szkody@ideagetin.pl</a></span><br><br><br></p>

<div class="center" style="margin-top: 0.5cm; margin-bottom: 0.5cm;"><b>OŚWIADCZENIE</b></div>
<div class="center" style="margin-bottom: 1cm;"><b>Dotyczy szkody {{$injury->injury_nr}}</b></div>

<p style="text-indent: 1.0cm"> Oświadczam, że naprawiłem pojazd marki
    {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}}
    nr rej {{$vehicle->registration}} po szkodzie  we własnym zakresie i na własny koszt.
    W przypadku gdyby z tytułu zapłaty za naprawę pojazdu po szkodzie zgłosił zasadne roszczenie inny podmiot trzeci to zobowiązuję się zapłacić tę należność.</p>



<i><span style="font-size: 9pt;border-bottom: 1px solid black;font-weight: 200;">Do oświadczenia prosimy
    dołączyć:</span><br>
    <p style="font-size: 9pt; line-height: 1.1;">
        - kosztorys TU,<br>
        - zdjęcia po naprawie z gazetą codzienną gdzie widoczna jest bieżąca data,<br>
        - badanie techniczne po szkodzie (w przypadku gdy uszkodzeniu uległy elementy układu nośnego, hamulcowego lub kierowniczego mające wpływ na bezpieczeństwo ruchu drogowego).<br><br>
        Warunkiem wydania upoważnienia jest brak zaległości na umowie leasingowej. <br>
        Proszę o odesłanie dokumentów na adres:
        <a style="font-size: 9pt; border-bottom: 1px solid black;">szkody@ideagetin.pl</a>.
    </p></i>
<span style="border-bottom: 1px solid black;"><b style="font-size: 9pt;">Opłata z tytułu wystawienia upoważnienia wynosi 399 zł
    netto zgodnie z Tabelą Opłat
    i Ogólnymi Warunkami
    Umowy.</b></span>
</span>

<div class="right" style="margin-top: 2cm; margin-right: 1.5cm;">Podpis leasingobiorcy</div>
</div>
</body>
</html>