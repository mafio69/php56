<?php

class InsurancesInfoController extends \BaseController {


    function __construct()
    {
        $this->beforeFilter('permitted:kartoteka_polisy#wejscie');
    }

    public function getShow($id)
	{
        Session::put('last_agreement', $id);
        $url = URL::previous();
        if(	$url != NULL && $url != '' && isset($url) && isset($_SERVER['HTTP_REFERER']) ){

            $path = parse_url($url);
            $short_url = $path['path'];
            $short_url = explode('/',$short_url);
            if($short_url[1] == 'insurances' && in_array($short_url[3], ['index', 'resume', 'resume-outdated', 'inprogress', 'archive' ]))
                Session::put('prev', $url);
            else
                Session::put('prev', URL::to('insurances/manage/inprogress'));
        }

        $agreement = LeasingAgreement::with('insurance_group_row', 'client', 'objects', 'objects.object_assetType', 'insurances', 'user', 'leasingAgreementType',
                                            'leasingAgreementPaymentWay', 'history', 'history.content', 'history.type', 'history.user',
                                            'insurances.coverages', 'insurances.coverages.type', 'insurances.payments',
                                            'conversations', 'conversations.messages', 'conversations.user', 'conversations.messages.user')
                                        ->find($id);
        $documentsTypes = array();

        $insurance = $agreement->insurances->last();

        if($insurance&&$insurance->insuranceCompany){
         $documentsTypes =  $insurance->insuranceCompany->documentsTypes;
        }

        if($agreement->has_yacht == 1) {
            return View::make('insurances.manage.info-yacht', compact('agreement','documentsTypes'));
        }else{
            return View::make('insurances.manage.info', compact('agreement','documentsTypes'));
        }
	}

    /*
     * Podgląd poprzednich leasingobiorców
     */

    public function getShowCessions($agreement_id)
    {
        $agreement = LeasingAgreement::find($agreement_id);
        $cessions = $agreement->cessions;

        return View::make('insurances.manage.card_file.dialog.show_cessions', compact('cessions'));
    }


}
