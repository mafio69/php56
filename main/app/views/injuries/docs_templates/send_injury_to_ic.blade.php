<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ url('templates-src/css/notification.css') }}" rel="stylesheet">
    <link href="{{ url('templates-src/css/notification-'.$documentTemplate->slug.'.css') }}" rel="stylesheet">
    <title></title>
</head>
<body>
<?php
$emails=array();
if(isset($inputs['custom_emails']) && $inputs['custom_emails'] != '')
{
    $custom_emails = explode(',', $inputs['custom_emails']);

    foreach ($custom_emails as $custom_email)
    {
        $custom_email = trim($custom_email);

        if( !filter_var($custom_email, FILTER_VALIDATE_EMAIL) === false ) {
            $emails[$custom_email] = $custom_email;
        }else{
            $unmachedEmails[] = $custom_email;
        }
    }
}
if(isset($inputs['insuranceCompanies']))
{
    foreach($inputs['insuranceCompanies']as $insuranceCompany)
    {
        if( !filter_var($insuranceCompany, FILTER_VALIDATE_EMAIL) === false ) {
            $emails[$insuranceCompany] = $insuranceCompany;
        }else{
            $unmachedEmails[] = $insuranceCompany;
        }
    }
}
if(isset($inputs['special_emails'])) {
    foreach ($inputs['special_emails'] as $special_email) {
        $special_email = trim($special_email);

        if (!filter_var($special_email, FILTER_VALIDATE_EMAIL) === false) {
            $emails[$special_email] = $special_email;
        } else {
            $unmachedEmails[] = $special_email;
        }
    }
}
?>
<p style="font-size: 14px; padding:0 20px;">
    Wysłano do
    {{implode(',',$emails)}}
</p>
@include('injuries.docs_templates.send_injury_to_ic_content')

</body>
</html>
