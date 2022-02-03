<?php


class CompanyAccountNumbersController extends BaseController
{
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:serwisy#warsztaty#zarzadzaj', ['only' => ['getEdit', 'postUpdate', 'getDelete', 'postDelete']]);
    }

    public function getCreate($company_id)
    {
        $company = Company::findOrFail($company_id);
        return View::make('companies.dialog.account-number-create', compact('company'));
    }

    public function postCreate($company_id)
    {
        $number = new CompanyAccountNumbers();
        $number->account_number = Input::get('account_number');
        $number->company_id = $company_id;
        $number->if_user_insert = true;
        $number->save();

        return json_encode(['code' => '0']);
    }


    public function getDelete($id)
    {
        $number = CompanyAccountNumbers::findOrFail($id);
        return View::make('companies.dialog.account-number-delete', compact('number'));
    }

    public function postDelete($id)
    {
        $number = CompanyAccountNumbers::findOrFail($id);
        $number->delete();

        return json_encode(['code' => '0']);
    }
}