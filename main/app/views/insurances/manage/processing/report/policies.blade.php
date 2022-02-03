<div class="col-sm-12 col-md-8 col-md-offset-2" id="policies-container" style="display: none;">
    <div class="panel panel-primary">
        <div class="panel-body ">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="progress-container text-center">
                    <div class="alert alert-info" role="alert">
                        <h4>Trwa przetwarzanie</h4>
                        <i class="fa fa-cog fa-spin md-ico"></i>
                    </div>
                </div>
                <div class="panel-group policies-container" role="tablist" id="policies-accordion" aria-multiselectable="true" style="display: none;">
                    <div class="panel panel-default parsed">
                        <div class="panel-heading" role="tab" id="headingParsed">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#policies-accordion" href="#collapseParsed" aria-expanded="true" aria-controls="collapseParsed">
                                    Polisy zaimportowane pomyślnie <span class="badge"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseParsed" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingParsed">
                            <div class="panel-body">
                                <h5>Nr zaimportowanych polis</h5>
                                <ol>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default existing">
                        <div class="panel-heading" role="tab" id="headingExisting">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#policies-accordion" href="#collapseExisting" aria-expanded="true" aria-controls="collapseExisting">
                                    Polisy istniejące już w systemie <span class="badge"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseExisting" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingExisting">
                            <div class="panel-body">
                                <h5>Nr polis istniejących już w systemie</h5>
                                <ol>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default missing">
                        <div class="panel-heading" role="tab" id="headingMissing">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#policies-accordion" href="#collapseMissing" aria-expanded="true" aria-controls="collapseMissing">
                                    Brak polisy do dopasowania <span class="badge"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseMissing" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingMissing">
                            <div class="panel-body">
                                <h5>Nr polis brakujących w systemie</h5>
                                <ol>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="import-warning" style="display: none;">
        <div class="alert alert-warning" role="alert">
            <span class="fa fa-minus" aria-hidden="true"></span>
            <span class="alert-msg"></span>
        </div>
    </div>
</div>