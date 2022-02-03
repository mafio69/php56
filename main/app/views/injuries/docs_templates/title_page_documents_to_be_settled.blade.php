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

    @page{
        margin: 2cm;
    }
    @import url('https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700&display=swap');
    * {
        /*font-family: "Times New Roman", "Times", serif;*/
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-family: Lato, sans-serif;
        font-size: 11pt;
        line-height: 1.5;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        font-size: 11pt;
    }
    span: {
        margin: 0px;
        padding: 0px;
    }
</style>
<body>
    <h4><b><u>DOKUMENTY PRZEKAZYWANE</u></b></h4>
    <p>Nr szkody: <b>{{$injury->injury_nr}}</b></p>
    <p>Nr umowy leasingowej: <b>{{$vehicle->nr_contract}}</b></p>
    @foreach($invoices as $key => $invoice)
        <p>
            {{$key + 1}}. {{ucfirst(Config::get('definition.fileCategory.'.$invoice->injury_files->category))}}; {{$invoice->serviceType ? $invoice->serviceType->name : "---"}}
             nr: <b>{{$invoice->invoice_nr}}</b><br>
        @if(array_key_exists('document_types', $inputs))
            @if($invoice->injury_files->category == 3 && array_key_exists($invoice->id, $inputs['document_types']))
                wraz z załącznikami
                <ul type='a'>
                    @foreach((array)$inputs['document_types'][$invoice->id] as $document_type)
                        <li>{{$document_type}}</li>
                    @endforeach
                </ul>
            @endif
        @endif
        
        @if(array_key_exists('document_types', $inputs))
            @if(array_key_exists($invoice->id, $inputs['description']))
                <p><b>UWAGI: </b>{{$inputs['description'][$invoice->id]}}</p>
                <br>
            @endif
        @endif
    @endforeach
    
    </p>

    <table style="width: 100%; font-size: 9pt; font-weight:normal;  ">
        <tbody>
            <tr>
                <td style="width:50%; "></td>
                <td style="text-align:center;"></td>
            </tr>
            <tr>
                <td style="width:50%; "></td>
                <td style="text-align:center;">
                    @include('modules.signatures')
                </td>
            </tr>
        </tbody>
        
    </table>

</body>
</html>
