<p>
    Dotyczy:
    NUMER UMOWY: {{$injury->vehicle->nr_contract}}<br>
    Numer szkody: {{$injury->injury_nr}} <br>
    Data szkody: {{$injury->date_event}} <br>
    Pojazd marki: {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}} <br>
    Nr rejestracyjny: {{$injury->vehicle->registration}}<br>
    {{$injury->date_event}}<br>
    Korzystający: {{ ($injury->client) ? $injury->client->name : ''}}
</p>