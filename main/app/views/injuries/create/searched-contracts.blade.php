<table class="table table-condensed table-contracts">
    <thead>
        <th>#</th>
        <th>Źródło</th>
        <th>Nr rej.</th>
        <th>Nr umowy</th>
        <th>Program sprzedaży</th>
        <th>Marka</th>
        <th>Model</th>
        <th>Właściciel</th>
        <th>Status umowy<br />
            Data ważności umowy</th>
        <th>Status karty<br />
            Data i forma zbycia</th>
        <th>Nazwa TU</th>
        <th></th>
        <th></th>
    </thead>
    @include('injuries.create.searched-contract-entities', ['contracts' => $contracts->data, 'lp' => 0])
</table>
@if($contracts->total > 10)
    <div class="row">
        <div class="col-sm-12 text-center">
            <span class="btn btn-primary btn-xs" id="load-next" data-total="{{ $contracts->total }}">
                Załaduj kolejne pojazdy <span class="counter-loaded">10</span>/{{ $contracts->total }}
            </span>
        </div>
    </div>
@endif
