<?php

/**
 * Class InjuriesTheftController
 * Zarządzanie kartoteką szkody -> kradzież
 */
class InjuriesTheftController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

    public function startProcessing($id)
    {

        InjuryTheft::create(array(
            'injury_id' => $id,
            'send_zu' => Date('Y-m-d', strtotime("+3 days"))
        ));

        $injury = Injury::find($id);
        $injury->theft_status_id = 1;
        $injury->save();

        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 1,
            'status_type' => 'InjuryTheftStatuses'
        ]);

        $url = URL::route('injuries-info', array('id'=>$id, '#theft'));
        return Redirect::to($url);

    }

    public function setSend_zu_confirm($id)
    {
        $theft = InjuryTheft::find($id);
        $result = array();

        if($theft->send_zu_confirm != '0000-00-00'){

            $theft->send_zu_confirm = '0000-00-00';
            $theft->send_zu_confirm_re = 1;
            $theft->save();

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Zgłoszenie do ZU zostało odtwierdzone.';
            $result['to_enable'][] = 'send_zu';
            $result['active'] = 1;
            $result['enable'] = 1;

            Histories::history($theft->injury->id, 149, Auth::user()->id, 'zgłoszenie do ZU.');

        }else {

            if( $theft->send_zu_confirm_re == 0) {

                $theft->send_zu_confirm = date('Y-m-d');
                $theft->police_memo = Date('Y-m-d', strtotime("+3 days"));
                $theft->save();

                $theft->injury->theft_status_id = 2;
                $theft->injury->save();

                InjuryStatusesHistory::create([
                    'injury_id' => $theft->injury->id,
                    'user_id'   => Auth::user()->id,
                    'status_id' => 2,
                    'status_type' => 'InjuryTheftStatuses'
                ]);

                $result['alert'] = 'response-alert-info';
                $result['message'] = 'Zgłoszenie do ZU zostało potwierdzone.';
                $result['to_show'] = array('police_memo-group');
                $result['to_disable'][] = 'send_zu';


                $result['to_set_val']['police_memo'] = $theft->police_memo;
                $result['to_enable'][] = 'police_memo';
                $result['to_enable'][] = 'label_check_police_memo';

            }else{

                $theft->send_zu_confirm = date('Y-m-d');
                $theft->save();

                $result['alert'] = 'response-alert-info';
                $result['message'] = 'Zgłoszenie do ZU zostało potwierdzone.';
                $result['to_show'] = array('police_memo-group');
                $result['to_disable'][] = 'send_zu';

                $result['enable'] = 1;
            }

            Histories::history($theft->injury->id, 144, Auth::user()->id, 'zgłoszenie do ZU.');

        }

        return json_encode($result);
    }

    public function setPolice_memo_confirm($id)
    {
        $theft = InjuryTheft::find($id);

        if($theft->police_memo_confirm != '0000-00-00') {
            $theft->police_memo_confirm_re = 1;
            $theft->police_memo_confirm = '0000-00-00';
            $theft->save();

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Wystawienie notatki policyjnej zostało odtwierdzone.';
            $result['to_enable'][] = 'police_memo';
            $result['active'] = 1;
            $result['enable'] = 1;

            Histories::history($theft->injury->id, 149, Auth::user()->id, 'zgłoszenie do ZU.');
        }else {
            if( $theft->police_memo_confirm_re == 0) {
                $theft->police_memo_confirm = date('Y-m-d');
                $theft->save();

                $theft->injury->theft_status_id = 3;
                $theft->injury->save();

                InjuryStatusesHistory::create([
                    'injury_id' => $theft->injury->id,
                    'user_id'   => Auth::user()->id,
                    'status_id' => 3,
                    'status_type' => 'InjuryTheftStatuses'
                ]);

                $result['alert'] = 'response-alert-info';
                $result['message'] = 'Wystawienie notatki policyjnej zostało potwierdzone.';
                $result['to_show'] = array('theft_doc_acceptation_panel');
                $result['to_disable'][] = 'police_memo';
            }else{
                $theft->police_memo_confirm = date('Y-m-d');
                $theft->save();

                $result['alert'] = 'response-alert-info';
                $result['message'] = 'Wystawienie notatki policyjnej zostało potwierdzone.';
                $result['to_show'] = array('theft_doc_acceptation_panel');
                $result['to_disable'][] = 'police_memo';

                $result['enable'] = 1;
            }

            Histories::history($theft->injury->id, 144, Auth::user()->id, 'wystawienie notatki policyjnej.');
        }

        return json_encode($result);
    }

    public function setAcceptation($theft_id, $acceptation_id)
    {
        $theft = InjuryTheft::find($theft_id);

        $acceptation = InjuryTheftAcceptation::where('injury_theft_id', $theft_id)->where('injury_theft_acceptation_type_id', $acceptation_id)->first();
        if(! $acceptation) {
            $acceptation = InjuryTheftAcceptation::create(array(
                'injury_theft_id' => $theft_id,
                'injury_theft_acceptation_type_id' => $acceptation_id,
                'date_acceptation' => date('Y-m-d H:i:s'),
                'user_id' => Auth::user()->id
            ));
        }else{
            $acceptation->date_acceptation = date('Y-m-d H:i:s');
            $acceptation->save();
        }

        $result = array();
        $result['alert'] = 'response-alert-info';
        $result['message'] = 'Nadesłanie dokumentu zostało potwierdzone.';
        $result['to_disable'][] = 'l_acceptation_' . $acceptation_id;
        $result['to_disable'][] = 'status_' . $acceptation_id;
        $result['to_disable'][] = 'value_' . $acceptation_id;

        $result['label'] = 'alert_acceptation_' . $acceptation_id;
        $result['label_content'] = substr($acceptation->date_acceptation, 0, -3) . '<br>' . Auth::user()->name;
        if ($theft->hasAllAcceptations()){
            $result['to_show'] = array('handling_panel');
            $theft->redemption_investigation = Date('Y-m-d', strtotime("+3 days"));
            $theft->save();

            $theft->injury->theft_status_id = 4;
            $theft->injury->save();

            InjuryStatusesHistory::create([
                'injury_id' => $theft->injury->id,
                'user_id'   => Auth::user()->id,
                'status_id' => 4,
                'status_type' => 'InjuryTheftStatuses'
            ]);

            $result['to_set_val']['redemption_investigation'] = $theft->redemption_investigation;
            $result['to_enable'][] = 'redemption_investigation';
            $result['to_enable'][] = 'label_check_redemption_investigation';
        }

        $result['to_show'][] = 'l_rollback_acceptation_' . $acceptation_id;

        return json_encode($result);
    }

    public function rollbackAcceptation($theft_id, $acceptation_id)
    {
        $theft = InjuryTheft::find($theft_id);

        $acceptation = InjuryTheftAcceptation::where('injury_theft_id', $theft_id)->where('injury_theft_acceptation_type_id', $acceptation_id)->first();
        if($acceptation) {
            $acceptation->delete();
        }

        $result = array();
        $result['alert'] = 'response-alert-info';
        $result['message'] = 'Nadesłanie dokumentu zostało cofnięte.';
        $result['to_hide'][] = 'l_rollback_acceptation_' . $acceptation_id;
        $result['to_hide'][] = 'alert_acceptation_' . $acceptation_id;

        $result['to_enable'][] = 'l_acceptation_' . $acceptation_id;
        $result['to_enable'][] = 'status_' . $acceptation_id;
        $result['to_enable'][] = 'value_' . $acceptation_id;

        $result['non_active'] = 'l_acceptation_' . $acceptation_id;

        Histories::history($theft->injury->id, 169, Auth::user()->id, $acceptation->acceptation->name);

        return json_encode($result);
    }


    public function setAcceptationParam($theft_id, $acceptation_id, $param)
    {
        $acceptation = InjuryTheftAcceptation::where('injury_theft_id', $theft_id)->where('injury_theft_acceptation_type_id', $acceptation_id)->first();
        if(! $acceptation) {
            InjuryTheftAcceptation::create(array(
                'injury_theft_id' => $theft_id,
                'injury_theft_acceptation_type_id' => $acceptation_id,
                $param => Input::get('alert'),
                'user_id' => Auth::user()->id
            ));
        }else{
            $acceptation->$param =  Input::get('alert');
            $acceptation->save();
        }

        $result = array();
        $result['alert'] = 'response-alert-info';
        $result['message'] = 'Ustawiono parametr.';

        return json_encode($result);
    }

    public function setRedemption_investigation_confirm($id)
    {
        $theft = InjuryTheft::find($id);
        $result = array();

        if($theft->redemption_investigation_confirm != '0000-00-00') {
            $theft->redemption_investigation_confirm_re = 1;
            $theft->redemption_investigation_confirm = '0000-00-00';

            $theft->deregistration_vehicle_confirm = '0000-00-00';
            $theft->compensation_payment_confirm = '0000-00-00';
            $theft->gap_confirm = '0000-00-00';

            $theft->save();

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Wydanie postanowienia o umorzeniu dochodzenia zostało odtwierdzone.';
            $result['to_hide'] = array('theft_dok_communication_panel', 'gap-group','compensation_payment-group', 'deregistration_vehicle-group');
            $result['to_enable'] = array('redemption_investigation', 'label_check_punishable',
                'deregistration_vehicle',
                'compensation_payment_value', 'compensation_payment', 'label_deny_compensation_payment', 'label_check_compensation_payment',
                'gap'
            );
            $result['to_deactivate'] = array('label_check_gap', 'label_check_compensation_payment', 'label_check_deregistration_vehicle');

            Histories::history($theft->injury->id, 149, Auth::user()->id, 'umorzenie dochodzenia.');
        }else {
            if( $theft->redemption_investigation_confirm_re == 0) {
                $theft->redemption_investigation_confirm = date('Y-m-d');
                $theft->deregistration_vehicle = Date('Y-m-d', strtotime("+3 days"));
                $theft->save();

                $theft->injury->theft_status_id = 5;
                $theft->injury->save();

                InjuryStatusesHistory::create([
                    'injury_id' => $theft->injury->id,
                    'user_id'   => Auth::user()->id,
                    'status_id' => 5,
                    'status_type' => 'InjuryTheftStatuses'
                ]);

                $result['alert'] = 'response-alert-info';
                $result['message'] = 'Wydanie postanowienia o umorzeniu dochodzenia zostało potwierdzone.';
                $result['to_show'] = array('deregistration_vehicle-group');
                $result['to_disable'][] = 'redemption_investigation';
                $result['to_disable'][] = 'label_check_punishable';

                $result['to_set_val']['deregistration_vehicle'] = $theft->deregistration_vehicle;
                $result['to_enable'][] = 'deregistration_vehicle';
                $result['to_enable'][] = 'label_check_deregistration_vehicle';
            }else{
                $theft->redemption_investigation_confirm = date('Y-m-d');
                $theft->save();

                $result['alert'] = 'response-alert-info';
                $result['message'] = 'Wydanie postanowienia o umorzeniu dochodzenia zostało potwierdzone.';
                $result['to_show'] = array('deregistration_vehicle-group');
                $result['to_disable'][] = 'redemption_investigation';
                $result['to_disable'][] = 'label_check_punishable';

                // $result['enable'] = 1;
            }

            Histories::history($theft->injury->id, 144, Auth::user()->id, 'umorzenie dochodzenia.');
        }

        if(Auth::user()->can('kartoteka_szkody#kradziez#edycja')){
            $result['active'] = 1;
            $result['enable'] = 1;
        } else {
            $result['active'] = 0;
            $result['enable'] = 0;
        }

        return json_encode($result);
    }

    public function setDeregistration_vehicle_confirm($id)
    {
        $theft = InjuryTheft::find($id);
        $result = array();

        if($theft->deregistration_vehicle_confirm != '0000-00-00') {
            $theft->deregistration_vehicle_confirm_re = 1;
            $theft->deregistration_vehicle_confirm = '0000-00-00';
            
            $theft->compensation_payment_confirm = '0000-00-00';
            $theft->gap_confirm = '0000-00-00';

            $theft->save();

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Wyrejestrowanie pojazdu zostało odtwierdzone.';
            $result['to_hide'] = array('theft_dok_communication_panel', 'gap-group', 'compensation_payment-group');
            $result['to_enable'] = array('deregistration_vehicle',
                'compensation_payment_value', 'compensation_payment', 'label_deny_compensation_payment', 'label_check_compensation_payment',
                'gap'
            );
            $result['to_deactivate'] = array('label_check_gap', 'label_check_compensation_payment');

            Histories::history($theft->injury->id, 149, Auth::user()->id, 'wyrejestrowanie pojazdu.');
        }else {
            if( $theft->deregistration_vehicle_confirm_re == 0) {
                $theft->deregistration_vehicle_confirm = date('Y-m-d');
                $theft->compensation_payment = Date('Y-m-d', strtotime("+3 days"));
                $theft->save();

                $theft->injury->theft_status_id = 6;
                $theft->injury->save();

                InjuryStatusesHistory::create([
                    'injury_id' => $theft->injury->id,
                    'user_id'   => Auth::user()->id,
                    'status_id' => 6,
                    'status_type' => 'InjuryTheftStatuses'
                ]);


                $result['alert'] = 'response-alert-info';
                $result['message'] = 'Wyrejestrowanie pojazdu potwierdzone.';
                $result['to_show'] = array('compensation_payment-group');
                $result['to_disable'][] = 'deregistration_vehicle';

                $result['to_set_val']['compensation_payment'] = $theft->compensation_payment;
                $result['to_enable'][] = 'compensation_payment';
                $result['to_enable'][] = 'label_check_compensation_payment';
            }else{
                $theft->deregistration_vehicle_confirm = date('Y-m-d');
                $theft->save();

                $result['alert'] = 'response-alert-info';
                $result['message'] = 'Wyrejestrowanie pojazdu potwierdzone.';
                $result['to_show'] = array('compensation_payment-group');
                $result['to_disable'][] = 'deregistration_vehicle';


                $result['enable'] = 1;
            }
            Histories::history($theft->injury->id, 144, Auth::user()->id, 'wyrejestrowanie pojazdu.');
        }
        
        if(Auth::user()->can('kartoteka_szkody#kradziez#edycja')){
            $result['active'] = 1;
            $result['enable'] = 1;
        } else {
            $result['active'] = 0;
            $result['enable'] = 0;
        }

        return json_encode($result);
    }

    public function setCompensation_payment_confirm($id)
    {
        $theft = InjuryTheft::find($id);
        $result = array();

        if($theft->compensation_payment_confirm != '0000-00-00') {
            $theft->compensation_payment_confirm_re = 1;
            $theft->compensation_payment_confirm = '0000-00-00';

            $theft->gap_confirm = '0000-00-00';

            $theft->save();

            InjuryStatusesHistory::create([
                'injury_id' => $theft->injury->id,
                'user_id'   => Auth::user()->id,
                'status_id' => 6,
                'status_type' => 'InjuryTheftStatuses'
            ]);

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Wypłata odszkodowania została odtwierdzona.';
            $result['to_hide'] = array('theft_dok_communication_panel', 'gap-group');
            $result['to_enable'] = array(
                'compensation_payment_value', 'compensation_payment', 'label_deny_compensation_payment', 'label_check_compensation_payment',
                'gap'
            );
            $result['to_deactivate'] = array('label_check_gap');

            Histories::history($theft->injury->id, 149, Auth::user()->id, 'wypłata odszkodowania.');
        }else {
            $theft->compensation_payment_confirm = date('Y-m-d');
            $theft->save();

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Wypłata odszkodowania została potwierdzona.';
            if ($theft->injury->vehicle->gap == 1) {
                $result['to_show'] = array('gap-group');
                $theft->gap = Date('Y-m-d', strtotime("+3 days"));
                $theft->save();

                $theft->injury->theft_status_id = 7;
                $theft->injury->save();

                InjuryStatusesHistory::create([
                    'injury_id' => $theft->injury->id,
                    'user_id'   => Auth::user()->id,
                    'status_id' => 7,
                    'status_type' => 'InjuryTheftStatuses'
                ]);

                $result['to_set_val']['gap'] = $theft->gap;
                $result['to_enable'][] = 'gap';
                $result['to_enable'][] = 'label_check_gap';
            } else {
                $result['to_show'] = array('theft_dok_communication_panel');

                $theft->injury->theft_status_id = 8;
                $theft->injury->save();

                InjuryStatusesHistory::create([
                    'injury_id' => $theft->injury->id,
                    'user_id'   => Auth::user()->id,
                    'status_id' => 8,
                    'status_type' => 'InjuryTheftStatuses'
                ]);
            }

            if( $theft->compensation_payment_confirm_re == 1) {
                $result['enable'] = 1;
            }

            $result['to_disable'][] = 'compensation_payment';
            $result['to_disable'][] = Auth::user()->can('kartoteka_szkody#kradziez#edycja') ? '' : 'label_check_compensation_payment';
            $result['to_disable'][] = 'label_deny_compensation_payment';
            $result['to_disable'][] = 'compensation_payment_value';

            Histories::history($theft->injury->id, 144, Auth::user()->id, 'wypłata odszkodowania.');
        }

        if(Auth::user()->can('kartoteka_szkody#kradziez#edycja')){
            $result['active'] = 1;
            $result['enable'] = 1;
        } else {
            $result['active'] = 0;
            $result['enable'] = 0;
        }

        return json_encode($result);
    }

    public function setCompensation_payment_deny($id)
    {
        $theft = InjuryTheft::find($id);
        $result = array();

        if($theft->compensation_payment_deny){
            $theft->compensation_payment_deny = null;

            $theft->gap_confirm = '0000-00-00';

            $theft->save();

            InjuryStatusesHistory::create([
                'injury_id' => $theft->injury->id,
                'user_id'   => Auth::user()->id,
                'status_id' => 6,
                'status_type' => 'InjuryTheftStatuses'
            ]);

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Potwierdzenie odmowy wypłaty zostało anulowane.';
            $result['to_hide'] = array('theft_dok_communication_panel', 'gap-group');
            $result['to_enable'] = array(
                'compensation_payment_value', 'compensation_payment', 'label_deny_compensation_payment', 'label_check_compensation_payment',
                'gap'
            );
            $result['to_deactivate'] = array('label_check_gap');

            Histories::history($theft->injury->id, 149, Auth::user()->id, 'odmowa wypłaty odszkodowania.');
        } else {
            $theft->compensation_payment_deny = date('Y-m-d');
            $theft->save();

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Odmowa wypłaty odszkodowania została potwierdzona.';
            if ($theft->injury->vehicle->gap == 1) {
                $result['to_show'] = array('gap-group');
                $theft->gap = Date('Y-m-d', strtotime("+3 days"));
                $theft->save();

                $theft->injury->theft_status_id = 7;
                $theft->injury->save();

                InjuryStatusesHistory::create([
                    'injury_id' => $theft->injury->id,
                    'user_id'   => Auth::user()->id,
                    'status_id' => 7,
                    'status_type' => 'InjuryTheftStatuses'
                ]);

                $result['to_set_val']['gap'] = $theft->gap;
                $result['to_enable'][] = 'gap';
                $result['to_enable'][] = 'label_check_gap';
            } else {
                $result['to_show'] = array('theft_dok_communication_panel');

                $theft->injury->theft_status_id = 8;
                $theft->injury->save();

                InjuryStatusesHistory::create([
                    'injury_id' => $theft->injury->id,
                    'user_id'   => Auth::user()->id,
                    'status_id' => 8,
                    'status_type' => 'InjuryTheftStatuses'
                ]);
            }

            $result['to_disable'][] = 'compensation_payment';
            $result['to_disable'][] = 'label_check_compensation_payment';
            $result['to_disable'][] = Auth::user()->can('kartoteka_szkody#kradziez#edycja') ? '' : 'label_deny_compensation_payment';
            $result['to_disable'][] = 'compensation_payment_value';

            Histories::history($theft->injury->id, 144, Auth::user()->id, 'odmowa wypłaty odszkodowania.');
        }

        if(Auth::user()->can('kartoteka_szkody#kradziez#edycja')){
            $result['active'] = 1;
            $result['enable'] = 1;
        } else {
            $result['active'] = 0;
            $result['enable'] = 0;
        }

        return json_encode($result);
    }

    public function setGap_confirm($id)
    {
        $theft = InjuryTheft::find($id);
        $result = array();

        if($theft->gap_confirm != '0000-00-00') {
            $theft->gap_confirm = '0000-00-00';
            $theft->save();

            $theft->injury->theft_status_id = 7;
            $theft->injury->save();

            InjuryStatusesHistory::create([
                'injury_id' => $theft->injury->id,
                'user_id'   => Auth::user()->id,
                'status_id' => 7,
                'status_type' => 'InjuryTheftStatuses'
            ]);

            $result['alert']    = 'response-alert-info';
            $result['message']  = 'Potwierdzenie wypłaty GAP zostało cofnięte.';
            $result['to_hide'] = array('theft_dok_communication_panel');
            $result['to_enable'][] = 'gap';
            if(Auth::user()->can('kartoteka_szkody#kradziez#edycja')){
                $result['active'] = 1;
                $result['enable'] = 1;
            } else {
                $result['active'] = 0;
                $result['enable'] = 0;
            }

            Histories::history($theft->injury->id, 149, Auth::user()->id, 'wypłata GAP.' );

        } else {
            $theft->gap_confirm = date('Y-m-d');
            $theft->save();

            $theft->injury->theft_status_id = 8;
            $theft->injury->save();

            InjuryStatusesHistory::create([
                'injury_id' => $theft->injury->id,
                'user_id'   => Auth::user()->id,
                'status_id' => 8,
                'status_type' => 'InjuryTheftStatuses'
            ]);

            $result['alert']    = 'response-alert-info';
            $result['message']  = 'Wypłata GAP została potwierdzona.';
            $result['to_show'] = array('theft_dok_communication_panel');
            $result['to_disable'][] = 'gap';
            if(Auth::user()->can('kartoteka_szkody#kradziez#edycja')){
                $result['active'] = 1;
                $result['enable'] = 1;
            } else {
                $result['active'] = 0;
                $result['enable'] = 0;
            }

            Histories::history($theft->injury->id, 144, Auth::user()->id, 'wypłata GAP.' );
        }

        return json_encode($result);
    }

    public function sendToDok($id)
    {
        $theft = InjuryTheft::find($id);

        $theft->send_to_dok_date = date('Y-m-d H:i:s');
        $theft->save();

        $theft->injury->theft_status_id = 9;
        $theft->injury->save();

        InjuryStatusesHistory::create([
            'injury_id' => $theft->injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 9,
            'status_type' => 'InjuryTheftStatuses'
        ]);

        $result = array();
        $result['alert']    = 'response-alert-info';
        $result['message']  = 'Potwierdzono przekazanie do DOK.';
        $result['to_disable'][] ='theft_send_to_dok_date';
        $result['label'] = 'alert_theft_send_to_dok_date';
        $result['label_content'] = substr($theft->send_to_dok_date, 0, -3);

        Histories::history($theft->injury->id, 144, Auth::user()->id, 'przekazano do DOK.');
        return json_encode($result);

    }

    public function setPunishable($id)
    {
        $theft = InjuryTheft::find($id);
        $result = array();

        if($theft->punishable == null){
            $theft->punishable = date('Y-m-d');
            $theft->punishable_user_id = Auth::user()->id;
            $theft->save();

            $theft->injury->theft_status_id = 10;
            $theft->injury->save();

            InjuryStatusesHistory::create([
                'injury_id' => $theft->injury->id,
                'user_id'   => Auth::user()->id,
                'status_id' => 10,
                'status_type' => 'InjuryTheftStatuses'
            ]);

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Potwierdzono brak znamion czynu karalnego.';

            if(Auth::user()->can('kartoteka_szkody#kradziez#edycja')){
                $result['enable'] = 1;
                $result['active'] = 1;
            } else {
                $result['enable'] = 0;
                $result['active'] = 0;
            }

            $result['to_hide'] = array('deregistration_vehicle-group', 'theft_dok_communication_panel', 'compensation_payment-group', 'gap-group');
            $result['to_disable'][] = 'redemption_investigation';
            $result['to_disable'][] = 'label_check_redemption_investigation';

            Histories::history($theft->injury->id, 144, Auth::user()->id, 'potwierdzenie braku znamion czynu karalnego.');

        } else {
            $theft->punishable = null;
            $theft->punishable_user_id = null;
            $theft->save();

            $theft->injury->theft_status_id = 4;
            $theft->injury->save();

            InjuryStatusesHistory::create([
                'injury_id' => $theft->injury->id,
                'user_id'   => Auth::user()->id,
                'status_id' => 4,
                'status_type' => 'InjuryTheftStatuses'
            ]);

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Anulowano potwierdzenie braku znamion czynu karalnego.';

            if(Auth::user()->can('kartoteka_szkody#kradziez#edycja')){
                $result['enable'] = 1;
                $result['active'] = 1;
            } else {
                $result['enable'] = 0;
                $result['active'] = 0;
            }

            $result['to_enable'][] = 'redemption_investigation';
            $result['to_enable'][] = 'label_check_redemption_investigation';

            Histories::history($theft->injury->id, 144, Auth::user()->id, 'anulowanie potwierdzenia braku znamion czynu karalnego.');
        }
        
        return json_encode($result);
    }

}