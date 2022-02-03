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
    * {
        font-family: "Times New Roman", "Times", serif;
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-size: 11pt;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        line-height: 1.5;
        margin-top: 5px;
        font-size: 11pt;
    }

    b {
        font-size: 11pt;
    }
    table.letter{
        font-weight: bold;
        margin-top: 20px;
        margin-bottom: 50px;
        width: 100%;
        font-size: 0.8em;
    }
</style>

<body style="{{--font-family:'Times';--}}margin: 0.5cm">

<div class="right" style="margin-bottom: 1.5cm; font-size: 11pt; line-height: 1.5"><br></div>
<table class="letter">
    <tr>
        <td></td>
        <td style="width: 9cm; height: 4.5cm; text-align: right; vertical-align: middle;">
            <span style="padding-right: 40px;">Sz. P.</span><br/>
            {{ ($injury->client) ? $injury->client->name : '....................' }}<br />
            {{ ($injury->client) ? $injury->client->correspond_street : $injury->client->registry_street }}<br />
            {{ ($injury->client) ? $injury->client->correspond_post : $injury->client->registry_post }} {{ ($injury->client) ? $injury->client->correspond_city : $injury->client->registry_city }}
        </td>
    </tr>
</table>
</body>
</html>