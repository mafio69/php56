<?php

class GapStoreController extends \BaseController {

    public function addNew()
    {
        if(Input::has('agreements')) {

            $agreements = Input::get('agreements');

            DB::disableQueryLog();
            Session::set('avoid_query_logging', true);

            foreach ($agreements as $agreement_init) {
              $data =
              [
                'status_id'=>1,
                'agreement_number'=>$agreement_init['agreement_number'],
                'group_id'=>$agreement_init['group_id'],
                'pesel'=>$agreement_init['pesel'],
                'regon'=>$agreement_init['regon'],
                'gross_net'=>$agreement_init['gross_net'],
                'type_id'=>$agreement_init['type_id'],
                'contribution'=>$agreement_init['contribution'],
                'time'=>$agreement_init['time'],
                'activation_date'=>$agreement_init['activation_date'],
                'accept_date'=>$agreement_init['accept_date'],
              ];
              $agreement = GapAgreement::create($data);
              $agreement->object()->create($agreement_init['object']);
            }

            Session::set('avoid_query_logging', false);

            Flash::success('Nowe umowy zostały wgrane do systemu.');
        }else
            Flash::message('Nie wykryto nowych umów do wgrania.');

        return Redirect::to('gap/agreements/new');

    }

}
