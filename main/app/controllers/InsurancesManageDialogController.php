<?php

class InsurancesManageDialogController extends \BaseController {

	/*
	 * przenoszenie do archiwum
	 */
    /**
     * InsurancesManageDialogController constructor.
     */
    public function __construct()
    {
        $this->beforeFilter('permitted:wykaz_polis#zarzadzaj', ['only' => ['getWithdraw', 'postMoveToWithdraw', 'getRestore', 'postSetRestore', 'getArchive', 'postMoveToArchive', 'getRefund', 'postStoreRefund']]);
    }

    public function getArchive($agreement_id)
	{
		$agreement = LeasingAgreement::find($agreement_id);

		return View::make('insurances.manage.dialog.archive', compact('agreement'));
	}

	public function postMoveToArchive($agreement_id)
	{
		$agreement = LeasingAgreement::find($agreement_id);
		$agreement->archive = \Carbon\Carbon::now()->toDateTimeString();
		$agreement->save();

		Histories::leasingAgreementHistory($agreement->id, 8);
		Flash::success("Umowa nr <i>".$agreement->nr_contract."</i> została przeniesiona do archiwum.");

		return json_encode(array(
			'code' => 0
		));
	}

	/*
	 * zwrot składki
	 */

	public function getRefund($agreement_id)
	{
		$agreement = LeasingAgreement::find($agreement_id);

		$date_to = $agreement->insurances->last()->date_to;

		if(!is_null($date_to) &&  new DateTime($date_to) > new DateTime(date('Y-m-d')) )
			$refund = $this->postCalculateRefund($agreement_id, date('Y-m-d'));
		else
			$refund = array();

		return View::make('insurances.manage.dialog.refund', compact('agreement', 'refund', 'date_to'));
	}

	public function postCalculateRefund($agreement_id, $date_count_from)
	{
		$agreement = LeasingAgreement::find($agreement_id);
		$lastInsurance = $agreement->insurances->last();

		if(is_null($lastInsurance->date_to))
			return ['error' => 'uzupełnij datę zakończenia ostatniej polisy'];

		$endInsuranceDate = Date::createFromFormat('Y-m-d', $lastInsurance->date_to);
		$daysToRefund = Date::createFromFormat('Y-m-d', $date_count_from)->diffInDays($endInsuranceDate);

		if(is_null($lastInsurance->date_from))
			return ['error' => 'uzupełnij datę rozpoczęcia ostatniej polisy'];

		$insuranceDays = Date::createFromFormat('Y-m-d', $lastInsurance->date_from)->diffInDays($endInsuranceDate);

		if(is_null($lastInsurance->contribution))
			return ['error' => 'uzupełnij składkę ostatniej polisy'];

		$date_to = $agreement->insurances->last()->date_to;
		if(!is_null($date_to) &&  new DateTime($date_to) >= new DateTime($date_count_from) )
		{
			$contribution = $lastInsurance->contribution;
			$contributionPerDay = ($contribution/$insuranceDays);
			$valueToRefund = $contributionPerDay*$daysToRefund;
		}
		else
			return ['error' => 'data zwrotu składki musi być przed datą końca polisy'];

		return [ 'value' => number_format((float)$valueToRefund, 2, '.', '')];
	}

	public function postStoreRefund($agreement_id)
	{
		$agreement = LeasingAgreement::find($agreement_id);
		$last_insurance = $agreement->insurances->last();

		$last_insurance->active = 0;
		$last_insurance->save();

		$new_insurance = $last_insurance;
		$new_insurance->active = 1;
		$new_insurance->date_from = Input::get('date_to');
		$new_insurance->refund = Input::get('refund');
		$new_insurance->if_refund_contribution = 1;
		$new_insurance->user_id = Auth::user()->id;

		$new_insurance->notification_number = Auth::user()->insurances_global_nr;
		$new_insurance->refunded_insurance_id = $last_insurance->id;

		LeasingAgreementInsurance::create($new_insurance->toArray());

		$agreement->archive = \Carbon\Carbon::now()->toDateTimeString();
		$agreement->save();

		Histories::leasingAgreementHistory($agreement_id, 10);

		Flash::success("Wykonano zwrot składki do umowy nr ".$agreement->nr_contract);

		return json_encode(array(
			'code' => 0
		));
	}

	public function getWithdraw($agreement_id)
	{
        $agreement = LeasingAgreement::find($agreement_id);

        return View::make('insurances.manage.dialog.withdraw', compact('agreement'));
	}

    public function postMoveToWithdraw($agreement_id)
    {
        $agreement = LeasingAgreement::find($agreement_id);
        $agreement->withdraw = Date::now();
        $agreement->update(Input::all());
        $agreement->save();

        Histories::leasingAgreementHistory($agreement_id, 16);

        Flash::success('Umowę '.$agreement->nr_contract.' wycofano pomyślnie.');

        return json_encode(array(
            'code' => 0
        ));

    }

	public function getChangeOwner($agreement_id)
	{
		$agreement = LeasingAgreement::find($agreement_id);

		//$owners = Owners::where('active', 0)->orderBy('name')->lists('name', 'id');
		$ownersDb = Owners::where('active', 0)->orderBy('name')->get();
		$owners = [];
		foreach($ownersDb as $owner)
        {
            $owners[$owner->id] = ($owner->old_name) ? $owner->name.' ('.$owner->old_name.')' : $owner->name;
        }
		$owners[0] = '---wybierz właściciela---';
		ksort($owners);

		return View::make('insurances.manage.dialog.change-owner', compact('agreement', 'owners'));
	}

	public function getRestore($agreement_id)
	{
		$agreement = LeasingAgreement::find($agreement_id);
		return View::make('insurances.manage.dialog.restore', compact('agreement'));
	}

	public function postSetRestore($agreement_id)
	{
		$agreement = LeasingAgreement::find($agreement_id);
		$agreement->archive = null;
		$agreement->save();

		Histories::leasingAgreementHistory($agreement_id, 24, null, Input::get('restore_reason'));
		Flash::success("Wykonano wycofanie umowy nr ".$agreement->nr_contract);

		return json_encode(array(
				'code' => 0
		));
	}
}
