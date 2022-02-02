<div class="tab-pane fade in" id="insurances-data">
    <h4 class="page-header marg-top-min overflow">
        <small>
            <span class="label label-default">Polisa zwykła</span>
            <span class="label label-primary">Polisa wznowiona</span>
            <span class="label label-info">Polisa po zwrocie</span>
        </small>
        @if(is_null($agreement->archive) && Auth::user()->can('kartoteka_polisy#zarzadzaj'))
            <a class="btn btn-primary pull-right" href="{{ URL::to('insurances/manage-actions/assign-to-yacht', [$agreement->id]) }}">
                <i class="fa fa-plus"></i> dodaj polisę do umowy
            </a>
            <a class="btn btn-primary pull-right marg-right" href="{{ URL::to('insurances/manage-actions/resume-yacht', [$agreement->id]) }}">wznów umowę</a>
        @endif
    </h4>

    <div class="row">
        @include('insurances.manage.card_file-yacht.partials.resumed')
        @include('insurances.manage.card_file-yacht.partials.refund_and_non_resumed')
    </div>
</div>

