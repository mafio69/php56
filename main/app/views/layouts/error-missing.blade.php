<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Szukana strona strona nie istnieje</title>
    <style>
        * {
            font-family: sans-serif;
            line-height: 1.4em;
        }

        body {
            background: #fcfcfc;
            -webkit-font-smoothing: antialiased;
            -moz-font-smoothing: antialiased;
            -ms-font-smoothing: antialiased;
            -o-font-smoothing: antialiased;
            font-smoothing: antialiased;
        }

        h1 {
            text-align: center;
            font-size: 3.5em;
        }

        .error-message {
            padding: 30px;
            background: #fff;
            color: black;
        }

        a {
            color: black;
        }

        @media screen and (min-width: 800px) {
            h1 {
                margin-top: 75px;
            }
            .error-message {
                max-width: 700px;
                margin: 50px auto;
                border-radius: 10px;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>

<h1>Szukana strona strona nie istnieje</h1>

<section class="error-message">
    <p>Szukana strona strona nie istnieje w systemie. Zespół programistów został powiadomiony o zaistniałej sytuacji.</p>
    <a href="{{ URL::previous() }}">powróć do poprzedniej strony</a>
    <a style="float: right;" href="/">strona główna</a>
</section>

</body>
</html>
