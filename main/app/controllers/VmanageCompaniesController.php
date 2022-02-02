<?php

class VmanageCompaniesController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
	}

	public function getIndex()
	{
		$companies = VmanageCompany::with('owner')->paginate(Session::get('search.pagin', '20'));

		$ownersData = Idea_data::get();
		$ownersInfo = [];
		foreach($ownersData as $data)
		{
			$ownersInfo[$data->owner_id][$data->parameter_id] = $data->value;
		}

		return View::make('vmanage.companies.index', compact('companies', 'ownersInfo'));
	}

	public function postLoadCounter()
    {
        $section = Input::get('section');

        $counters = VmanageVehicle::
                selectRaw('count(*) as total, vmanage_company_id')
                ->where(function($query) use($section){
                    if($section == 'vehicles'){
                        $query->where('if_truck', 0);
                    }else{
                        $query->where('if_truck', 1);
                    }
                })
                ->where('outdated', 0)
                ->groupBy('vmanage_company_id')
                ->lists('total', 'vmanage_company_id');

        return Response::json($counters);
    }


	public function getCreate()
	{
		return View::make('vmanage.companies.create');
	}

	public function postStore()
	{
		$data = Input::all();
		$rules = array(
			'nip'  => 'unique:vmanage_companies'
		);
		$validation = Validator::make($data, $rules);

		if ($validation->fails())
		{
			Flash::error('Istnieje już w systemie firma o podanym numerze NIP.');
			return Redirect::back()->withInput();
		}
        $data['owners_group_id'] = 4;
        $data['short_name'] = shortenName($data['name']);
        $data['nip'] = trim(str_replace('-', '', $data['nip']));

        $owner = Owners::create($data);
        $data['owner_id'] = $owner->id;

		$matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
		$registry_post = $data['post'];
		if(strlen($registry_post) == 6)
		{
			$voivodeship_id = $matcher->match($registry_post);
			$data['registry_voivodeship_id'] = $voivodeship_id;
			$data['correspond_voivodeship_id'] = $voivodeship_id;
		}


        $client = Clients::create([
            'name' => $data['name'],
            'NIP' => $data['nip'],
            'REGON' => $data['regon'],
            'registry_post' => $data['post'],
            'registry_city' => $data['city'],
            'registry_street' => $data['street'],
			'registry_voivodeship_id' => (isset($data['registry_voivodeship_id'])) ? $data['registry_voivodeship_id'] : null,
            'correspond_post' => $data['post'],
            'correspond_city' => $data['city'],
            'correspond_street' => $data['street'],
			'correspond_voivodeship_id' => (isset($data['correspond_voivodeship_id'])) ? $data['correspond_voivodeship_id'] : null,
            'phone' => $data['phone'],
            'email' => $data['mail']
        ]);

        $data['client_id'] = $client->id;

		VmanageCompany::create($data);

		Flash::success('Firma została utworzona.');
		return Redirect::action('VmanageCompaniesController@getIndex');
	}


	public function getEdit($id)
	{
		$company = VmanageCompany::find($id);
		return View::make('vmanage.companies.edit', compact('company'));
	}


	public function postUpdate($id)
	{
		$data = Input::all();
		$rules = array(
			'nip'  => 'unique:vmanage_companies,nip,'.$id
		);
		$validation = Validator::make($data, $rules);

		if ($validation->fails())
		{
			Flash::error('Istnieje już w systemie firma o podanym numerze NIP.');
			return Redirect::back()->withInput();
		}
		$company = VmanageCompany::find($id);
		$company->update($data);
        $company->save();

        $company->client->update([
            'name' => $data['name'],
            'NIP' => $data['nip'],
            'REGON' => $data['regon'],
            'registry_post' => $data['post'],
            'registry_city' => $data['city'],
            'registry_street' => $data['street'],
            'correspond_post' => $data['post'],
            'correspond_city' => $data['city'],
            'correspond_street' => $data['street'],
            'phone' => $data['phone'],
            'email' => $data['mail']
        ]);

		Flash::success('Dane firmy '.$company->name.' zostały zaktualizowane.');
		return Redirect::action('VmanageCompaniesController@getIndex');
	}


	public function getDelete($id)
	{
		$company = VmanageCompany::find($id);
		return View::make('vmanage.companies.delete', compact('company'));
	}


	public function postDestroy($id)
	{
		$company = VmanageCompany::find($id);

        $company->vehicles()->delete();
        $company->users()->delete();
		$company->delete();

		$result['code'] = 0;
		Flash::success('Firma '.$company->name.' został usunięta wraz podlegającymi jej pojazdami.');
		return json_encode($result);
	}

	public function getCsm($id)
	{
		$csm_types = VmanageCsmType::whereDefault(1)->orWhere('vmanage_company_id', $id)->lists('name', 'id');
		$company = VmanageCompany::with('csm', 'csm.csmType')->find($id);
		return View::make('vmanage.companies.csm', compact('csm_types', 'company'));
	}

	public function getEditCsm($company_id, $csm_type_id)
	{
		$company = VmanageCompany::with('csm', 'csm.csmType')->find($company_id);
		$csm_type = VmanageCsmType::find($csm_type_id);

		$content = null;
		if($company->csm()->where('vmanage_csm_type_id', $csm_type_id)->first())
			$content = $company->csm()->where('vmanage_csm_type_id', $csm_type_id)->first()->content;



		return View::make('vmanage.companies.edit_csm', compact('company', 'csm_type', 'content'));
	}

	public function postEditCsm($company_id, $csm_type_id)
	{
		$company = VmanageCompany::find($company_id);
		$csm = $company->csm()->where('vmanage_csm_type_id', $csm_type_id)->first();
        $content = preg_replace('/\s+/', ' ',Input::get('content'));

        if( $csm )
		{
			$csm->content = $content;
			$csm->save();
		}else{
			VmanageCompanyCsm::create([
				'vmanage_company_id' => $company_id,
				'vmanage_csm_type_id' => $csm_type_id,
				'content' => $content
			]);
		}

		$result['code'] = 0;
		Flash::success('Informacje zostały zaktualizowane.');
		return json_encode($result);
	}

    public function getCreateCsm($company_id)
    {
        $company = VmanageCompany::find($company_id);
        return View::make('vmanage.companies.create_csm', compact('company'));
    }

    public function postCreateCsm($company_id)
    {
        $csmType = VmanageCsmType::
            where(function($query) use($company_id){
                $query->where('default', 1);
                $query->orWhere('vmanage_company_id', $company_id);
            })->where('name', 'like', Input::get('name'))->first();

        if($csmType)
        {
            $result['code'] = 3;
            $result['error'] = 'Istnieje już podany typ przypisany do firmy';
            return json_encode($result);
        }

        $csmType = VmanageCsmType::create([
            'name' => Input::get('name'),
            'vmanage_company_id' => $company_id
        ]);

        $content = preg_replace('/\s+/', ' ',Input::get('content'));
        VmanageCompanyCsm::create([
            'vmanage_company_id' => $company_id,
            'vmanage_csm_type_id' => $csmType->id,
            'content' => $content
        ]);

        $result['code'] = 0;
        Flash::success('Informacje zostały dodane.');
        return json_encode($result);
    }

	public function getExportVehicles($company_id)
	{
		$company = VmanageCompany::find($company_id);

		Excel::create('Baza aut - '.$this->normalizeString($company->name), function($excel) use ($company){
			$excel->sheet('baza', function($sheet) use ($company){
				$sheet->appendRow([
					'MARKA',
					'MODEL',
					'NRPOLISY',
					'POCZATEKUBEZP',
					'KONIECUBEZP',
					'NRREJESTRACYJNY',
					'VIN',
					'OPCJAASSISTANCE',
					'OPCJA_ASSISTANCE_2',
					'DATAREJESTRACJI',
					'ROKPRODUKCJI',
					'TOWARZYSTWO',
					'WLASCICIEL',
					'ADRESWLASCICIELA',
					'MIASTOWLASCICIELA',
					'REGON',
					'UZYTKOWNIK',
					'U_ADRES',
					'NIP_Wlasciciel',
					'KOMENTARZOKLIENCIE',
					'ATRYBUT',
					'KONTRAKT',
					'DATAWPISU',
					'RODZAJWPISU'
				]);

				$ownersData = Idea_data::get();
				$ownersInfo = [];
				foreach($ownersData as $data)
				{
					$ownersInfo[$data->owner_id][$data->parameter_id] = $data->value;
				}

				foreach( $company->vehicles()->with('brand', 'model', 'insurance_company', 'owner', 'user')->get() as $vehicle) {
					$sheet->appendRow([
                        ($vehicle->brand && $vehicle->brand_id != '0') ? $vehicle->brand->name : '',
                        ($vehicle->model && $vehicle->model_id != '0') ? $vehicle->model->name : '',
						'',
						'',
						'',
						$vehicle->registration,
						$vehicle->vin,
						$vehicle->assistance,
						'',
						$vehicle->first_registration,
						$vehicle->year_production,
						($vehicle->insurance_company) ? $vehicle->insurance_company->name : '',
						($vehicle->owner) ? $vehicle->owner->name : '',
						($vehicle->owner) ? $vehicle->owner->street : '',
						($vehicle->owner) ? $vehicle->owner->post.' '.$vehicle->city : '',
						(isset($ownersInfo[$vehicle->owner_id]) && isset($ownersInfo[$vehicle->owner_id][15])) ? $ownersInfo[$vehicle->owner_id][15] : '',
						($vehicle->user) ? $vehicle->user->name.' '.$vehicle->user->surname : '',
						'',
						(isset($ownersInfo[$vehicle->owner_id]) && isset($ownersInfo[$vehicle->owner_id][8])) ? $ownersInfo[$vehicle->owner_id][8] : '',
					]);
				}
			});
		})->download();

	}

	public function getExportVehiclesCsv($company_id, $if_truck){
			$company = VmanageCompany::find($company_id);

			$fileName = 'Baza aut - '.$this->normalizeString($company->name).'.csv';

			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header('Content-Description: File Transfer');
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename={$fileName}");
			header("Expires: 0");
			header("Pragma: public");

			$fh = @fopen( 'php://output', 'w' );

			fputcsv($fh,[
				'MARKA',
				'MODEL',
				'NRPOLISY',
				'POCZATEKUBEZP',
				'KONIECUBEZP',
				'NRREJESTRACYJNY',
				'VIN',
				'OPCJAASSISTANCE',
				'OPCJA_ASSISTANCE_2',
				'DATAREJESTRACJI',
				'ROKPRODUKCJI',
				'TOWARZYSTWO',
				'WLASCICIEL',
				'ADRESWLASCICIELA',
				'MIASTOWLASCICIELA',
				'REGON',
				'UZYTKOWNIK',
				'U_ADRES',
				'NIP_Wlasciciel',
				'KOMENTARZOKLIENCIE',
				'ATRYBUT',
				'KONTRAKT',
				'DATAWPISU',
				'RODZAJWPISU'
			]);

			$ownersData = Idea_data::get();
			$ownersInfo = [];
			foreach($ownersData as $data)
			{
				$ownersInfo[$data->owner_id][$data->parameter_id] = $data->value;
			}

			if($if_truck == 0) {
                $company->vehicles()->chunk(1000, function($vehicles) use(&$fh,$ownersInfo){
                    $vehicles->load('brand', 'model', 'insurance_company', 'owner', 'user');
                    foreach ($vehicles as $vehicle) {
                        $data = [
                            ($vehicle->brand && $vehicle->brand_id != '0') ? $vehicle->brand->name : '',
                            ($vehicle->model && $vehicle->model_id != '0') ? $vehicle->model->name : '',
                            '',
                            '',
                            '',
                            $vehicle->registration,
                            $vehicle->vin,
                            $vehicle->assistance,
                            '',
                            $vehicle->first_registration,
                            $vehicle->year_production,
                            ($vehicle->insurance_company) ? $vehicle->insurance_company->name : '',
                            ($vehicle->owner) ? $vehicle->owner->name : '',
                            ($vehicle->owner) ? $vehicle->owner->street : '',
                            ($vehicle->owner) ? $vehicle->owner->post . ' ' . $vehicle->city : '',
                            (isset($ownersInfo[$vehicle->owner_id]) && isset($ownersInfo[$vehicle->owner_id][15])) ? $ownersInfo[$vehicle->owner_id][15] : '',
                            ($vehicle->user) ? $vehicle->user->name . ' ' . $vehicle->user->surname : '',
                            '',
                            (isset($ownersInfo[$vehicle->owner_id]) && isset($ownersInfo[$vehicle->owner_id][8])) ? $ownersInfo[$vehicle->owner_id][8] : '',
                        ];

                        fputcsv($fh, $data);
                    }
                });
            }else{
                $company->trucks()->chunk(1000, function ($vehicles) use(&$fh,$ownersInfo){
                    $vehicles->load('brand', 'model', 'insurance_company', 'owner', 'user');

                    foreach ($vehicles as $vehicle) {
                        $data = [
                            ($vehicle->brand && $vehicle->brand_id != '0') ? $vehicle->brand->name : '',
                            ($vehicle->model && $vehicle->model_id != '0') ? $vehicle->model->name : '',
                            '',
                            '',
                            '',
                            $vehicle->registration,
                            $vehicle->vin,
                            $vehicle->assistance,
                            '',
                            $vehicle->first_registration,
                            $vehicle->year_production,
                            ($vehicle->insurance_company) ? $vehicle->insurance_company->name : '',
                            ($vehicle->owner) ? $vehicle->owner->name : '',
                            ($vehicle->owner) ? $vehicle->owner->street : '',
                            ($vehicle->owner) ? $vehicle->owner->post . ' ' . $vehicle->city : '',
                            (isset($ownersInfo[$vehicle->owner_id]) && isset($ownersInfo[$vehicle->owner_id][15])) ? $ownersInfo[$vehicle->owner_id][15] : '',
                            ($vehicle->user) ? $vehicle->user->name . ' ' . $vehicle->user->surname : '',
                            '',
                            (isset($ownersInfo[$vehicle->owner_id]) && isset($ownersInfo[$vehicle->owner_id][8])) ? $ownersInfo[$vehicle->owner_id][8] : '',
                        ];

                        fputcsv($fh, $data);
                    }
                });
            }

			fclose($fh);
			exit;
	}

	private function normalizeString ($str = '')
	{
		$str = strip_tags($str);
		$str = preg_replace('/[\r\n\t ]+/', ' ', $str);
		$str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
		$str = strtolower($str);
		$str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
		$str = htmlentities($str, ENT_QUOTES, "utf-8");
		$str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
		$str = str_replace(' ', '-', $str);
		$str = rawurlencode($str);
		$str = str_replace('%', '-', $str);
		return $str;
	}
}
