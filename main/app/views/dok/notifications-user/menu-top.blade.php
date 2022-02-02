@if(Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1)
<div class="pull-right">
	<a href="{{{ URL::route('dok.notifications.create') }}}" class="btn btn-small btn-primary iframe"><span class="glyphicon glyphicon-plus-sign"></span> Wprowadź zgłoszenie</a>
</div>
@endif
