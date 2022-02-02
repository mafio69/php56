<?php

class InsurancesInfoClientController extends \BaseController {


	public function __construct()
	{
        $this->beforeFilter('permitted:kartoteka_polisy#zarzadzaj');
	}

	public function getEdit($agreement_id)
	{
		$agreement = LeasingAgreement::find($agreement_id);

		$client = $agreement->client;

		return View::make('insurances.manage.card_file.manage.edit.client', compact('client', 'agreement_id'));
	}

	public function postUpdate($agreement_id)
	{
		$agreement = LeasingAgreement::find($agreement_id);
		$client = $agreement->client;

		$input = Input::all();
		$input['NIP'] = trim(str_replace('-', '', $input['NIP']));

		$matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
		if(Input::has('registry_post') && strlen(Input::has('registry_post')) == 6)
		{
			$registry_post = $input['registry_post'];
			$voivodeship_id = $matcher->match($registry_post);
			$input['registry_voivodeship_id'] = $voivodeship_id;
		}
		if(Input::has('correspond_post') && strlen(Input::has('correspond_post')) == 6)
		{
			$correspond_post = $input['correspond_post'];
			$voivodeship_id = $matcher->match($correspond_post);
			$input['correspond_voivodeship_id'] = $voivodeship_id;
		}

		/*
		$foundedClient = Clients::where('NIP', '=', $input['NIP'])->get();
		if(! $foundedClient->isEmpty() && $input['NIP'] != $client->NIP)
		{
			Flash::warning('Istnieje już w systemie leasingobiorca o wskazanym numerze NIP. W razie problemów skontaktuj się z administratorem.');
			return Redirect::back()->withInput();
		}
		*/

		$input['parent_id'] = $client->id;
		$newClient = Clients::create($input);

		$historyType = 12;
		$history_id = Histories::leasingAgreementHistory($agreement_id, $historyType);

		new \Idea\Logging\LeasingAgreements\Logger($historyType,
			[
				'client' => [
					'previous' => $client->toArray(),
					'current' => $newClient->toArray()
				]
			], $history_id, $agreement_id);

		$agreement->client_id = $newClient->id;
		$agreement->save();


		Flash::message('Dane leasingobiorcy zostały zaktualizowane pomyślnie.');
		return Redirect::to(url('insurances/info/show', [$agreement_id]));

	}

	public function checkClientNIP(){
		\Debugbar::disable();
		$nip = trim(str_replace('-', '', Input::get('NIP')));
		$client = Clients::where('NIP', '=', $nip)->get();
		if($client->isEmpty())
			return '0';
		return '1';
	}

	public function postStoreClient(){
		$input = Input::all();
		$input['NIP'] = trim(str_replace('-', '', $input['NIP']));

		$matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
		if(Input::has('registry_post') && strlen(Input::has('registry_post')) == 6)
		{
			$registry_post = $input['registry_post'];
			$voivodeship_id = $matcher->match($registry_post);
			$input['registry_voivodeship_id'] = $voivodeship_id;
		}
		if(Input::has('correspond_post') && strlen(Input::has('correspond_post')) == 6)
		{
			$correspond_post = $input['correspond_post'];
			$voivodeship_id = $matcher->match($correspond_post);
			$input['correspond_voivodeship_id'] = $voivodeship_id;
		}

		$client = Clients::create($input);

		if($client){
			$result['status'] = 'success';
			$result['client'] = $client->toArray();
		}else{
			$result['status'] = 'error';
			$result['msg'] = 'Wystąpił błąd w trakcie dodawania klienta. Skontaktuj się z administratorem.';
		}
		return json_encode($result);
	}


}
