<ul class="nav nav-tabs" id="info_tabs">
    <li class="active"><a href="#communicator" data-toggle="tab">Komunikator</a></li>
    <li><a href="#agreement-data" data-toggle="tab">Dane umowy</a></li>
    <li><a href="#objects-data" data-toggle="tab">Przedmioty umowy</a></li>
    @if($agreement->insurances)
        <li><a href="#insurances-data" data-toggle="tab">Polisy majątkowe</a></li>
    @endif
    <li><a href="#files" data-toggle="tab">Dokumentacja</a></li>
    <li><a href="#documents" data-toggle="tab">Generowanie dokumentów</a></li>
    <li>
        <a href="#history" data-toggle="tab">
            @if($agreement->history()->whereHas('type', function($query){
                $query->whereWarning(1);
            })->first())
                <i class="fa fa-exclamation-triangle red"></i>
            @endif
            Historia
        </a>
    </li>
</ul>
