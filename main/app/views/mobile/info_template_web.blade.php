<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="templates-src/css/notification-vb.css" rel="stylesheet">
    <title></title>
</head>
<body style="margin: 1cm 0;">

        <h3>Dane zgłoszenia</h3>
        <div style ="margin-top:20pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
            <table class="pad-medium bordered-all" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="font-weight: bold;">Typ szkody:</td>
                    <td>
                        @if($injury->injuries_type == 2)
                            komunikacyjna OC
                        @elseif($injury->injuries_type == 1)
                            komunikacyjna AC
                        @elseif($injury->injuries_type == 3)
                            komunikacyjna kradzież
                        @elseif($injury->injuries_type == 4)
                            majątkowa
                        @elseif($injury->injuries_type == 5)
                            majątkowa kradzież
                        @elseif($injury->injuries_type == 6)
                            komunikacyjna AC - Regres
                        @endif
                    </td>
                </tr>
                <Tr>
                    <td style="font-weight: bold;">Nr umowy leasingu:</td>
                    <td>{{ $injury->nr_contract }}</td>
                </Tr>
                @if($injury->injuries_type == 4 || $injury->injuries_type == 5)
                <Tr>
                    <td style="font-weight: bold;">Rodzaj przedmiotu leasingu:</td>
                    <td>{{ $injury->rdl }} </td>
                </Tr>
                <Tr>
                    <td style="font-weight: bold;">Nr identyfikacyjny przedmiotu leasingu:</td>
                    <td>{{ $injury->ipl }} </td>
                </Tr>
                @endif
                <Tr>
                    <td style="font-weight: bold;">Towarzystwo Ubezpieczeniowe:</td>
                    <td>{{ $injury->name_zu }} </td>
                </Tr>
                <Tr>
                    <td style="font-weight: bold;">Data zdarzenia:</td>
                    <td>{{ $injury->date_event }} </td>
                </Tr>
                <Tr>
                    <td style="font-weight: bold;">Miejsce zdarzenia:</td>
                    <td>{{ $injury->event_city }} </td>
                </Tr>
                <Tr>
                    <td style="font-weight: bold;">Nr szkody:</td>
                    <td>{{ $injury->nr_injurie }} </td>
                </Tr>
                <Tr>
                    <td style="font-weight: bold;">Opis zdarzenia:</td>
                    <td>{{ preg_replace("/[^[:alnum:][:space:]]/u", "",$injury->desc_event) }} </td>
                </Tr>
                <Tr>
                    <td style="font-weight: bold;">Lokalizacja przedmiotu leasingu:</td>
                    <td>{{ $injury->location_upl }} </td>
                </Tr>

                @if($injury->injuries_type == 3 || $injury->injuries_type == 5)
                    <Tr>
                        <td style="font-weight: bold;">Jednostka policji:</td>
                        <td>{{ $injury->police_unite }} </td>
                    </Tr>
                    <Tr>
                        <td style="font-weight: bold;">Nr sprawy policji:</td>
                        <td>{{ $injury->nr_case }} </td>
                    </Tr>
                    <Tr>
                        <td style="font-weight: bold;">Nr telefonu policji:</td>
                        <td>{{ $injury->policeman_phone }} </td>
                    </Tr>
				@endif
                <Tr>
                    <td style="font-weight: bold;">Warsztat naprawczy:</td>
                    <td>{{ $injury->company }} </td>
                </Tr>
            </table>
        </div>

     	@if($injury->injuries_type == 0 || $injury->injuries_type == 1 || $injury->injuries_type == 2 || $injury->injuries_type == 3 || $injury->injuries_type == 6)
	    <h3>Dane pojazdu</h3>
        <div style ="margin-top:20pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
            <table class="pad-medium bordered-all" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="font-weight: bold;">Nr rejestracyjny:</td>
                    <td>{{ $injury->registration }}</td>
                </tr>
                <Tr>
                    <td style="font-weight: bold;">Marka:</td>
                    <td>{{ $injury->marka }}</td>
                </Tr>
                <Tr>
                    <td style="font-weight: bold;">Model:</td>
                    <td>{{ $injury->model }}</td>
                </Tr>
            </table>
        </div>
        @endif
        <h3>Dane klienta</h3>
        <div style ="margin-top:20pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
            <table class="pad-medium bordered-all" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="font-weight: bold;">Nazwa :</td>
                    <td>{{ $injury->name_client }}</td>
                </tr>
                <Tr>
                    <td style="font-weight: bold;">Adres :</td>
                    <td>{{ $injury->code_client }} {{ $injury->city_client }}, {{ $injury->adres_client }} </td>
                </Tr>
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



</body>
</html>
