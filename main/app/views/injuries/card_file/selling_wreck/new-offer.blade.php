@if($injury->wreck && $injury->wreck->pro_forma_request != '0000-00-00' && ! $injury->wreck->scrapped )
    <div class="row marg-btm" id="new_offer">
        <div class="col-sm-12">
            <span class="btn btn-primary btn-sm btn-block modal-open" target="{{ URL::route('injuries.info.getNewOffer', array($injury->wreck->id)) }}" data-toggle="modal" data-target="#modal">
                <i class="fa fa-external-link-square"></i> ponowna sprzedaż
            </span>
        </div>
    </div>
@endif
@if($injury->previousWrecks->count() > 0)
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Historia ofert
                </div>
                <div class="list-group">
                <div role="tablist" aria-multiselectable="true">
                    @foreach($injury->previousWrecks as $lp => $previousWreck)
                        <div role="tab" id="heading{{ $previousWreck->id }}">
                            <a class="btn btn-info btn-sm btn-block off-disable collapsed marg-top-min" role="button"
                                  data-toggle="collapse"
                                  href="#oferta{{ $previousWreck->id }}"
                                  aria-expanded="false"
                                  aria-controls="oferta{{ $previousWreck->id }}">
                                Oferta #{{ ++$lp }} [{{ $previousWreck->created_at->format('Y-m-d') }}]
                            </a>
                        </div>

                        <div id="oferta{{ $previousWreck->id }}" class="panel-collapse collapse " role="tabpanel"  aria-labelledby="heading{{ $previousWreck->id }}">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-6 control-label">Wartość pojazdu w stanie nieuszkodzonym</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $previousWreck->value_undamaged }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-6 control-label">Numer aukcyjny ubezpieczalni</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $previousWreck->nr_auction }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-6 control-label">Wartość pojazdu do odkupu przez LB</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $previousWreck->value_repurchase }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-6 control-label">Cena od oferenta aukcyjnego</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $previousWreck->value_tenderer }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-6 control-label">Data ważności oferty oferenta aukcyjnego</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $previousWreck->expire_tenderer }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-6 control-label">Termin odesłenia deklaracji przez LB</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $previousWreck->alert_repurchase }}</p>
                                    </div>
                                </div>
                                @if($previousWreck->buyerInfo)
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label">Nabywca</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static">{{ $previousWreck->buyer ? Config::get('definition.wreck_buyers')[$previousWreck->buyer] : '' }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label">Termin zwrotu potwierdzenia odkupu</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static">{{ $previousWreck->alert_buyer }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        @include('injuries.card_file.partials.buyer-info-table', ['buyer' => $previousWreck->buyerInfo])
                                    </div>
                                @endif
                            </form>
                        </div>
                    @endforeach
                </div>
                </div>
            </div>
        </div>
    </div>
@endif
