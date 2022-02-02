<?php

class GapAgreementsController extends \BaseController {

	protected $gap_statuses;
	public function __construct(){
		$this->gap_statuses = GapAgreementStatus::all()->keyBy('name');
		 View::share('gap_statuses',$this->gap_statuses);
	}
	public function getNew()
	{
				$status = null;

				if(isset($this->gap_statuses['new']))
					$status = $this->gap_statuses['new'];

         $agreements = GapAgreement::
                where(function($query) {
                    //czy ustawione jest filtrowanie wyszukiwaniem
                    $this->passingWheres($query);
                })
								->where('status_id',$status->id)
                ->with('object.type', 'object.group', 'group', 'type')
                ->orderBy('id', 'asc')->paginate(Session::get('search.pagin', '10'));

        return View::make('gap.manage.new', compact('agreements','status'));
	}

		//Zrobić
    public function getSearch(){
        $last =  URL::previous();
        $url = strtok($last, '?');

        $gets = '?';

        if(Input::has('search_term')){

            if(Input::has('nr_contract'))
                $gets .= 'nr_contract=1&';

            if(Input::has('client_name'))
                $gets .= 'client_name=1&';

            if(Input::has('client_NIP'))
                $gets .= 'client_NIP=1&';

            if(Input::has('object_name'))
                $gets .= 'object_name=1&';

            if(Input::has('policy_nb'))
                $gets .= 'policy_nb=1&';

            $gets.='term='.Input::get('search_term').'&';
        }

        if(Input::has('warnings'))
            $gets .= 'warnings=1&';

        if(Input::has('yachts'))
            $gets .= 'yachts=1&';

        if(Input::has('foreign_policy'))
            $gets .= 'foreign_policy=1&';

        if(Input::has('global')){
            return url('insurances/manage/searchGlobal').$gets;
        }else{
            return $url.$gets;
        }
    }
		//Zrobić

    public function searchGlobal()
    {
        $leasingAgreements = GapAgreement::
            where(function($query) {
                //czy ustawione jest filtrowanie wyszukiwaniem
                $this->passingWheres($query);
            })
            ->with('insurances','client', 'user', 'leasingAgreementType', 'leasingAgreementPaymentWay')
            ->orderBy('id', 'asc')->paginate(Session::get('search.pagin', '10'));

        return View::make('insurances.manage.searchGlobal', compact('leasingAgreements'));
    }


    private function passingWheres(&$query, $parameters = null)
    {
        if(is_null($parameters))
            $parameters = Input::all();

        if ( isset($parameters['term']) ) {
            $query->where(function ($query2) use($parameters){
                if (isset($parameters['nr_contract'])) {
                    $query2->orWhere('nr_contract', 'like', $parameters['term']);
                }
                if (isset($parameters['client_name'])) {
                    $query2->orWhereHas('client', function($query3) use($parameters){
                        $query3->where('name', 'like', '%'.$parameters['term'].'%');
                    });
                }
                if (isset($parameters['client_NIP'])) {
                    $query2->orWhereHas('client', function($query3) use($parameters){
                        $query3->where('NIP', 'like', '%'.$parameters['term'].'%');
                    });
                }

                if(isset($parameters['object_name'])){
                    $query2->orWhereHas('objects', function($query) use ($parameters){
                        $query->where('name', 'like', $parameters['term'].'%');
                    });
                }
                if( isset($parameters['policy_nb'])){
                    $query2->orWhereHas('insurances', function ($query) use($parameters){
                        $query->where('insurance_number', 'like', '%'.$parameters['term'].'%');
                    });
                }
            });
        }
        if (isset($parameters['warnings'])) {
            $query->where('detect_problem', '=', '1');
        }
        if (isset($parameters['yachts']) || Session::get('search.yachts_filter', '0') != 0) {
            $query->where('has_yacht', '=', '1');
        }

        if (isset($parameters['foreign_policy']) || Session::get('search.foreign_policy', '0') != 0) {
            $query->whereHas('insurances', function($query){
                $query->where('if_foreign_policy', '=', '1');
            });
        }

        if( Session::get('search.insurance_company_filter', '') != '') {
            $query->where(function($query){
                $query->whereHas('insurances', function($query){
                    $query->whereActive(1)->whereHas('insuranceCompany', function($query){
                        $query->where('name', 'like', '%'.Session::get('search.insurance_company_filter').'%');
                    });
                })->orWhere('import_insurance_company', 'like', '%'.Session::get('search.insurance_company_filter').'%');
            });
        }

        return $query;
    }



}
