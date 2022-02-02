<table class="table table-condensed table-contracts">
    <thead>
        <th>#</th>
        <th>Źródło</th>
        <th>Nr umowy</th>
        <th>Obiekt sprawy</th>
        <th>Kategoria</th>
        <th>Właściciel</th>
        <th>Status umowy</th>
        <th>Data ważności umowy</th>
        <th>Nazwa TU</th>
        <th></th>
    </thead>
    @include('dos.other_injuries.create.searched-contract-entities', ['contracts' => $contracts->data, 'lp' => 0])
</table>
@if($contracts->total > 10)
    <div class="row">
        <div class="col-sm-12 text-center">
            <span class="btn btn-primary btn-xs" id="load-next" data-total="{{ $contracts->total }}">
                Załaduj kolejne przedmioty <span class="counter-loaded">10</span>/{{ $contracts->total }}
            </span>
        </div>
    </div>
@endif
