<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Wystąpił błąd w systemie!</title>
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

<h1>Wystąpił błąd w systemie!</h1>

<section class="error-message">
	<p>Wystąpił błąd w trakcie wykonywania operacji w systemie. Zespół programistów został powiadomiony o zaistniałym wyjątku.</p>
	<a href="{{ URL::previous() }}">powróć do poprzedniej strony</a>
	<a style="float: right;" href="/">strona główna</a>
</section>

</body>
</html>
