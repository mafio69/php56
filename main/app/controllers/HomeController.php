<?php

class HomeController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

	public function index()
	{
        $locked_users = User::where('active', 0)->whereNotNull('locked_at')->get();

        $non_vat_companies = Cache::remember('non_vat_companies', 60, function()
        {
            return Company::whereHas('groups', function ($query){
                        $query->whereIn('company_group_id', [1,5]);
                    })->where('is_active_vat', 0)->with('companyVatCheck')->limit(100)->get();
        });



        $changes_vat_companies = Cache::remember('changes_vat_companies', 60, function(){

            $changes_vat_companies = [];

            Company::whereHas('groups', function ($query){
                $query->whereIn('company_group_id', [1,5]);
            })->where('is_active_vat', 1)
                ->whereHas('companyVatCheck', function ($query){
                    $query->where('created_at', '>', \Carbon\Carbon::now()->subDays(7)->startOfDay());
                })
                ->limit(100)
                ->chunk(50, function($changes_vat_companies_db) use(&$changes_vat_companies){
                    $changes_vat_companies_db->load('companyVatCheck');

                    foreach($changes_vat_companies_db as $item)
                    {
                        if($item->companyVatCheck->status_code != 'C'){
                            $changes_vat_companies[] = $item;
                        }
                    }
                });



            Company::whereHas('groups', function ($query){
                $query->whereIn('company_group_id', [1,5]);
            })->where('is_active_vat', 1)
                ->whereHas('companyVatCheck', function ($query){
                    $query->where('created_at', '>', \Carbon\Carbon::now()->subDays(7)->startOfDay());
                })
                ->limit(100)
                ->chunk(50, function($new_vat_companies_db) use (&$changes_vat_companies){
                    $new_vat_companies_db->load('companyVatCheck');

                    foreach($new_vat_companies_db as $item)
                    {
                        if($item->companyVatCheck &&  $item->companyVatCheck->status_code == 'C'){
                            $changes_vat_companies[] = $item;
                        }
                    }
                });


            return $changes_vat_companies;
        });


        return View::make('panel.home', compact('locked_users', 'non_vat_companies', 'changes_vat_companies'));
	}


	public function logout()
	{
		Auth::logout();
		return Redirect::to('/');
	}

	public function postCheckAsConnection()
	{
        $owners = Owners::whereActive('0')->where('wsdl', '!=', '')->get();
        foreach($owners as $owner) {
            $owner_id = $owner->id;

            Webservice::establishSoap($owner_id);
        }

        Settings::set('as-connection','active');
        return json_encode(['status' => 'success']);
	}

    public function locked()
    {
        Auth::logout();
        return View::make('panel.locked');
    }
}
