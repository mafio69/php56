@if(Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1)
<div class="pull-right">
    @if(Auth::user()->can('zlecenia_(szkody)#wyszukaj_pojazd'))
        <a href="{{ URL::to('injuries/make/search') }}" class="btn btn-primary">
            <i class="fa fa-search fa-fw"></i>
            Wyszukaj pojazd
        </a>
        {{--<a href="{{{ URL::route('injuries-create') }}}" class="btn btn-small btn-primary iframe"><span class="glyphicon glyphicon-plus-sign"></span> Wprowadź szkodę</a>--}}
    @endif

    @if(Auth::user()->can('zlecenia_(szkody)#wgraj_szkody_nieprzetworzone'))
        <a href="{{ URL::action('InjuriesController@uploadUnprocessed') }}" class="btn btn-small btn-primary">
            <span class="glyphicon glyphicon-plus-sign "></span> Wgraj szkody nieprzetworzone</a>
        </a>
    @endif
</div>

@section('headerJs')
	@parent
	<script>
    $(function () {
        $(document).on('change', 'input:radio[name="rowSelectOptions"]', function(){
            if ($(this).is(':checked') ) {
                var row = $(this).val();
                var hrf = $(this).attr('hrf');
                self.location = hrf+'/'+row;
            }
        });
    });
    </script>
@stop

@endif
