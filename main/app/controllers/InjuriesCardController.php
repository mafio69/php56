<?php

/**
 * Class InjuriesCardController
 * Zarządzanie kartoteką szkody
 */
class InjuriesCardController extends BaseController {


    public function __construct(){
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_opis_szkody', ['only' => ['editRemarks', 'updateRemarks']]);
        $this->beforeFilter('permitted:kartoteka_szkody#zarejestruj_w_sap', ['only' => ['postRegisterSap']]);
        $this->beforeFilter('permitted:kartoteka_szkody#sprzedaz_wraku#zarzadzaj', ['only' => ['setAlert_repurchase_confirm', 'setAlert_expire_tenderer_confirm', 'setIf_tenderer_confirm', 'setAlert_buyer_confirm', 'setPro_forma_request_confirm', 'setPayment_confirm', 'setInvoice_request_confirm', 'setCassation_receipt_confirm', 'setOff_register_vehicle_confirm']]);
    }

    public function setAlert($name, $id, $model, $desc)
    {
        $wreck = $model::find($id);
        $input = Input::all();
        $rules = array(
            'alert' => 'date',
        );

        $validation = Validator::make($input, $rules);

        if ($validation->fails())
        {
            return Response::json(array('alert' => 'response-alert-danger', 'message' => 'Niepoprawny format daty.'));
        }

        $wreck->$name = Input::get('alert');
        $col_user = $name.'_user_id';
        $wreck->$col_user = Auth::user()->id;
        $wreck->save();

        $result = array();
        $result['alert'] = 'response-alert-info';
        $result['message'] = 'Data parametru została zmieniona.';

        Histories::history($wreck->injury->id, 143, Auth::user()->id, $desc.' - '.Input::get('alert') );

        return json_encode($result);
    }

    public function setAlert_repurchase_confirm($id)
    {
        $wreck = InjuryWreck::find($id);

        if($wreck->alert_repurchase_confirm=='0000-00-00'){
          $wreck->alert_repurchase_confirm = date('Y-m-d');
          $wreck->save();

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Deklaracja została zaakceptowana.';
          $result['label']    = 'alert_repurchase_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->alert_repurchase_confirm;
          $result['to_show'] = array('wreck_data');
          $result['to_disable'][] = 'alert_repurchase';
          $result['to_enable'][] = 'label_check_alert_repurchase';

          Histories::history($wreck->injury->id, 144, Auth::user()->id, 'napłynięcie deklaracji.' );
        }
        else{
          $wreck->alert_repurchase_confirm = "0000-00-00";
          $wreck->save();

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Deklaracja została odakceptowana.';
          $result['label']    = 'alert_repurchase_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->alert_repurchase_confirm;
          $result['to_hide'] = array('wreck_data');
          $result['to_enable'][] = 'label_check_alert_repurchase';
          $result['to_enable'][] = 'alert_repurchase';
          $result['non_active'] = 'label_check_alert_repurchase';

          Histories::history($wreck->injury->id, 149, Auth::user()->id, 'napłynięcie deklaracji.' );
        }

        return json_encode($result);
    }

    public function setAlert_expire_tenderer_confirm($id)
    {
        $wreck = InjuryWreck::find($id);

        $wreck->expire_tenderer_confirm = date('Y-m-d');
        $wreck->save();

        $result = array();
        $result['alert']    = 'response-alert-info';
        $result['message']  = 'Oferta została zaakceptowana.';


        return json_encode($result);
    }

    public function setIf_tenderer_confirm($id)
    {
        $wreck = InjuryWreck::find($id);
        $result = array();
        $result['alert']    = 'response-alert-info';

        if($wreck->if_tenderer) {
            $wreck->if_tenderer = null;
            $result['message']  = 'Oferent został cofnęty.';
            $result['active'] = 1;
            Histories::history($wreck->injury->id, 149, Auth::user()->id, 'oferent.' );
        }else{
            $wreck->if_tenderer = date('Y-m-d');
            $result['message']  = 'Oferent został potwierdzony.';
            $result['active'] = 0;
            Histories::history($wreck->injury->id, 144, Auth::user()->id, 'oferent.' );
        }
        $wreck->save();

        $result['enable'] = 1;

        return json_encode($result);
    }

    public function setValue($id, $element, $model, $desc, $desc_array = null)
    {
        $result = array();
        $object = $model::find($id);

        if(Input::has('validation') && Input::get('validation') != '' && Input::get('validation') != 'undefined') {
            $rules = array(
                'val' => Input::get('validation'),
            );

            $validation = Validator::make(Input::all(), $rules);

            if ($validation->fails()) {
                $result['appendShowAndHideElement'] = '<span class="form-control-feedback custom-feedback right-col-space-25"><i class="fa fa-times"></i> proszę podać prawidłową wartość</span>';
                $result['appendClassIdElement'] = $element.'-group';
                $result['appendClass'] = 'has-error';
                return json_encode($result);
            }
        }

        $object->$element = Input::get('val');
        $object->save();




        $result['appendShowAndHideElement'] = '<span class="form-control-feedback custom-feedback right-col-space-'.Input::get('right_space').'"><i class="fa fa-check"></i> zapisano zmiany</span>';
        $result['appendClassIdElement'] = $element.'-group';
        $result['appendClass'] = 'has-success';

        if(is_null($desc_array)) {
            $desc_value = Input::get('val');
        }else{
            $desc_value = Config::get('definition')[$desc_array][Input::get('val')];
        }

        Histories::history($object->injury->id, 145, Auth::user()->id, $desc.' - '.$desc_value );

        switch ($element) {
            case 'buyer':
                if ($object->$element != 0) {
                    $result['to_show'][] = 'buyer_panel';
                    if ($object->$element == 1) {
                        $result['to_hide'][] = 'alert_buyer_confirm-group';
                        $result['to_disable'][] = 'doc_16';
                        $result['to_show'][] = 'pro_forma_container';
                        $result['to_show'][] = 'invoice_panel';
                        $result['to_set_val']['repurchase_price'] = $object->value_repurchase;
                        $result['to_set_val']['balance_repurchase_price'] = $object->value_repurchase.' zł';
                        $object->repurchase_price = $object->value_repurchase;
                        $object->save();

                        $result['to_hide'][] = 'buyer-search-container';
                        $result['to_set_val']['buyer_id'] = $this->getBuyerFromInjuryClient($object);
                        $result['to_change_trigger'][] = 'buyer_id';
                    } else if ($object->$element == 2 || $object->$element == 7) {
                        $result['to_show'][] = 'alert_buyer_confirm-group';
                        $result['to_enable'][] = 'doc_16';
                        if($object->alert_buyer_confirm!='0000-00-00'){
                          $result['to_show'][] = 'pro_forma_container';
                          $result['to_show'][] = 'invoice_panel';
                        }
                        else{
                          $result['to_hide'][] = 'pro_forma_container';
                          $result['to_hide'][] = 'invoice_panel';
                        }
                        $result['to_clear'][] = 'buyer-info-container';
                        $result['to_set_val']['repurchase_price'] = $object->value_tenderer;
                        $result['to_set_val']['balance_repurchase_price'] = $object->value_tenderer.' zł';
                        $object->repurchase_price = $object->value_tenderer;
                        $object->buyer_id = null;
                        $object->save();

                        $result['to_show'][] = 'buyer-search-container';
	                    $result['to_set_val']['buyer_id'] = null;
	                    $result['to_change_trigger'][] = 'buyer_id';
                    } else if($object->$element == 3){
                        $result['to_hide'][] = 'alert_buyer_confirm-group';
                        $result['to_disable'][] = 'doc_16';
                        $result['to_show'][] = 'pro_forma_container';
                        $result['to_show'][] = 'invoice_panel';
                        $result['to_set_val']['repurchase_price'] = '0.00';

                        $result['to_show'][] = 'buyer-search-container';
	                    $result['to_set_val']['buyer_id'] = null;
	                    $result['to_change_trigger'][] = 'buyer_id';
	                    $result['to_clear'][] = 'buyer-info-container';
	                    $object->buyer_id = null;
	                    $object->save();
                    } else {
                        $result['to_hide'][] = 'buyer_panel';
                        $result['to_hide'][] = 'pro_forma_container';
                        $result['to_hide'][] = 'invoice_panel';
	                    $result['to_set_val']['buyer_id'] = null;
	                    $result['to_change_trigger'][] = 'buyer_id';
	                    $result['to_clear'][] = 'buyer-info-container';
	                    $object->buyer_id = null;
	                    $object->save();
                    }
                } else {
                    $result['to_hide'][] = 'buyer_panel';
                    $result['to_hide'][] = 'pro_forma_container';
                    $result['to_hide'][] = 'invoice_panel';
	                $result['to_set_val']['buyer_id'] = null;
	                $result['to_change_trigger'][] = 'buyer_id';
	                $result['to_set_val']['repurchase_price'] = 0;
	                $result['to_set_val']['balance_repurchase_price'] = 0;
	                $result['to_clear'][] = 'buyer-info-container';
	                $object->repurchase_price = 0;
	                $object->buyer_id = null;
	                $object->save();
                }

                if($object->injury->total_status_id != 3 && $object->injury->total_status_id != 4){
                    if($object->$element == 3) {
                        $object->injury->total_status_id = 5;
                        InjuryStatusesHistory::create([
                            'injury_id' => $object->injury->id,
                            'user_id'   => Auth::user()->id,
                            'status_id' => 5,
                            'status_type' => 'InjuryTotalStatuses'
                        ]);
                    }else {
                        $object->injury->total_status_id = 3;
                        InjuryStatusesHistory::create([
                            'injury_id' => $object->injury->id,
                            'user_id'   => Auth::user()->id,
                            'status_id' => 3,
                            'status_type' => 'InjuryTotalStatuses'
                        ]);
                    }
                    $object->injury->save();
                }

                break;
            case 'value_undamaged':
                $result['to_set_val']['balance_value_undamaged'] = $object->$element;
                break;
            case 'repurchase_price':
                $result['to_set_val']['balance_repurchase_price'] = $object->$element;
                break;
        }
        // jeśli z selecta zostanie wybrany "oferent aukcyjny"
        if($element == 'buyer' && $object->$element == 2){
            if($object->alert_buyer == '0000-00-00'){
                $object->injury->update(['total_status_id' => 3]);
                Histories::history($object->injury->id, 184, Auth::user()->id);
            }
        }elseif($element == 'buyer' && $object->$element == 4){
            $object->injury->update(['total_status_id' => 17]);
            Histories::history($object->injury->id, 188, Auth::user()->id);
        }elseif($element == 'buyer' && $object->$element == 5){
            $object->injury->update(['total_status_id' => 18]);
            Histories::history($object->injury->id, 189, Auth::user()->id);
        }elseif($element == 'buyer' && $object->$element == 6){
            $object->injury->update(['total_status_id' => 19]);
            Histories::history($object->injury->id, 190, Auth::user()->id);
        }
        return json_encode($result);
    }


    public function setAlert_buyer_confirm($id)
    {
        $wreck = InjuryWreck::find($id);
        if($wreck->alert_buyer_confirm!='0000-00-00'){
          $wreck->alert_buyer_confirm = '0000-00-00';
        //  $wreck->buyer=0;
          $wreck->save();

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Potwierdzenia odkupu zostało anulowane.';
          $result['to_hide'][] = 'invoice_panel';
          $result['to_enable'][] = 'alert_buyer';
          $result['to_enable'][] = 'label_check_alert_buyer';
          $result['to_enable'][] = 'label_check_alert_repurchase';
          $result['non_active'][] = 'label_check_alert_buyer';

            if($wreck && $wreck->buyer != 1  && (
                    $wreck->pro_forma_request == '0000-00-00'
                    ||
                    (in_array(Auth::user()->login, ['przem_k', 'justynan']) && $wreck->invoice_request == '0000-00-00' && $wreck->alert_buyer_confirm == '0000-00-00')
                )
            ) {
                $result['to_show'][] = 'buyer-search-container';
            }

          Histories::history($wreck->injury->id, 149, Auth::user()->id, 'napłynięcie potwierdzenia odkupu.' );

          $wreck->injury->update(['total_status_id' => 3]);
          Histories::history($wreck->injury->id, 184, Auth::user()->id);
        }
        else{
          $wreck->alert_buyer_confirm = date('Y-m-d');
          $wreck->save();

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Potwierdzenia odkupu zostało zaakceptowane.';
          $result['label']    = 'alert_buyer_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->alert_buyer_confirm;
          $result['to_show'][] = 'invoice_panel';
          $result['to_disable'][] = 'alert_buyer';
          $result['to_enable'][] = 'label_check_alert_buyer';
          $result['to_show'][] = 'pro_forma_container';
          $result['to_hide'][] = 'buyer-search-container';

          Histories::history($wreck->injury->id, 144, Auth::user()->id, 'napłynięcie potwierdzenia odkupu.' );

          $wreck->injury->update(['total_status_id' => 5]);
          Histories::history($wreck->injury->id, 185, Auth::user()->id);
        }



        return json_encode($result);
    }

    public function getProFormaRequest($id)
    {
        $wreck = InjuryWreck::find( $id );

        return View::make('injuries.card_file.selling_wreck.dialogs.pro_forma_request', compact('wreck'));
    }

    public function postProFormaRequest($id)
    {
        $wreck = InjuryWreck::find($id);
        $vehicle = $wreck->injury()->first()->vehicle;

        $input = Input::all();

        $rules = array(
            'alert' => 'date',
        );

        $validation = Validator::make($input, $rules);

        if ($validation->fails())
        {
            return 'Niepoprawny format daty.';
        }

        $data = array(
            'wreck' => $wreck,
            'vehicle' => $vehicle,
	        'logo' => public_path() . '/assets/css/images/idea-getin-logo.png'
        );

        Mail::send('injuries.card_file.selling_wreck.mail_templates.pro_forma_request', $data, function($message) {
            $subject = '[IdeaLeasing] Prośba o wystawienie faktury pro forma';

            $message->subject($subject);
            foreach ( Config::get('definition.accountancy_email') as $to) {
                $message->to($to['address'], $to['name']);
            }
        });

        $wreck->pro_forma_request = Input::get('alert');
        $wreck->payment = Date('Y-m-d', strtotime("+7 days"));
        $wreck->save();

        $wreck->injury->total_status_id = 5;
        $wreck->injury->save();

        InjuryStatusesHistory::create([
            'injury_id' => $wreck->injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 5,
            'status_type' => 'InjuryTotalStatuses'
        ]);

        Histories::history($wreck->injury->id, 147, Auth::user()->id );

        $result = 0;
        return json_encode($result);

    }

    public function setPro_forma_request_confirm($id)
    {
        $wreck = InjuryWreck::find($id);

        if($wreck->pro_forma_request_confirm!='0000-00-00'){
          $wreck->pro_forma_request_confirm = '0000-00-00';
          $wreck->payment_confirm ='0000-00-00';
          $wreck->save();

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Wystawienia faktury pro forma zostało anulowane.';
          $result['label']    = 'pro_forma_request_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->pro_forma_request_confirm;
          $result['to_hide'] = array('payment_confirm-group', 'pro_forma_info-group','invoice_request-group');
          $result['to_enable'][] = 'pro_forma_request';
          $result['to_enable'][] = 'label_check_pro_forma_request';
          $result['non_active'][] = 'label_check_pro_forma_request';
          $result['to_enable'][] = 'payment' ;
          $result['to_enable'][] = 'label_check_payment' ;
          $result['non_active'][] = 'label_check_payment' ;

          Histories::history($wreck->injury->id, 149, Auth::user()->id, 'wystawienia faktury pro.' );
        }
        else{
          $wreck->pro_forma_request_confirm = date('Y-m-d');


          $wreck->save();

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Wystawienia faktury pro forma zostało potwierdzone.';
          $result['label']    = 'pro_forma_request_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->pro_forma_request_confirm;
          $result['to_show'] = array('payment_confirm-group', 'pro_forma_info-group');
          $result['to_enable'][] = 'label_check_pro_forma_request';
          $result['to_disable'][] = 'pro_forma_request';

          Histories::history($wreck->injury->id, 144, Auth::user()->id, 'wystawienia faktury pro.' );
        }

        return json_encode($result);
    }

    public function setPayment_confirm($id)
    {
        $wreck = InjuryWreck::find($id);

        if($wreck->payment_confirm!='0000-00-00'){
          $wreck->payment_confirm ='0000-00-00';

          $wreck->save();

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Płatność za fakturę została anulowana.';
          $result['label']    = 'payment_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->payment_confirm;
          $result['to_hide'] = array('invoice_request-group');
          $result['to_enable'] = ['payment'] ;
          $result['to_enable'] = ['label_check_payment'] ;
          $result['non_active'] = ['label_check_payment'] ;

          Histories::history($wreck->injury->id, 144, Auth::user()->id, 'płatności za fakturę.' );
        }
        else{
          $wreck->payment_confirm = date('Y-m-d');

          $wreck->save();

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Płatność za fakturę została potwierdzona.';
          $result['label']    = 'payment_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->payment_confirm;
          $result['to_show'] = array('invoice_request-group');
          $result['to_disable'] = ['payment'] ;
          $result['to_enable'] = ['label_check_payment'] ;

          Histories::history($wreck->injury->id, 144, Auth::user()->id, 'płatności za fakturę.' );
        }


        return json_encode($result);
    }

    public function getInvoiceRequest($id)
    {
        $wreck = InjuryWreck::find( $id );

        return View::make('injuries.card_file.selling_wreck.dialogs.invoice_request', compact('wreck'));
    }

    public function postInvoiceRequest($id)
    {
        $wreck = InjuryWreck::find($id);
        $vehicle = $wreck->injury()->first()->vehicle;

        $input = Input::all();
        $rules = array(
            'alert' => 'date',
        );

        $validation = Validator::make($input, $rules);

        if ($validation->fails())
        {
            return 'Niepoprawny format daty.';
        }

        $data = array(
            'wreck' => $wreck,
            'vehicle' => $vehicle,
	        'logo' => public_path() . '/assets/css/images/idea-getin-logo.png'
        );

        Mail::send('injuries.card_file.selling_wreck.mail_templates.invoice_request', $data, function($message) {
            $subject = '[IdeaLeasing] Prośba o wystawienie faktury';

            $message->subject($subject);
            foreach ( Config::get('definition.accountancy_email') as $to) {
                $message->to($to['address'], $to['name']);
            }
        });

        $wreck->invoice_request = Input::get('alert');
        $wreck->save();

        Histories::history($wreck->injury->id, 148, Auth::user()->id );

        $result = 0;
        return json_encode($result);

    }

    public function setInvoice_request_confirm($id)
    {
        $wreck = InjuryWreck::find($id);

        if( $wreck->invoice_request_confirm!='0000-00-00'){
          $wreck->invoice_request_confirm = '0000-00-00';

          $wreck->save();

          $item_history=$wreck->injury->totalStatusesHistory()->where('status_id',6)->orderBy('created_at','desc')->first()->pivot->created_at;
          $item_history_last=$wreck->injury->totalStatusesHistory()->where('status_id','!=',6)->where('injury_statuses_history.created_at','<',$item_history->format('Y-m-d H:i:s'))->orderBy('created_at','desc')->first()->id;

          $wreck->injury->total_status_id = $item_history_last;
          $wreck->injury->save();

          $wreck->injury->update(['total_status_id' => 5]);
          Histories::history($wreck->injury->id, 185, Auth::user()->id);


          InjuryStatusesHistory::create([
              'injury_id' => $wreck->injury->id,
              'user_id'   => Auth::user()->id,
              'status_id' => $item_history_last,
              'status_type' => 'InjuryTotalStatuses'
          ]);

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Anulowano potwierdzenie dostarczenia faktury.';
          $result['label']    = 'invoice_request_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->invoice_request_confirm;
          $result['to_hide'] = array('wreck_success-group');
          $result['to_enable'][] = 'invoice_request';
          $result['to_enable'][] = 'label_check_invoice_request';
          $result['non_active'][] = 'label_check_invoice_request';

          Histories::history($wreck->injury->id, 149, Auth::user()->id, 'dostarczenia faktury.' );
        }
        else{
          $wreck->invoice_request_confirm = date('Y-m-d');

          $wreck->save();

          $wreck->injury->total_status_id = 6;
          $wreck->injury->save();

          $wreck->injury->update(['total_status_id' => 6]);
          Histories::history($wreck->injury->id, 186, Auth::user()->id);

          InjuryStatusesHistory::create([
              'injury_id' => $wreck->injury->id,
              'user_id'   => Auth::user()->id,
              'status_id' => 6,
              'status_type' => 'InjuryTotalStatuses'
          ]);

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Potwierdzono dostarczenia faktury.';
          $result['label']    = 'invoice_request_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->invoice_request_confirm;
          $result['to_show'] = array('wreck_success-group');
          $result['to_disable'][] = 'invoice_request';
          $result['to_enable'][] = 'label_check_invoice_request';

          Histories::history($wreck->injury->id, 144, Auth::user()->id, 'dostarczenia faktury.' );
        }

        return json_encode($result);
    }

    public function setNotApplicable($id)
    {
        $wreck = InjuryWreck::find($id);

        if($wreck->not_applicable){
          $wreck->not_applicable = null;  $wreck->buyer=0;
          $wreck->buyer=0;
          $wreck->save();

          $result = array();
          $result['to_enable'][] = 'label_check_not_applicable';
          $result['non_active'][] = 'label_check_not_applicable';

          if($wreck->alert_repurchase_confirm=='0000-00-00'){
            $result['to_hide'][] = 'wreck_data';
            $result['to_hide'][] = 'buyer_panel';
            $result['to_hide'][] = 'invoice_panel';
            if($wreck->alert_repurchase=='0000-00-00'){
              $result['to_disable'][] = 'label_check_alert_repurchase';
              $result['to_enable'][] = 'alert_repurchase';
            }
            else{
              $result['to_enable'][] = 'label_check_alert_repurchase';
            }
          }
          else{
            $result['to_enable'][] = 'label_check_alert_repurchase';
          }

          Histories::history($wreck->injury->id, 149, Auth::user()->id, 'procedowanie sprzedży wraku bez deklaracji LB.' );
        }
        else{
          $wreck->not_applicable = date('Y-m-d');
          $wreck->save();

          $result = array();
          $result['to_show'][] = 'wreck_data';
          $result['to_enable'][] = 'label_check_not_applicable';
          if($wreck->buyer != '0'){
              $result['to_show'][] = 'buyer_panel';
              $result['to_show'][] = 'invoice_panel';
          }

          Histories::history($wreck->injury->id, 144, Auth::user()->id, 'procedowanie sprzedży wraku bez deklaracji LB.' );
        }

        return json_encode($result);
    }

    public function setScrapped($id)
    {
        $wreck = InjuryWreck::find($id);

        if($wreck->scrapped){
          $wreck->scrapped = null;
          $wreck->cassation_receipt = null;
          $wreck->cassation_receipt_confirm = null;
          $wreck->off_register_vehicle = null;
          $wreck->off_register_vehicle_confirm = null;
          $wreck->save();

          $item_history=$wreck->injury->totalStatusesHistory()->where('status_id',12)->orderBy('created_at','desc')->first()->pivot->created_at;
          $item_history_last=$wreck->injury->totalStatusesHistory()->where('status_id','!=',12)->where('injury_statuses_history.created_at','<',$item_history->format('Y-m-d H:i:s'))->orderBy('created_at','desc')->first()->id;

          $wreck->injury->total_status_id = $item_history_last;
          $wreck->injury->save();

          Histories::history($wreck->injury->id, 149, Auth::user()->id, 'zezłomowanie pojazdu.' );

          InjuryStatusesHistory::create([
              'injury_id' => $wreck->injury->id,
              'user_id'   => Auth::user()->id,
              'status_id' => $item_history_last,
              'status_type' => 'InjuryTotalStatuses'
          ]);

          $result = array();
          $result['to_enable'][] = 'label_check_scrapped';
          $result['to_enable'][] = 'label_check_not_applicable';
          $result['to_hide'][] = 'scrapped_panel';
          $result['non_active'][] = 'label_check_scrapped';

          $result['non_active'][] = 'label_check_cassation_receipt';
          $result['to_enable'][] = 'label_check_off_register_vehicle';
          $result['to_enable'][] = 'off_register_vehicle';
          $result['to_enable'][] = 'cassation_receipt';
          $result['non_active'][] = 'label_check_off_register_vehicle';
          $result['to_hide'][] = 'scrapped-group';
          $result['to_hide'][] = 'off_register_vehicle_confirm-group';

          if($wreck->alert_repurchase_confirm != '0000-00-00'||$wreck->not_applicable){
            $result['to_show'][] = 'wreck_data';
            if($wreck->buyer != '0'){
                $result['to_show'][] = 'buyer_panel';
                $result['to_show'][] = 'invoice_panel';
            }
            else{
                $result['to_hide'][] = 'buyer_panel';
                $result['to_hide'][] = 'invoice_panel';
            }
          }
          //????
          $result['to_hide'][] = 'new_offer';

        }
        else{
          $wreck->scrapped = date('Y-m-d');
          $wreck->cassation_receipt = date('Y-m-d');
          $wreck->save();

          $wreck->injury->total_status_id = 12;
          $wreck->injury->save();

          InjuryStatusesHistory::create([
              'injury_id' => $wreck->injury->id,
              'user_id'   => Auth::user()->id,
              'status_id' => 12,
              'status_type' => 'InjuryTotalStatuses'
          ]);

          Histories::history($wreck->injury->id, 144, Auth::user()->id, 'zezłomowanie pojazdu.' );

          $result = array();
          $result['to_show'][] = 'scrapped_panel';
          $result['to_enable'][] = 'label_check_scrapped';
          $result['to_disable'][] = 'label_check_not_applicable';
          $result['to_hide'][] = 'wreck_data';
          $result['to_hide'][] = 'buyer_panel';
          $result['to_hide'][] = 'invoice_panel';
          $result['to_hide'][] = 'new_offer';


        }

        return json_encode($result);
    }

    public function setCassation_receipt_confirm($id)
    {
        $wreck = InjuryWreck::find($id);
        if($wreck->cassation_receipt_confirm){
          $wreck->cassation_receipt_confirm = null;
          $wreck->off_register_vehicle = null;
          $wreck->off_register_vehicle_confirm = null;
          $wreck->save();

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Otrzymanie kwitu kasacji zostało cofnięte.';
          $result['label']    = 'cassation_receipt_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->cassation_receipt_confirm;
          $result['to_hide'] = array('off_register_vehicle_confirm-group','scrapped-group');
          $result['to_enable'][] = 'label_check_cassation_receipt';
          $result['to_enable'][] = 'cassation_receipt';
          $result['non_active'][] = 'label_check_cassation_receipt';
          $result['to_enable'][] = 'label_check_off_register_vehicle';
          $result['to_enable'][] = 'off_register_vehicle';
          $result['non_active'][] = 'label_check_off_register_vehicle';


          Histories::history($wreck->injury->id, 144, Auth::user()->id, ' kwit kasacji.' );
        }
        else{
          $wreck->cassation_receipt_confirm = date('Y-m-d');
          $wreck->off_register_vehicle = date('Y-m-d');
          $wreck->save();

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Otrzymanie kwitu kasacji zostało potwierdzone.';
          $result['label']    = 'cassation_receipt_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->cassation_receipt_confirm;
          $result['to_show'] = array('off_register_vehicle_confirm-group');
          $result['to_disable'][] = 'cassation_receipt';
          $result['to_enable'][] = 'label_check_cassation_receipt';

          Histories::history($wreck->injury->id, 144, Auth::user()->id, ' kwit kasacji.' );
        }
        return json_encode($result);
    }

    public function setOff_register_vehicle_confirm($id)
    {
        $wreck = InjuryWreck::find($id);
        if($wreck->off_register_vehicle_confirm){
          $wreck->off_register_vehicle_confirm = null;
          $wreck->save();

          $item_history=$wreck->injury->totalStatusesHistory()->where('status_id',13)->orderBy('created_at','desc')->first()->pivot->created_at;
          $item_history_last=$wreck->injury->totalStatusesHistory()->where('status_id','!=',13)->where('injury_statuses_history.created_at','<',$item_history->format('Y-m-d H:i:s'))->orderBy('created_at','desc')->first()->id;

          $wreck->injury->total_status_id = $item_history_last;
          $wreck->injury->save();

          InjuryStatusesHistory::create([
              'injury_id' => $wreck->injury->id,
              'user_id'   => Auth::user()->id,
              'status_id' => $item_history_last,
              'status_type' => 'InjuryTotalStatuses'
          ]);

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Wyrejestrowanie pojazdu zostało anulowane.';
          $result['to_hide'] = array('scrapped-group');
          $result['non_active'] = 'label_check_off_register_vehicle';
          $result['to_enable'][] = 'label_check_off_register_vehicle';
          $result['to_enable'][] = 'off_register_vehicle';

          Histories::history($wreck->injury->id, 149, Auth::user()->id, ' wyrejestrowanie pojazdu.' );
        }
        else{
          $wreck->off_register_vehicle_confirm = date('Y-m-d');
          $wreck->save();

          $wreck->injury->total_status_id = 13;
          $wreck->injury->save();

          InjuryStatusesHistory::create([
              'injury_id' => $wreck->injury->id,
              'user_id'   => Auth::user()->id,
              'status_id' => 13,
              'status_type' => 'InjuryTotalStatuses'
          ]);

          $result = array();
          $result['alert']    = 'response-alert-info';
          $result['message']  = 'Wyrejestrowanie pojazdu zostało potwierdzone.';
          $result['label']    = 'off_register_vehicle_confirm_label';
          $result['label_content'] = 'data potwierdzenia: '.$wreck->cassation_receipt_confirm;
          $result['to_show'] = array('scrapped-group');
          $result['to_enable'][] = 'label_check_off_register_vehicle';
          $result['to_disable'][] = 'off_register_vehicle';

          Histories::history($wreck->injury->id, 144, Auth::user()->id, ' wyrejestrowanie pojazdu.' );
        }

        return json_encode($result);
    }

    public function wreckCalcBalance($id)
    {
        $result = array();
        $result['status'] = 0;
        $injury = Injury::find($id);
        if( $injury->wreck ) {
            $wreck = $injury->wreck;

            if ($injury->vehicle->gap == 1) {
                $balance = $wreck->value_invoice - $wreck->repurchase_price - $wreck->value_compensation - $wreck->extra_charge_ic - $wreck->value_gap;
                $result['status'] = 1;
            } else {
                $balance = $wreck->value_undamaged - $wreck->repurchase_price - $wreck->value_compensation - $wreck->extra_charge_ic;
                $result['status'] = 1;
            }

            $result['balance'] = $balance.' zł';

            if ($balance == 0)
                $result['label'] = 'label-success';
            else if ($balance > 0)
                $result['label'] = 'label-info';
            else
                $result['label'] = 'label-warning';
        }

        return json_encode($result);
    }

    public function wreckDokTransfer($id)
    {
        $injury = Injury::find($id);

        $injury->wreck->dok_transfer = date('Y-m-d');
        $injury->wreck->save();

        $injury->total_status_id = 7;
        $injury->save();

        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 7,
            'status_type' => 'InjuryTotalStatuses'
        ]);

        Histories::history($id, 146, Auth::user()->id);

        $url = URL::route('injuries-info', array('id'=>$id, '#balance_wreck'));
        return Redirect::to($url);
    }

    public function appendLetter($letter_id, $injury_id)
    {
        $letter = InjuryLetter::find($letter_id);
        $injury = Injury::find($injury_id);

        $file = InjuryFiles::create(array(
            'injury_id' => $injury_id,

            'type'		=> 2,
            'category'	=> $letter->category,
            'document_id'   =>  $letter->category,
            'document_type' =>  'InjuryUploadedDocumentType',

            'user_id'	=> Auth::user()->id,
            'file'		=> $letter->file,
            'name'      => $letter->name
        ));

        Histories::history($injury_id, 158, Auth::id(), 'Kategoria '.$file->document->name.' - <a target="_blank" href="'.URL::route('injuries-downloadDoc', array($file->id)).'">pobierz</a>');

        if($file->document_id == 3 || $file->document_id == 4){
            InjuryInvoices::create(array(
                    'initial_company_vat_check_id' => ($injury->branch && $injury->branch->company->companyVatCheck) ? $injury->branch->company->companyVatCheck->id : null,
                    'injury_id' 		=> $file->injury_id,
                    'injury_files_id'	=> $file->id,
                    'invoicereceives_id'=> $file->injury()->first()->invoicereceives_id,
                    'created_at'		=> $file->created_at,
                    'updated_at'		=> $file->updated_at
                )
            );
        }
        if($file->document_id == 6)
        {
            InjuryCompensation::create(array(
                'injury_id' => $file->injury_id,
                'injury_files_id'	=> $file->id,
                'user_id' => Auth::user()->id
            ));
        }

        $letter->injury_file_id = $file->id;
        $letter->save();

        Flash::success('Przypisano pismo do szkody.');
        return json_encode(['code' => 0]);
    }

    public function editRemarks($injury_id)
    {
        $injury = Injury::with('getRemarks')->find($injury_id);
        return View::make('injuries.dialog.editRemarks', compact('injury'));
    }

    public function updateRemarks($injury_id)
    {
        $injury = Injury::with('getRemarks')->find($injury_id);
        if($injury->getRemarks) {
            $remarks = $injury->getRemarks;
            $remarks->content = Input::get('info');
            $remarks->save();
        }else{
            $content = Text_contents::create(['content' => Input::get('info')]);
            $injury->remarks = $content->id;
            $injury->save();
        }

        Histories::history($injury_id, 159, Auth::id());
        Flash::success('Zaktualizowano opis szkody.');
        return json_encode(['code' => 0]);
    }

    public function searchBuyer()
    {
        $term = Input::get('term');
        $column = Input::get('col_name');

        $buyers = Buyer::where($column, 'like', '%'.$term.'%')->get();
        $result = [];

        foreach ($buyers as $k => $buyer) {
            $result[] = array(
                "id" => $buyer->id,
                "label" => $buyer->name.' - '.$buyer->nip,
                "value" => $buyer->name
            );
        }

        return json_encode($result);
    }

    public function setBuyer()
    {
        $buyer = Buyer::find(Input::get('buyer_id'));
        $wreck = InjuryWreck::find(Input::get('wreck_id'));
        $wreck->buyer_id = Input::get('buyer_id');
        $wreck->save();

        Histories::history($wreck->injury->id, 144, Auth::user()->id, 'zmiana nabywcy na - '.$buyer->name );
        return 'success';
    }

    public function buyerInfo()
    {
        $buyer_id = Input::get('buyer_id');
        $buyer = Buyer::findOrFail($buyer_id);

        return View::make('injuries.card_file.partials.buyer-info', compact('buyer'));
    }

    public function getNewOffer($wreck_id)
    {
        return View::make('injuries.card_file.selling_wreck.dialogs.new-offer', compact('wreck_id'));
    }

    public function postNewOffer($wreck_id)
    {
        $wreck = InjuryWreck::find($wreck_id);
        $wreck->active = 0;
        $wreck->save();

        $wreck->injury->total_status_id = 14;
        $wreck->injury->save();

        $wreck->injury->update(['total_status_id' => 14]);
        Histories::history($wreck->injury->id, 187, Auth::user()->id);

        InjuryStatusesHistory::create([
            'injury_id' => $wreck->injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 14,
            'status_type' => 'InjuryTotalStatuses'
        ]);

        InjuryWreck::create(array(
            'injury_id'  =>  $wreck->injury_id,
            'value_undamaged' => $wreck->value_undamaged,
            'value_undamaged_net_gross' => $wreck->value_undamaged_net_gross,
            'value_undamaged_currency' => $wreck->value_undamaged_currency,
            'nr_auction' => $wreck->nr_auction,
            'value_repurchase' => $wreck->value_repurchase,
            'value_repurchase_net_gross' => $wreck->value_repurchase_net_gross,
            'value_repurchase_currency' => $wreck->value_repurchase_currency,
            'value_compensation' => $wreck->value_compensation,
            'value_compensation_net_gross' => $wreck->value_compensation_net_gross,
            'value_compensation_currency' => $wreck->value_compensation_currency,
            'value_tenderer' => $wreck->value_tenderer,
            'value_tenderer_net_gross' => $wreck->value_tenderer_net_gross,
            'value_tenderer_currency' => $wreck->value_tenderer_currency,
            'expire_tenderer' => $wreck->expire_tenderer,
            'if_tenderer' => $wreck->if_tenderer,
            'alert_repurchase' => $wreck->alert_repurchase,
            'alert_repurchase_confirm' => $wreck->alert_repurchase_confirm,
            'not_applicable' => $wreck->not_applicable
        ));

        Histories::history($wreck->injury->id, 161);
        Flash::success('Rozpoczęto ponowną sprzedaż.');
        return json_encode(['code' => 0]);
    }

    private function getBuyerFromInjuryClient(InjuryWreck $wreck)
    {
        $injury = Injury::find($wreck->injury_id);
        $client = $injury->client;

        $buyer = Buyer::where('name', $client->name)
            ->where(function($query) use ($client){
                if($client->NIP == '')
                    $query->whereNull('nip');
                else
                    $query->where('nip', $client->NIP);

                if($client->REGON == '')
                    $query->whereNull('regon');
                else
                    $query->where('regon', $client->REGON);
            })
            ->first();

        if(! $buyer)
        {
            $buyer = Buyer::create([
                'name' => $client->name,
                'address_street' => $client->registry_street,
                'address_code' => $client->registry_post,
                'address_city' => $client->registry_city,
                'nip' => $client->NIP,
                'regon' => $client->REGON,
                'phone' => $client->phone,
                'email' => $client->email
            ]);
        }

        $wreck->buyer_id = $buyer->id;
        $wreck->save();

        return $buyer->id;
    }

    public function getAssignClient($injury_id)
    {
        $injury = Injury::find($injury_id);
        $vehicle = $injury->vehicle;

        return View::make('injuries.dialog.assign-client', compact('injury', 'vehicle'));
    }

    public function postSearchClient()
    {
        $term = Input::get('term');

        $clients = Clients::distinct()
            ->where(function($query){
                if(Input::has('client_id')) {
                    $client_id = Input::get('client_id');
                    $query->where('id', '!=', $client_id);
                }
            })
            ->where('name', 'like', '%'.$term.'%')
            ->groupBy('name')
            ->get();
        $result = array();

        foreach($clients as $k => $v){
            $result[] = array(
                "id"=>$v->id,
                "label"=> $v->name,
                "value" => $v->name
            );
        }

        return json_encode($result);
    }

    public function postAssignClient($injury_id)
    {
        $injury = Injury::find($injury_id);

        if(Input::has('client_id'))
        {
            $client_id = Input::get('client_id');
            if($client_id == '')
                return json_encode(['msg' => 'Proszę poprawnie wybrać nowego klienta.']);

            $injury->client_id = Input::get('client_id');
            $injury->save();

            Flash::success('Dane klienta zostały zaktualizowane');
            return json_encode(['code' => 0]);
        }

        $inputs = Input::all();
        $inputs['NIP'] = stripNonNumeric($inputs['NIP']);

        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
        if(Input::has('registry_post') && strlen(Input::has('registry_post')) == 6)
        {
            $registry_post = $inputs['registry_post'];
            $voivodeship_id = $matcher->match($registry_post);
            $inputs['registry_voivodeship_id'] = $voivodeship_id;
        }
        if(Input::has('correspond_post') && strlen(Input::has('correspond_post')) == 6)
        {
            $correspond_post = $inputs['correspond_post'];
            $voivodeship_id = $matcher->match($correspond_post);
            $inputs['correspond_voivodeship_id'] = $voivodeship_id;
        }

        $client = Clients::create($inputs);
        $injury->client_id = $client->id;
        $injury->save();

        Flash::success('Dane klienta zostały zaktualizowane');
        return json_encode(['code' => 0]);
    }

    public function postRegisterSap($injury_id)
    {
        $injury = Injury::find($injury_id);
        $sap = new Idea\SapService\Sap();

         try {
            $result = $sap->szkodaUtworzNew($injury);
        } catch (Exception $e) {
            Log::error('register sap', [$e->getMessage(), $e->getCode(), $e->getTraceAsString()]);
            Session::flash('show.modal.in.the.next.request', $e->getMessage());
            return Redirect::back();
        }

        if($result['status'] != 400){
            Histories::history($injury_id, 216, Auth::user()->id);
        }

        if($result['status'] == 200){
            Flash::message('Szkoda zarejestrowana w SAP o id:'.$result['sap_id']);
        }else {
            Session::flash('show.modal.in.the.next.request', $result['msg']);
        }

        return Redirect::back();
    }

    public function checkContractStatus($injury_id)
    {
        $injury = Injury::find($injury_id);
        $vehicle = $injury->vehicle;
        $current_status = $injury->contractStatus ? $injury->contractStatus->name : '';

        $new_contract_status = null;
        if($injury -> vehicle_type == 'VmanageVehicle'){
            if($vehicle->outdated == 1){
                $history = $vehicle->history;
                if($history) {
                    $latest_history = VmanageVehicleHistory::where('history_id', $history->history_id)->orderBy('id', 'desc')->first();
                    if ($latest_history) {
                        $new_contract_status = $latest_history->vehicle->contract_status;
                    }
                }
            }else{
                $new_contract_status = $vehicle->contract_status;
            }
        }elseif($vehicle->register_as == 1 && $vehicle->owner->wsdl != ''){
            $contractList = '<contractList><contract>'.$vehicle->nr_contract.'</contract></contractList>';
            $data = new \Idea\Structures\CHKCONTSTATEInput($contractList);

            $webservice = Webservice::establishSoap($vehicle->owner_id)->generateParameters($data)->callSoap('chkcontstate_XML');
            $xml = $webservice->getResponseXML();

            if ($xml->ANSWER->chkContStateReturn->Error->ErrorCde == 'ERR0000') {
                if(count( (array)$xml->ANSWER->chkContStateReturn->stateList) > 0){
                    $new_contract_status = $xml->ANSWER->chkContStateReturn->stateList->contractState->status->__toString();
                }
            }else{
                Log::warning('wsdl status error', [$xml]);
            }
        } else{
            $syjonService = new \Idea\SyjonService\SyjonService();
            $contract = json_decode($syjonService->loadContract($vehicle->syjon_contract_id))->data;
            if($contract) {
                $new_contract_status = $contract->contract_status;
            }
        }

        if($new_contract_status && $current_status != $new_contract_status){
            $contract_status = ContractStatus::where('name', 'like', $new_contract_status)->first();
            if (!$contract_status) {
                $is_active = 0;
                if (str_contains(mb_strtoupper($new_contract_status), 'AKTYWNA')) {
                    $is_active = 1;
                }
                $contract_status = ContractStatus::create([
                    'name' => $new_contract_status,
                    'is_active' => $is_active
                ]);
            } else {
                $is_active = $contract_status->is_active;
            }

            InjuryContractStatusHistory::create([
                'injury_id' => $injury->id,
                'contract_status_id' => $contract_status->id,
                'vehicle_id' => $vehicle->id,
                'vehicle_type' => $injury->vehicle_type
            ]);

            $injury->update(['contract_status_id' => $contract_status->id]);

            return Response::json([
                'contract_status' => $contract_status->name,
                'is_active' => $is_active,
                'status' => 200
            ]);
        }

        return Response::json(['status' => 204]);
    }

    public function postUpdateSap($injury_id)
    {
        $injury = Injury::find($injury_id);
        $sap = new Idea\SapService\Sap();
        $result = $sap->szkoda($injury);

        if($result['status'] == 200){
            Flash::message('Szkoda zaktualizowana w SAP');
        }else {
            Session::flash('show.modal.in.the.next.request', $result['msg']);
        }

        if(in_array( $result['status'], [200, 300])){
            Histories::history($injury_id, 217, Auth::user()->id);
        }

        return Redirect::back();
    }

    public function postSendToSap($injury_id)
    {
        $injury = Injury::find($injury_id);
        $sap = new Idea\SapService\Sap();

        $notes = [];
        $invoices = [];
        foreach($injury->invoices()->where('active', 0)->whereNull('injury_note_id')->get() as $k => $invoice)
        {
            $notes[$k] = $invoice->invoice_nr." – przekazano do płatności";
            $invoices[$k] = $invoice;
        }
        $result = $sap->szkodaNotUtworz($injury, $notes);
        
        $errors = [];
        if(isset($result['ftReturn']) && is_array($result['ftReturn'])){
            foreach($result['ftReturn'] as $ftReturn){
                if($ftReturn['typ'] =='E'){
                    $errors[] = $ftReturn;
                }
            }
        }

        if(count($errors) > 0){
            Flash::error('Wystąpił błąd w trakcie wysyłki notatek.');
            return Redirect::back();
        }

        if(! isset($result['ftNotatkaN'])){
            Flash::error('Wystąpił błąd w trakcie wysyłki notatek.');
            return Redirect::back();
        }

        foreach($result['ftNotatkaN'] as $note_item => $note){
            $key = str_replace('item', '', $note_item);
            $invoice = $invoices[$key];

            $injuryNote = InjuryNote::create([
                'referenceable_id' => $invoice->id,
                'referenceable_type' => 'InjuryInvoices',
                'injury_id' => $injury_id,
                'user_id' => Auth::user()->id,
                'roknotatki' => $note['roknotatki'],
                'nrnotatki'=> $note['nrnotatki'],
                'obiekt'=> $note['obiekt'],
                'temat'=> $note['temat'],
                'data'=> $note['data'],
                'uzeit'=> $note['uzeit'],
            ]);

            $invoice->note()->associate($injuryNote);
            $invoice->save();
        }

        if(count($result['ftNotatkaN']) > 0 && !in_array($injury->sap_rodzszk, ['TOT', 'KRA'])) {
            $injury->update([
                'sap_rodzszk' => 'CZA',
                'sap_stanszk' => ((int)$injury->sap_stanszk <= 2) ? 2 : $injury->sap_stanszk
            ]);
            $result = $sap->szkoda($injury);

            if($result['status'] == 200){
                Flash::message('Szkoda zaktualizowana w SAP');
            }else {
                Session::flash('show.modal.in.the.next.request', $result['msg']);
            }

            if(in_array( $result['status'], [200, 300])){
                Histories::history($injury->id, 217, Auth::user()->id);
            }
        }

        return Redirect::to(url('injuries/info', [$injury_id]).'#settlements');
    }

    public function postStoreNote($injury_id)
    {
        $injury = Injury::find($injury_id);

        $sap = new Idea\SapService\Sap();

        $notes = [];

        $msg = strip_tags( Input::get('content'));
        $nbsp = html_entity_decode("&nbsp;");
        $msg = html_entity_decode($msg);
        $msg = str_replace($nbsp, " ", $msg);
        $msg = preg_replace("/[\\n\\r]+/", "", $msg);
        $msg = trim($msg);
        $msg = preg_replace('!\s+!', ' ', $msg);

        $notes[0] = $msg;

        $result = $sap->szkodaNotUtworz($injury, $notes);

        $errors = [];
        if(isset($result['ftReturn']) && is_array($result['ftReturn'])){
            foreach($result['ftReturn'] as $ftReturn){
                if($ftReturn['typ'] =='E'){
                    $errors[] = $ftReturn;
                }
            }
        }

        if(count($errors) > 0){
            Flash::error('Wystąpił błąd w trakcie wysyłki notatek.');
            return Redirect::back();
        }

        foreach($result['ftNotatkaN'] as $note_item => $note){
            $injuryNote = InjuryNote::create([
                'injury_id' => $injury->id,
                'user_id' => Auth::user()->id,
                'roknotatki' => $note['roknotatki'],
                'nrnotatki'=> $note['nrnotatki'],
                'obiekt'=> $note['obiekt'],
                'temat'=> $note['temat'],
                'data'=> $note['data'],
                'uzeit'=> $note['uzeit'],
            ]);
        }

        return json_encode(['code' => 0]);
    }

    public function loadBranchData()
    {
        $branch = Branch::find(Input::get('branch_id'));

        if($branch){
            return json_encode(['branch' => $branch]);
        }

        return json_encode([]);
    }

    public function postSyncSapPremiums($injury_id)
    {
        $injury = Injury::find($injury_id);

        $sap = new Idea\SapService\Sap();

        $sap_remote_data = $sap->szkodaPobierz($injury->sap->szkodaId);
        if(isset($sap_remote_data['fsSzkodaOut']['dataszkody'])) unset($sap_remote_data['fsSzkodaOut']['dataszkody']);
        $injury->sap->update($sap_remote_data['fsSzkodaOut']);

        $ftExistingDoplaty = $sap->getExistingSapDoplaty($injury->sap);

        $new_premiums = 0;
        if($ftExistingDoplaty && count($ftExistingDoplaty) > 0){
            $premiums = [];
            foreach ($ftExistingDoplaty as $ftDoplata)
            {
                $premium = $injury->sapPremiums()->where('nrRaty', 'like', $ftDoplata['nrRaty'])->first();
                if($premium){
                    if(! $premium->injuryCompensation) $premium->update($ftDoplata);
                }else{
                    $premium = $injury->sapPremiums()->create($ftDoplata);
                    $new_premiums++;
                }
                $premiums[] = $premium->id;
            }
            $injury->sapPremiums()->whereNotIn('id', $premiums)->delete();
        }

        if($new_premiums == 0){
            Session::flash('show.modal.in.the.next.request', 'Brak nowych wypłat po stronie SAP.');
        }

        return Redirect::to('injuries/info/' . $injury_id .'#premiums');
    }
}
