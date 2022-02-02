<div class="pull-right legend">
    <h6>Problem w trakcie importu - <i class="fa fa-exclamation-triangle red"></i></h6>
    <h6>Zawiera jacht - <i class="fa fa-ship blue"></i></h6>
    <h6>Zawiera polisę obcą - <i class="fa fa-code-fork blue"></i></h6>
    <h6>Umowa obca <i class="fa fa-globe blue"></i></h6>

    @if( in_array(Request::segment(3), ['inprogress', 'resume', 'resume-outdated']) )
        <h6>Umowa oznaczona do wznowienia - <i class="fa fa-flag warning"></i></h6>
    @endif
</div>