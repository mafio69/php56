<div style="padding:20px;">
  <p style="font-size: 14px;"> Działając w imieniu i na rzecz {{$owner->name}} przesyłamy zgłoszenie szkody.
  <p style="font-size:16px; font-weight:bold; text-align:center;">
    PROSIMY O ZAREJESTROWANIE SZKODY.  <br>
    NUMER SZKODY PROSIMY  PRZESŁAĆ ZWROTNIE NA ADRES :
      zgloszeniaszkody@cas-auto.pl

      , <br>
    POWOŁUJĄC SIĘ W TYTULE WIADOMOŚCI NA NUMER REJESTRACYJNY POJAZDU.
  </p>

    <?php $vehicle = $injury->vehicle()->first();?>
  <style>
    ol li{
      line-height: 1.3em;
      margin-bottom: 4px;
      font-size: 13px;
    }
  </style>
  <div style="margin-top:14px padding:10px;">
    <?php
    if (array_reverse(explode('/', $vehicle->nr_contract))[0] == 'P' || (array_reverse(explode('/', $vehicle->nr_contract))[0] == 'SK' && isset(array_reverse(explode('/', $vehicle->nr_contract))[1]) && array_reverse(explode('/', $vehicle->nr_contract))[1] == 'P')) {
      $type_spec = false;
    } else {
      $type_spec = true;
    } ?>
    <ol>
      <li>Zgłoszenie szkody komunikacyjnej: {{$injury->injuries_type()->first()->name}}</li>
      @if($injury->injuries_type()->first()->name=='AC'||$injury->injuries_type()->first()->name=='AC-regres')
        <li>Nr polisy AC: {{$injury->injuryPolicy->nr_policy}}</li>
      @endif
      <li>Data zdarzenia: {{ $injury->date_event }}@if($injury->time_event); godzina
        zdarzenia: {{Carbon\Carbon::parse($injury->time_event)->format('H:i')}} @endif </li>
      <li>Miejsce zdarzenia: {{ $injury->event_city . ' ' . $injury->event_street}}</li>
      <li>Właściciel pojazdu poszkodowanego:
        <br>
        @if($type_spec)
          Nazwa: {{$owner->name}}<br>
          Adres: {{ $owner->post }} {{ $owner->city }} {{ $owner->street }}<br>
          NIP: {{ ($owner->data()->where('parameter_id', 8)->first() ) ? $owner->data()->where('parameter_id', 8)->first()->value  : '---' }}
          <br>
          Regon: {{ ($owner->data()->where('parameter_id', 15)->first() ) ? $owner->data()->where('parameter_id', 15)->first()->value  : '---' }}
        @else
              <?php $client = $injury->client()->first();?>
          Nazwa: {{ checkObjectIfNotNull($client, 'name') }}<br>
          Adres: {{ checkObjectIfNotNull($client, 'registry_post') }} {{ checkObjectIfNotNull($client, 'registry_city') }} {{ checkObjectIfNotNull($client, 'registry_street') }}<br>
          NIP:  {{ checkObjectIfNotNull($client, 'NIP') }}<br>
          Regon: {{ checkObjectIfNotNull($client, 'REGON') }}
        @endif
      </li>
      <li>
        Pojazd jest przedmiotem
        @if($type_spec)
          leasingu:
        @else
          cesji:
        @endif
        TAK
      </li>
      <li> Dane
        @if($type_spec)
          leasingobiorcy:
        @else
          cesjonariusza:
        @endif
        <br>
        @if(!$type_spec)
          Nazwa: {{$owner->name}}<br>
          Adres: {{ $owner->post }} {{ $owner->city }} {{ $owner->street }}<br>
          NIP: {{ ($owner->data()->where('parameter_id', 8)->first() ) ? $owner->data()->where('parameter_id', 8)->first()->value  : '---' }}
        @else
              <?php $client = $injury->client()->first();?>
          Nazwa: {{ checkObjectIfNotNull($client, 'name') }}<br>
          Adres: {{ checkObjectIfNotNull($client, 'registry_post') }} {{ checkObjectIfNotNull($client, 'registry_city') }} {{ checkObjectIfNotNull($client, 'registry_street') }}<br>
          NIP:  {{ checkObjectIfNotNull($client, 'NIP') }}
        @endif
      </li>
      <li>Osoba kontaktowa w sprawie oględzin:
        @if($injury->contact_person == 2)
          <p>Imię i nazwisko: {{ $injury->notifier_name }} {{ $injury->notifier_surname }};<br>
            Nr tel. : {{ $injury->notifier_phone }}</p>
        @else
          <p>Imię i nazwisko: {{ ($injury->driver!=null&&$injury->driver->name)? $injury->driver->name : '---' }} {{ ($injury->driver!=null&&$injury->driver->surname)? $injury->driver->surname : '---' }};<br>
            Nr tel. : {{ ($injury->driver!=null&&$injury->driver->phone)? $injury->driver->phone : '---' }}</p>
        @endif
      </li>
      <li>Kierujący pojazdem poszkodowanego:
        <p>Imię i nazwisko: {{ ($injury->driver!=null&&$injury->driver->name)? $injury->driver->name : '---' }} {{ ($injury->driver!=null&&$injury->driver->surname)? $injury->driver->surname : '---' }};<br>
          Nr tel. : {{ ($injury->driver!=null&&$injury->driver->phone)? $injury->driver->phone : '---' }}</p>
      </li>
      <li>Dane pojazdu poszkodowanego:
        <p>
          Nr rej.: {{$vehicle->registration}}<br>
          Marka i model: {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand) }} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)  }}<br>
          Rok produkcji: {{ $vehicle->year_production }}<br>
          Numer VIN: {{ ($injury->vehicle_type == 'Vehicles') ? $vehicle->VIN : $vehicle->vin }}
        </p>
      </li>
      <li>	Rodzaj zdarzenia:
        @if( $injury->type_incident_id != 0 && $injury->type_incident_id != NULL)
          {{ $injury->type_incident()->first()->name}}
        @else
          ---
        @endif
      </li>
      <li> Wykaz uszkodzonych elementów:
          <?php $i=0; $count=$injury->damages()->count();?>
        @foreach($injury->damages()->with('damage')->get() as $damage)
              <?php if($damage->param == 1) {
                  switch($damage->damage->param){
                      case 1:
                          $strona = 'lewy';
                          break;
                      case 2:
                          $strona = 'lewe';
                          break;
                      case 3:
                          $strona = 'lewa';
                          break;
                  }
              }elseif($damage->param == 2){
                  switch($damage->damage->param){
                      case 1:
                          $strona = 'prawy';
                          break;
                      case 2:
                          $strona = 'prawe';
                          break;
                      case 3:
                          $strona = 'prawa';
                          break;
                  }
              }
              $i++;
              ?>
              <?php
              echo trim($damage->damage->name).((isset($strona)) ? ' '.trim($strona) : '').(($i<$count) ? ', ': '');
              ?>
        @endforeach

      </li>
      @if(($injury->injuries_type()->first()->name=='OC'||$injury->injuries_type()->first()->name=='AC-regres')&&$injury->offender_id != 0)
        <li>Dane pojazdu sprawcy:
            <?php $offender = $injury->offender()->first();?>
          <p>
            Nr rej.: {{$offender->registration}}<br>
            Marka i model: {{$offender->car}}<br>
            Nr polisy sprawcy: {{$offender->oc_nr}}<br>
            Nazwa TU sprawcy: {{$offender->zu}}
          </p>
        </li>
      @endif
      <li>Zawiadomiono Policję:
        @if($injury->police == 1)
          tak;
          Jednostka Policji: {{$injury->police_unit}};
          Kontakt z policją: {{$injury->police_contact}};
        @elseif($injury->police == 0)
          nie
        @else
          nie ustalono
        @endif
      </li>
      <li>Sposób rozliczenia szkody: <b>bezgotówkowo na podstawie faktur</b></li>
      <li>Naprawa została zlecona do serwisu:
        @if(isset($branch) && $branch && $branch->id != 0 && $branch->id != '-1')
          <br>
          <b>{{$branch->short_name}}</b>
          <br>
          Adres: {{ $branch->post }} {{$branch->city}}, {{$branch->street}}
      @endif
      <li>Zgłaszający szkodę: <b>
            Centrum Asysty Szkodowej sp. z o.o.,


          ul.Gwiaździsta 66, 53-413 Wrocław

          szkodyasyta@cas-auto.pl

          , tel. 71 33 44 807</b></li>
    </ol>
  </div>
  <p style="font-size:16px; font-weight:bold; text-align:center;">
    NR SZKODY WRAZ Z PODANIEM NR REJ. POJAZDU PROSIMY ODESŁAĆ <br> ZWROTNIE NA ADRES :
      zgloszeniaszkody@cas-auto.pl

  </p>
  @if(isset($inputs['email_comment'])&&$inputs['email_comment']!='')
    <p>
      {{$inputs['email_comment']}}
    </p>
  @endif
</div>
