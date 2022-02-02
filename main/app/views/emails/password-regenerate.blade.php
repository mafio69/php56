<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<div>
    <p>Witaj {{$name}}</p>

    <p>W aplikacji CAS zostało nadane nowe hasło startowe.</p>

    <p>
        Obecnie Twoje dane do logowania to:
        <br>
        <strong>Login:</strong> <span>{{$login}}</span>
        <br>
        <strong>Hasło:</strong> <span>{{$password}}</span>
    </p>

    <p>Po uruchomieniu aplikacji przejdziesz w tryb pierwszego logowania.<br>
        Oznacza to, że system poprosi o ustawienie własnego hasła znanego tylko Tobie. Aby tego dokonać należy wprowadzić hasło startowe a następnie dwukrotnie hasło właściwe.</p>

    <p>UWAGA! - Zwróć uwagę czy podczas kopiowania hasła startowego ostatnim znakiem nie jest spacja. <br>
        Podczas ustawiania nowego hasła należy pamiętać aby:
    <ul>
        <li>Hasło miało długość minimum 8 znaków</li>
        <li>Zawierało przynajmniej 3 rodzaje znaków z pośród poniższych:
            <ul>
                <li>Małe litery</li>
                <li>Wielkie litery</li>
                <li>Cyfry</li>
                <li>Znaki specjalne typu @, #, ?, itp.</li>
            </ul></li>
        <li>Hasło nie może się powtarzać przez ostatnich 6 zmian.</li>
    </ul>
    </p>

    <p>Pamiętaj również o tym, że:
    <ul>
        <li>Hasło wygaśnie po upływie 30 dni. Gdy to nastąpi system znów przejdzie w tryb pierwszego logowania i da możliwość wpisania nowego hasła wg zasad opisanych powyżej</li>
        <li>Trzy nieudane próby wprowadzenia hasła blokują konto. Gdy to nastąpi należy skontaktować się z administratorem aplikacji</li>
    </ul>
    </p>

    <p>Wszelkie problemy w działaniu aplikacji rozwiązujemy pod adresem <u>pomoc-it@cas-auto.pl</u></p>
    <br>

    <p>Pozdrawiamy <br>
        Centrum Asysty Szkodowej z o.o.</p>

</div>
</body>
</html>