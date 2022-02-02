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
        font-family: Lato, sans-serif;
        font-size: 11pt;
        text-align: justify;
        text-justify: inter-word;
    }
    p { 
        font-size: 11pt;
        line-height: 1.5;
    }
</style>

<body>
<div class="body content body-margin-big">
    <div class="t-body t-body-size-16">
        @include('injuries.docs_templates.modules.place')
        Dane właściciela (Leasingodawcy):
        <br><br>
        @include('injuries.docs_templates.modules.owner')
        <a href="mailto:szkody@ideagetin.pl">szkody@ideagetin.pl</a>
       
        <br><br>

        <p style="text-align: center">
            <b>OŚWIADCZENIE<br> Dotyczy szkody {{ $injury->injury_nr }}</b>
        </p>

        <p class="text-left">
            &emsp;&emsp;&emsp;&emsp;Oświadczam, że przypadku gdyby z tytułu zapłaty za naprawę pojazdu po szkodzie zgłosił zasadne roszczenie inny podmiot trzeci to zobowiązuję się zapłacić tę należność.
        </p>
        <br><br><br>
        <p style="text-align: right; margin-right: 1cm">Podpis Leasingobiorcy</p>
 
    </div>
</div>

</body>
</html>
