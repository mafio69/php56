<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ url('templates-src/css/notification.css') }}" rel="stylesheet">
    <link href="{{ url('templates-src/css/notification-'.$documentTemplate->slug.'.css') }}" rel="stylesheet">
    <title></title>
    <style>

        table {
            border-collapse: collapse
        }

        th, td {
            border-bottom: 1px solid rgb(84, 179, 224);
            /*line-height: 0.13in;*/
            /*height: 0.13in;*/
            padding-left: 5px;
            word-break: keep-all;
            white-space: nowrap;
        }

        span {
            font-family: "Arial Narrow";
            font-size: 8.1px;
            /*font-family: Helvetica Light, Arial, sans-serif;*/
            /*font-size: 8.1px;*/
            /*font-weight: lighter;*/
        }

        checkbox {
            padding: 2px;
        }

        .row {
            display: flex;
        }

        .column {
            flex: 50%;
        }

        div {
            font-family: Arial, sans-serif;
            font-weight: 300;
            font-size: 8.1px;
            color: rgb(66, 63, 61);
        }
    </style>
</head>
<body>
<div id="body">
    <div style="text-align: center;">
        <p style="font-family:Arial,serif;font-size:14.1px;color:rgb(0,148,212);">ZGŁOSZENIE SZKODY POJAZDU DLA
            KLIENTÓW<br> NIE KORZYSTAJĄCYCH Z ASYSTY SZKODOWEJ</p>
    </div>
    <div style="text-align: left;">
        <div class="page" title="Page 1">
            <div class="layoutArea"><p style="font-size: 6.0pt; color:rgb(42,42,41)">Jeżeli szkoda była zgłoszona do
                    Asysty
                    Szkodowej pod nr
                    22 22 77 222 lub na
                    portalu
                    Bank Kierowcy na stronie www.bankkierowcy.pl nie ma potrzeby wypełniania niniejszego
                    formularza</p>
                <div>
                    <table style="float: left; width: 100%;">
                        <tbody>
                        <tr style="width: 100%; background-color: rgb(0,148,212)">
                            <td class="block" colspan="6"><span
                                        style="color:rgb(254,255,255);font-size:8.0pt;">
                                                <b>PODSTAWOWE INFORMACJE O WNIOSKODAWCACH:</b></span></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Czy użytkownikiem pojazdu
                                        jest leasingobiorca</span></td>
                            <td>
                                            <span>
                                        <input type="checkbox"
                                        >TAK</span></td>
                            <td><span>
                                        <input type="checkbox">NIE</span></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253)"><span>Czy pojazd
                                                znajduje si w zakadzie naprawczym/ serwisie</span></td>
                            <td>
                                            <span>
                                        <input type="checkbox">TAK</span></td>
                            <td><span>
                                        <input type="checkbox">NIE</span></td>
                            <td style="background-color: rgb(243,248,253)"><span>lub czy został już
                                        naprawiony</span></td>
                            <td><span>
                                        <input type="checkbox">TAK</span></td>
                            <td><span>
                                        <input type="checkbox">NIE</span></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253)"><span>Czy zgłoszono szkodę bezpośrednio
                                    do Towarzystwa Ubezpieczeniowego
                                            </span></td>
                            <td>
                                            <span>
                                        <input type="checkbox">TAK</span></td>
                            <td><span>
                                        <input type="checkbox">NIE</span></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr style="background-color: rgb(0,148,212)">
                            <td colspan="6"><span style="color:rgb(254,255,255);font-weight:normal;font-size:8.0pt;">
                                                <b>OSOBA DO KONTAKTU:</b></span></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Imie i
                                                nazwisko:
                                            </span></td>
                            <td><b></b></td>
                            <td style="background-color: rgb(243,248,253);"><span>Telefon
                                            </span></td>
                            <td></td>
                            <td style="background-color: rgb(243,248,253);"><span>E-mail
                                            </span></td>
                            <td><b></b></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Marka:
                                            </span></td>
                            <td><b></b></td>
                            <td></td>
                            <td></td>
                            <td style="background-color: rgb(243,248,253);"><span>Numer rejestracyjny pojazdu:
                                </span></td>
                            <td><b></b></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Numer umowy leasingu
                                </span></td>
                            <td><b></b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Z jakiej polisy
                                                likwidowana jest szkoda
                                            </span></td>
                            <td><span>
                                        <input type="checkbox"> OC
                                    sprawcy</span></td>
                            <td><span>
                                        <input type="checkbox">AC użytkownika</span></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Zakład ubezpieczeń w którym zgłoszono szkodę (o ile została zgłoszona)
                                </span></td>
                            <td><b></b>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Rodzaj szkody
                                            </span></td>
                            <td><span>
                                        <input type="checkbox"
                                        >Kradzież</span></td>
                            <td><span>
                                        <input type="checkbox"
                                        >Kasacja</span></td>
                            <td><span>
                                        <input type="checkbox"
                                        >Cześciowa</span></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Numer i data szkody w TU
                                            </span></td>
                            <td><b></b></td>
                            <td></td>
                            <td></td>
                            <td style="background-color: rgb(243,248,253);"><span>Szacunkowa wartość szkody
                                            </span></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Krótki opis szkody
                                            </span></td>
                            <td colspan="5">
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Numer polisy ubezpieczeniowej
                                            </span></td>
                            <td><b></b></td>
                            <td></td>
                            <td></td>
                            <td style="background-color: rgb(243,248,253);"><span>Data i miejsce wystąpienia szkody
                                            </span></td>
                            <td><b></b></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Odbiorca odszkodowania
                                            </span></td>
                            <td><span>
                                        <input type="checkbox"> Leasingobiorca*
                                </span></td>
                            <td><span>
                                        <input type="checkbox">Warsztat</span></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr style="background-color: rgb(0,148,212)">
                            <td colspan="6"><span style="color:rgb(254,255,255);font-weight:normal;font-size:8.0pt">
                                                <b>DANE WARSZTATU:</b></span></td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Nazwa
                                            </span></td>
                            <td><b>
                                </b></td>
                            <td style="background-color: rgb(243,248,253);"><span>NIP warsztatu
                                            </span></td>
                            <td><b>
                                </b></td>
                            <td style="background-color: rgb(243,248,253);"><span>E-mail warsztatu
                                            </span></td>
                            <td>

                                <b></b>
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: rgb(243,248,253);"><span>Adres
                                            </span></td>
                            <td>
                                <b></b>
                            </td>
                            <td></td>
                            <td></td>
                            <td style="background-color: rgb(243,248,253);"><span>Kod
                                            </span></td>
                            <td><b></b></td>
                        </tr>
                        </tbody>
                    </table>

                    <div style="padding-top: 5px"><span style="font-size: 6.0pt; color:rgb(66,63,61);">Jeżeli
                                            szkoda
                                            była
                                            zgłoszona do Asysty
                                            * Wypłata na rzecz Leasingobiorcy możliwa tylko po przedstawieniu:</span>
                    </div>
                    <div style="padding-top: 5px; margin-left: 5px">
                        <div style="font-size:7.0px;color:rgb(66,63,61);line-height: 0.9;">• faktury za
                            naprawę oraz potwierdzenia płatności,<br>
                            • oświadczenia wydanego przez warsztat wykonujący naprawę pojazdu o uregulowaniu
                            w całości zapłaty przez leasingobiorcę oraz badania techniczne w przypadku
                            szkody
                            o wartości powyżej 2000 PLN, • oświadczenia leasingobiorcy o naprawie pojazdu ze
                            środków własnych oraz badania technicznego w przypadku szkody o wartości powyżej
                            2000 PLN<br>
                            Do w/w danych należy dołączyć sporządzony w Towarzystwie Ubezpieczeniowym
                            protokół zgłoszenia szkody oraz wstępną kalkulację-kosztorys szkody.<br>
                            Pisemna zgoda do likwidacji szkody i odbioru odszkodowania zostanie wydana pod
                            warunkiem braku zaległości w zapłacie należności z tytułu niniejszej umowy
                            leasingu. W przypadku nieuzupełnie- nia pól obowiązkowych zostanie wystawiona
                            dyspozycja przelewu odszkodowania na konto leasingodawcy.<br>
                            Za wystawienie upoważnienia do odbioru odszkodowania pobierana jest opłata
                            zgodnie z Tabelą Opłat: 0 zł dla samochodów osobowych i ciężarowych do 3,5 t
                            oraz
                            pojazdów powyżej 3,5 t w przypadku likwidacji szkód w ramach asysty szkodowej,
                            399 zł w przypadku likwidacji szkody komunikacyjnej poza asystą
                            szkodową.<br></div>
                    </div>

                    <div class="row" style="padding-bottom: 0.5cm; padding-top: 0.3cm">
                        <div class="column">
                            <span style="font-size:7.0px;color:rgb(66,63,61);"><b>Do zgłoszenia załączam:</b></span><br>
                            <span style="font-size:7.0px;color:rgb(66,63,61);"><input
                                        type="checkbox">kosztorys*</span><br>
                            <span style="font-size:7.0px;color:rgb(66,63,61);"><input type="checkbox">oświadczenie o naprawie*</span><br>
                            <span style="font-size:7.0px;color:rgb(66,63,61);"><input type="checkbox">faktury ponaprawcze**</span><br>
                            <span style="font-size:7.0px;color:rgb(66,63,61);"><input type="checkbox">dowód rejestracyjny***</span><br>
                            <span style="font-size:7.0px;color:rgb(66,63,61);"><input type="checkbox">potwierdzenie polisy OC** umorzenie śledztwa**</span><br>
                            <span style="font-size:7.0px;color:rgb(66,63,61);"><input type="checkbox">*w przypadku szkody częściowej</span><br>
                            <span style="font-size:7.0px;color:rgb(66,63,61);"><input type="checkbox">**w przypadku szkody całkowitej lub kradzieżowej</span><br>
                        </div>
                        <div class="column" style="margin-top: 10px; margin-right: 0.5cm;">
                            <table style="width:100%; height:100%; border: 1px solid black">
                                <th style="border-bottom-color: black"></th>
                            </table>
                            <span tyle="width: 100%;text-align: center; padding-top: 15px">Data zgłoszenia
                                        Podpis i
                                        pieczątka Leasingobiorcy</span>
                        </div>
                    </div>
                    <span style="font-size: 9px; line-height: 1.0">
                                    <b>Leasingodawca płaci VAT! Jeżeli chcesz bezgotówkowo i bezproblemowo zlikwidować
                                    szkodę. Zapoznaj się z informacją poniżej.</b></span><br>
                    <div style="font-size: 6.0pt; line-height: 1.1;color:rgb(66,63,61);"><b><br>Szanowni Państwo</b>,
                        <br>
                        Leasingodawca podejmuje bardzo szerokie działania mające na celu uatrakcyjnienie świadczonych
                        usług, jak i podnoszenie ich jakości.<br>
                        Proponujemy Państwu pełną i kompleksową likwidację w zakresie szkód komunikacyjnych.<br>
                        Wszelkie sprawy dotyczące likwidacji szkód komunikacyjnych wraz z przysługującą pomocą
                        assistance sfinalizują Państwo
                        dzwoniąc pod nr: 22 22 77 222*
                        <div style="font-size: 6.0pt;color:rgb(66,63,61);"><br>* Opłata jak za połączenie
                            lokalne wg
                            taryfy operatora.<br></div>
                        <b>W ramach asysty szkodowej gwarantujemy:</b><br>
                        • 100 % bez VAT - leasingobiorca płaci pełny VAT za naprawę,<br>
                        • 100 % mobilności zapewniamy holowanie oraz bezpłatny pojazd zastępczy na czas naprawy (tylko
                        dla pojazdów osobowych i ciężarowych do 3,5 t)<br>
                        • 100 % jakości Auto trafi do sprawdzonego i wyselekcjonowanego zakładu naprawczego,
                        spełniającego kryteria jakości. Zapewniamy oryginalne części i gwarancje producenta,<br>• 100 %
                        rozliczenie bezgotówkowego, rozliczenia odbywa się bez pośrednictwa pomiędzy ubezpieczycielem a
                        serwisem.<br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
