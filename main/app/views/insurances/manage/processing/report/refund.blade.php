<div class="col-sm-12 col-md-8 col-md-offset-2" id="refund-container" style="display: none;">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Arkusz 'ZWROT SKŁADKI'</h3>
        </div>
        <div class="panel-body ">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="progress-container text-center">
                    <div class="alert alert-info" role="alert">
                        <h4>Trwa przetwarzanie arkusza 'ZWROT SKŁADKI'</h4>
                        <i class="fa fa-cog fa-spin md-ico"></i>
                    </div>
                </div>
                <div class="panel-group agreements-container" role="tablist" id="refund-accordion" aria-multiselectable="true" style="display: none;">
                    <div class="panel panel-default parsed">
                        <div class="panel-heading" role="tab" id="headingRefundParsed">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#refund-accordion" href="#collapseRefundParsed" aria-expanded="true" aria-controls="collapseRefundParsed">
                                    Zwroty składki zakończone sukcesem <span class="badge"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseRefundParsed" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingRefundParsed">
                            <div class="panel-body">
                                <h5>Nr umów na których wykonano zwrot składki</h5>
                                <ol>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default unparsed">
                        <div class="panel-heading" role="tab" id="headingRefundExisting">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#refund-accordion" href="#collapseRefundExisting" aria-expanded="true" aria-controls="collapseRefundExisting">
                                    Zwroty składki zakończone niepowodzeniem <span class="badge"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseRefundExisting" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingRefundExisting">
                            <div class="panel-body">
                                <h5>Nr umów które nie istnieją w systemie - niewykonano zwrotu składki</h5>
                                <ol>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default alreadyArchived">
                        <div class="panel-heading" role="tab" id="headingAlreadyArchived">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#alreadyArchived-accordion" href="#collapseAlreadyArchived" aria-expanded="true" aria-controls="collapseAlreadyArchived">
                                    Umowy które posiadały już zwrot składki <span class="badge"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseAlreadyArchived" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingAlreadyArchived">
                            <div class="panel-body">
                                <h5>Nr umów które posiadają już zwrot składki - niewykonano ponownego zwrotu składki</h5>
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
        <div class="alert alert-warning " role="alert">
            <span class="fa fa-minus" aria-hidden="true"></span>
            <span class="alert-msg"></span>
        </div>
    </div>
</div>