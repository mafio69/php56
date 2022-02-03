@if(Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1 && Auth::user()->can('zlecenia#wprowadz_zlecenie'))
<div class="pull-right">
	<a href="{{ URL::to('dos/other/injuries/make/search') }}" class="btn btn-primary">
		<i class="fa fa-search fa-fw"></i>
		Wyszukaj przedmiot
	</a>
	<a href="{{{ URL::route('dos.other.injuries.create') }}}" class="btn btn-small btn-primary iframe"><span class="glyphicon glyphicon-plus-sign"></span> Wprowadź zlecenie</a>
</div>
@endif
