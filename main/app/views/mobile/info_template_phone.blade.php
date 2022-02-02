<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="templates-src/css/notification.css" rel="stylesheet">
    <title></title>
</head>
<body style="margin: 1cm 0;">

<h3>Dane pojazdu</h3>
<div style ="margin-top:20pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
    <table class="pad-medium bordered-all" cellspacing="0" cellpadding="0">
        <tr>
            <td style="font-weight: bold;">Nr rejestracyjny:</td>
            <td>{{ $injury->registration }}</td>
        </tr>
        <Tr>
            <td style="font-weight: bold;">Nr umowy:</td>
            <td>{{ $injury->nr_contract }}</td>
        </Tr>

    </table>
</div>
<h3>Dane klienta</h3>
<div style ="margin-top:20pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
    <table class="pad-medium bordered-all" cellspacing="0" cellpadding="0">
        <tr>
            <td style="font-weight: bold;">NIP :</td>
            <td>{{ $injury->nip }}</td>
        </tr>
    </table>
</div>
<h3>Dane zgłaszającego</h3>
<div style ="margin-top:20pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
    <table class="pad-medium bordered-all" cellspacing="0" cellpadding="0">
        <tr>
            <td style="font-weight: bold;">Nazwisko:</td>
            <td>{{ $injury->notifier_surname }} {{ $injury->notifier_name }}</td>
        </tr>
        <Tr>
            <td style="font-weight: bold;">Telefon:</td>
            <td>{{ $injury->notifier_phone }} </td>
        </Tr>
        <Tr>
            <td style="font-weight: bold;">Email:</td>
            <td>{{ $injury->notifier_email }} </td>
        </Tr>
    </table>
</div>
<h3>Dane zgłoszenia</h3>
<div style ="margin-top:20pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
    <table class="pad-medium bordered-all" cellspacing="0" cellpadding="0">
        <Tr>
            <td style="font-weight: bold;">Rodzaj zdarzenia:</td>
            <td>{{ ($injury->injuries_type()->first()) ? $injury->injuries_type()->first()->name : ''}}</td>
        </Tr>
        <Tr>
            <td style="font-weight: bold;">Data zdarzenia:</td>
            <td>{{ $injury->date_event }} </td>
        </Tr>
        <Tr>
            <td style="font-weight: bold;">Miejsce zdarzenia:</td>
            <td>{{ $injury->event_city }} </td>
        </Tr>


    </table>
</div>


</body>
</html>
