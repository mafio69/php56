<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {{-- <link href="templates-src/css/notification-common.css" rel="stylesheet"> --}}
    <link href="templates-src/css/backup/notification-common.css" rel="stylesheet">
    <title></title>
</head>
<body>

<div class="body content body-margin-big">
  <?php $insurance = $agreement->insurances->last();?>
    <div style="background:rgb(210,210,210); display:block; border-top: 3px #b31111 solid; padding:10px 8px;">
      <p style="margin:2px; line-height:20px font-size:18px; font-weight:bold;">Aneks nr {{(isset($inputs['annex_id'])) ? $inputs['annex_id'] : ''}} do certyfikatu nr {{$insurance->insurance_number}}</p>
      <p style="margin:2px; line-height:20px font-size:18px; font-weight:bold;">Numer aneksu: {{(isset($inputs['annex_number'])) ? $inputs['annex_number'] : ''}}</p>
      <p style="margin:2px; line-height:20px font-size:18px;">Data obowiązywania zmian:
        <br>
        <b>
          od {{(isset($inputs['date_form'])) ? $inputs['date_form'].' 00:00' : '---'}} do {{(isset($inputs['date_to'])) ? $inputs['date_to'].' 23:59' : '---'}}
        </b>
      </p>
    </div>
    <div style="display:block; border-top: 3px #b31111 solid; padding:5px 3px; margin-top:10px;">
      <div style="width:40%; display:inline-block; color: #b31111;  font-size:14px; font-weight:bold;">Dane Ubezpieczającego:</div>
      <div style="width:55%; display:inline-block;">
        <p style="margin:2px; line-height:18px font-size:17px; font-weight:bold;">{{ checkIfEmpty('1', $ideaA) }}</p>
        <p style="margin:2px; line-height:18px font-size:17px;">NIP: {{ checkIfEmpty('8', $ideaA) }}</p>
        <p style="margin:2px; line-height:18px font-size:17px;">{{ checkIfEmpty('2', $ideaA) }}, {{ checkIfEmpty('3', $ideaA) }} {{ checkIfEmpty('13', $ideaA) }}</p>
      </div>
    </div>
    <div class="">
      <div style="background:rgb(210,210,210); display:block; padding:4px 3px; font-size:15px; line-height: 17px; font-weight:bold; margin-top:10px;">
        Dotyczy:
      </div>
      <div style="font-size:13px; line-height: 15px;">
        {{(isset($inputs['refer'])&&isset($annex_refers[$inputs['refer']])) ? $annex_refers[$inputs['refer']]  : ''}}
      </div>
    </div>
    <div class="">
      <div style="background:rgb(210,210,210); display:block; padding:4px 3px; font-size:15px; line-height: 17px; font-weight:bold; margin-top:10px;">
        Treść aneksu:
      </div>
      <div style="font-size:16px; line-height: 18px;">
        <p style="margin:0px">Symbol produktu: {{($agreement->insurance_group_row()->first()) ? $agreement->insurance_group_row()->first()->symbol_product.'-'.$agreement->insurance_group_row()->first()->symbol_element : ''}}</p>
        <p>
          @if($inputs['refer']=='12')
            rozwiązanie umowy {{($agreement->leasing_agreement_type_id==1) ? 'pożyczki' : 'leasingu'}} nr {{$agreement->nr_contract}}
            <br>
            Polisa zostaje rozwiązana z dniem {{(isset($inputs['end_date'])) ? $inputs['end_date'] : ''}}
          @endif
          {{(isset($inputs['annex_content'])) ? nl2br($inputs['annex_content']) : ''}}
        </p>
        <p style="margin:0px; margin-top:8px; border-top:1px solid #000; padding-top:2px;">pozostałe warunki umowy ubezpieczenia pozostają bez zmian</p>
      </div>
    </div>
    <div class="">
      <div style="background:rgb(210,210,210); display:block; padding:4px 3px; font-size:15px; line-height: 17px; font-weight:bold; margin-top:10px;">
        Składka i sposób płatności aneksu
      </div>
      <div style="font-size:12px; line-height: 13px;">
        <table style="width:100%; font-size:12px; line-height: 13px;">
          <tr>
            <td style="width:40%">Składka przed zmianą:</td>
            <td style="font-weight:bold">{{ number_format($insurance->contribution,2,",",".") }} PLN (słownie: {{ Idea\AmountTranslator\AmountTranslator::getInstance()->slownie($insurance->contribution, false, false, false) }})</td>
          </tr>
          <tr>
            <td style="width:40%">Składka po zmianie:</td>
            <td style="font-weight:bold">
              @if(isset($inputs['annex_value'])&&$inputs['type']==2)
                {{ number_format($insurance->contribution+$inputs['annex_value'],2,",",".") }} PLN (słownie: {{ Idea\AmountTranslator\AmountTranslator::getInstance()->slownie($insurance->contribution+$inputs['annex_value'], false, false, false) }})
              @else
                {{ number_format($insurance->contribution-$inputs['annex_value'],2,",",".") }} PLN (słownie: {{ Idea\AmountTranslator\AmountTranslator::getInstance()->slownie($insurance->contribution-$inputs['annex_value'], false, false, false) }})
              @endif
            </td>
          </tr>
          <tr>
            <td style="width:40%">
                @if(isset($inputs['type'])&&$inputs['type']==2)
                  Kwota zwiększenia składki:
                @else
                  Kwota zmniejszenia składki:
                @endif
            </td>
            <td style="font-weight:bold">
              @if(isset($inputs['annex_value']))
                {{ number_format($inputs['annex_value'],2,",",".") }} PLN (słownie: {{ Idea\AmountTranslator\AmountTranslator::getInstance()->slownie($inputs['annex_value'], false, false, false) }})
              @endif
            </td>
          </tr>
          <tr>
            <td style="width:40%">Zasady płatności:</td>
            <td style="font-weight:bold">
              @if(isset($inputs['type'])&&$inputs['type']==2)
                składka płatna jedorazowo przelewem na konto Ubezpieczyciela w terminie do {{(isset($inputs['return_date'])) ? $inputs['return_date'] : ''}}
              @else
                zwrot składki przelewem na konto Ubezpieczającego w terminie do {{(isset($inputs['return_date'])) ? $inputs['return_date'] : ''}}
              @endif
            </td>
          </tr>
          <tr>
            <td style="width:40%">Składka płatna na rachunek bankowy:</td>
            <td style="font-weight:bold">
              @if(isset($inputs['type'])&&$inputs['type']==2)
                33 1240 5400 1111 0000 4916 5924
              @else
                62 1560 0013 2077 0562 5000 0007
              @endif
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div style="display:block; border-top: 3px #b31111 solid; padding:5px 3px; margin-top:15px;">
      <div style="font-size: 12px;">
        Oświadczenie Ubezpieczającego:
      </div>
      <div style="font-size: 11px; font-style: italic;">
        potwierdzam, że przed zawarciem umowy otrzymałem tekst  Ogólnych Warunków Ubezpieczenia wraz z klauzulami dodatkowymi, na podstawie których umowę zawarto oraz zapoznałem się z nimi i zaakceptowałem ich treść.
      </div>
    </div>
    <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:25px; ">
          @include('insurances.docs_templates.modules.regards')
    </table>
</div>

</body>
</html>
