<div class="pull-left">
    <ul  class="nav nav-pills nav-injuries btn-sm">
        <li class="<?php if(Request::segment(3) == 'index') echo 'active'; ?> ">
            <a href="{{{ URL::to('insurances/manage/index' ) }}}">Nowe</a>
        </li>

        <li class="<?php if(Request::segment(3) == 'inprogress') echo 'active'; ?> ">
            <a href="{{{ URL::to('insurances/manage/inprogress' ) }}}" >Trwające</a>
        </li>

        <li class="separated">|</li>

        <li class="<?php if (Request::segment(3) == 'resume') echo 'active'; ?>">
            <a href="{{{ URL::to('insurances/manage/resume' ) }}}">Wznowienia aktualne</a>
        </li>

        <li class="<?php if (Request::segment(3) == 'resume-outdated') echo 'active'; ?>">
            <a href="{{{ URL::to('insurances/manage/resume-outdated',[1] ) }}}">Wznowienia minione</a>
        </li>

        <li class="<?php if (Request::segment(3) == 'future-resume') echo 'active'; ?>">
            <a href="{{ URL::to('insurances/manage/future-resume' ) }}">Wznowienia przyszłe </a>
        </li>

        <li class="separated">|</li>

        <li class="<?php if (Request::segment(3) == 'other') echo 'active'; ?>">
            <a href="{{{ URL::to('insurances/manage/other' ) }}}">Obce <i class="fa fa-globe"></i></a>
        </li>

        <li class="<?php if (Request::segment(3) == 'other-new') echo 'active'; ?>">
            <a href="{{{ URL::to('insurances/manage/other-new' ) }}}">Obce nowe</a>
        </li>

        <li class="<?php if (Request::segment(3) == 'other-resume') echo 'active'; ?>">
            <a href="{{{ URL::to('insurances/manage/other-resume' ) }}}">Wznowienia obce</a>
        </li>

        <li class="separated">|</li>

        <li class="<?php if(Request::segment(3) == 'archive') echo 'active'; ?>">
            <a href="{{{ URL::to('insurances/manage/archive' ) }}}">Archiwum</a>
        </li>
        <li class="separated">|</li>

        <li class="<?php if (Request::segment(3) == 'withdraw') echo 'active'; ?>">
            <a href="{{{ URL::to('insurances/manage/withdraw' ) }}}">Wycofane</a>
        </li>

    </ul>
</div>

<div class="input-group input-group-sm pull-left col-sm-4 col-md-2 marg-left editable-global" style="margin-top: 3px;">
    <span class=" input-group-addon " id="sizing-addon3">Globalny nr zgłoszenia:</span>
    <span class="form-control global-nr-container"  aria-describedby="sizing-addon3">
        <a href="#" id="insurances_global_nr-editable" class="editable editable-click"
            data-type="combodate"
            data-pk="insurances_global_nr"
            data-token="{{ csrf_token() }}"
            data-url="{{ URL::to('insurances/manage-actions/update-global-nr') }}"
            data-title="Wprowadź aktualny nr zgłoszenia">
            {{ Auth::user()->insurances_global_nr }}
        </a>
    </span>
</div>

{{ Form::open(array('url' => URL::to('insurances/manage/generate-excel', [Request::segment(3)] ))) }}
    @foreach(Input::query() as $parameterName => $parameter)
        {{ Form::hidden('parameters['.$parameterName.']', $parameter) }}
    @endforeach
    @if(Request::segment(4))
        {{ Form::hidden('resume-outdated', Request::segment(4))}}
    @endif
<button type="submit" class="btn btn-sm btn-info pull-left marg-left" style="margin-top: 3px;">
    <i class="fa fa-file-excel-o"></i> generuj zrzut do Excela
</button>
{{ Form::close()}}


<div class="pull-right search-box">
    <div class="pull-right">
        @if(Input::has('term'))
            <span class="label label-primary pull-right " style="margin-left:10px; font-size:14px;">
      {{ Input::get('term') }}
      </span>
        @endif
        <i class="fa fa-search show-search font-xlarge  pull-right " ></i>
    </div>

    <div class="panel panel-default search-adv" >
        <div class="panel-heading">
            <h4 class="panel-title">Filtrowanie polis</h4>
        </div>
        <div class="panel-body">
            <div class="form-group" style="margin-bottom:0px;">
                {{ Form::open(array('url' => URL::to('insurances/manage/search' ), 'id' => 'search-adv-form')) }}
                    <div class="row ">
                        <div class="col-sm-12 marg-btm">
                            <input class="form-control" name="search_term" placeholder="wprowadź szukaną frazę"
                            @if(Input::has('term'))
                                   value ="{{ Input::get('term') }}"
                                    @endif
                                    >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 marg-btm">
                            <div class="btn-group" >
                                <label class="btn btn-info btn-check btn-xs" >
                                    <input type="checkbox" name="nr_contract" value="1" checked readonly >nr umowy leasingowej
                                </label>
                            </div>

                            <div class="btn-group" >
                                <label class="btn btn-info btn-check btn-xs">
                                    <input type="checkbox" name="client_name" value="1"
                                    @if(Input::has('client_name'))
                                           checked
                                            @endif
                                            >nazwa leasingobiorcy
                                </label>
                            </div>

                            <div class="btn-group" >
                                <label class="btn btn-info btn-check btn-xs">
                                    <input type="checkbox" name="client_NIP" value="1"
                                    @if(Input::has('client_NIP'))
                                           checked
                                            @endif
                                            >NIP leasingobiorcy
                                </label>
                            </div>

                            <div class="btn-group" >
                                <label class="btn btn-info btn-check btn-xs">
                                    <input type="checkbox" name="object_name" value="1"
                                           @if(Input::has('object_name'))
                                           checked
                                            @endif
                                    >nazwa przedmiotu
                                </label>
                            </div>
                            <div class="btn-group" >
                                <label class="btn btn-info btn-check btn-xs">
                                    <input type="checkbox" name="policy_nb" value="1"
                                           @if(Input::has('policy_nb'))
                                           checked
                                            @endif
                                    >nr polisy
                                </label>
                            </div>
                            <hr class="marg-top-min marg-btm"/>
                            <div class="btn-group">
                                <label class="btn btn-danger btn-check btn-xs" >
                                    <input type="checkbox" name="warnings" value="1"
                                    @if(Input::has('warnings'))
                                           checked
                                            @endif
                                            ><i class="fa fa-exclamation-triangle"></i>  błędny import
                                </label>
                            </div>
                            <div class="btn-group" >
                                <label class="btn btn-info btn-check btn-xs" >
                                    <input type="checkbox" name="yachts" value="1"
                                    @if(Input::has('yachts'))
                                           checked
                                            @endif
                                            ><i class="fa fa-ship"></i>  zawiera jacht
                                </label>
                            </div>
                            <div class="btn-group" >
                                <label class="btn btn-info btn-check btn-xs" >
                                    <input type="checkbox" name="foreign_policy" value="1"
                                        @if(Input::has('foreign_policy'))
                                           checked
                                        @endif
                                    ><i class="fa fa-code-fork"></i>  polisa obca
                                </label>
                            </div>
                            <div class="btn-group">
                                <label class="btn btn-info btn-check btn-xs">
                                    <input type="checkbox" name="global" value="1"
                                    @if(Input::has('global'))
                                           checked
                                            @endif
                                            >wyszukaj globalnie
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-10 col-sm-offset-1">
                            <button class="btn btn-primary btn-sm pull-right" id="search-adv" style="width:100%"> <i class="fa fa-search"></i> szukaj </button>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {

            $('.btn-check input').change(function(){
                if( $(this).prop('checked') ){
                    $(this).closest('.btn').addClass('active');
                }else{
                    $(this).closest('.btn').removeClass('active');
                }
            }).change();

            $('#search-adv-form').on('click', '#search-adv', function(){
                $.ajax({
                    type: "POST",
                    url: $('#search-adv-form').prop( 'action' ),
                    data: $('#search-adv-form').serialize(),
                    assync:false,
                    cache:false,
                    success: function( data ) {
                        self.location = data;
                    }

                });

                return false;
            });
            $('input[name="search_term"]').bind("keypress", function(e) {
                if( e.which == 13 ){
                    $.ajax({
                        type: "POST",
                        url: $('#search-adv-form').prop( 'action' ),
                        data: $('#search-adv-form').serialize(),
                        assync:false,
                        cache:false,
                        success: function( data ) {
                            self.location = data;
                        }
                    });

                    return false;
                }
            });

            $('#insurances_global_nr-editable').editable({
                format: 'MM/YYYY',
                viewformat: 'MM/YYYY',
                template: 'MM / YYYY',
                combodate: {
                    //minYear: "{{ date('Y') }}",
                    maxYear: "{{ Date::make()->addYears(10)->year }}"
                },
                validate: function(value) {
                    if($.trim(value) == '') {
                        return 'Pole wymagane';
                    }
                    if(!confirm('Potwierdź zmianę swojego aktualnego nr zgłoszenia.')){
                        return 'Operacja anulowana';
                    }
                },
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                success: function(response, newValue) {
                    if(!response) {
                        return "Unknown error!";
                    }
                    if(response.success === false) {
                        return response.msg;
                    }else{
                        $.notify({
                            icon: "fa fa-check",
                            message: response.notification
                        },{
                            type: 'success',
                            placement: {
                                from: 'bottom',
                                align: 'right'
                            },
                            delay: 2500,
                            timer: 500
                        });
                    }
                }
            });

        });
    </script>
@stop

