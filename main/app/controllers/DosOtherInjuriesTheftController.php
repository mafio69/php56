<?php

/**
 * Class InjuriesTheftController
 * Zarządzanie kartoteką szkody -> kradzież
 */
class DosOtherInjuriesTheftController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

    public function startProcessing($id)
    {

        DosOtherInjuryTheft::create(array(
            'injury_id' => $id,
            'send_zu' => Date('Y-m-d', strtotime("+3 days"))
        ));

        $injury = DosOtherInjury::find($id);
        $injury->save();

        $url = URL::route('dos.other.injuries.info', array('id'=>$id, '#theft'));
        return Redirect::to($url);

    }

    public function setSend_zu_confirm($id)
    {
        $theft = DosOtherInjuryTheft::find($id);
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

            Histories::dos_history($theft->injury->id, 149, Auth::user()->id, 'zgłoszenie do ZU.');

        }else {

            if( $theft->send_zu_confirm_re == 0) {

                $theft->send_zu_confirm = date('Y-m-d');
                $theft->police_memo = Date('Y-m-d', strtotime("+3 days"));
                $theft->save();

                $result['alert'] = 'response-alert-info';
                $result['message'] = 'Zgłoszenie do ZU zostało potwierdzone.';
                $result['to_show'] = array('theft_doc_acceptation_panel');
                $result['to_disable'][] = 'send_zu';
            }else{

                $theft->send_zu_confirm = date('Y-m-d');
                $theft->save();

                $result['alert'] = 'response-alert-info';
                $result['message'] = 'Zgłoszenie do ZU zostało potwierdzone.';
                $result['to_show'] = array('theft_doc_acceptation_panel');
                $result['to_disable'][] = 'send_zu';

                $result['enable'] = 1;
            }

            Histories::dos_history($theft->injury->id, 144, Auth::user()->id, 'zgłoszenie do ZU.');

        }

        return json_encode($result);
    }

    public function setAcceptation($theft_id, $acceptation_id)
    {
        $theft = DosOtherInjuryTheft::find($theft_id);

        $acceptation = DosOtherInjuryTheftAcceptation::create(array(
            'injury_theft_id' => $theft_id,
            'injury_theft_acceptation_type_id' => $acceptation_id,
            'date_acceptation' => date('Y-m-d H:i:s'),
            'user_id' => Auth::user()->id
        ));

        $result = array();
        $result['alert'] = 'response-alert-info';
        $result['message'] = 'Nadesłanie dokumentu zostało potwierdzone.';
        $result['to_disable'][] = 'l_acceptation_' . $acceptation_id;
        $result['label'] = 'alert_acceptation_' . $acceptation_id;
        $result['label_content'] = substr($acceptation->date_acceptation, 0, -3) . '<br>' . Auth::user()->name;

        if($acceptation_id == 4)
            $result['to_hide'][] = 'label_theft_warning';

        if ($theft->hasAllAcceptations()){
            $result['to_show'][] = array('handling_panel');
            $theft->compensation_payment = Date('Y-m-d', strtotime("+3 days"));
            $theft->save();

            $result['to_set_val']['compensation_payment'] = $theft->compensation_payment;
            $result['to_enable'][] = 'compensation_payment';
            $result['to_enable'][] = 'label_check_compensation_payment';
        }

        return json_encode($result);
    }


    public function setCompensation_payment_confirm($id)
    {
        $theft = DosOtherInjuryTheft::find($id);
        $result = array();

        if($theft->compensation_payment_confirm != '0000-00-00') {
            $theft->compensation_payment_confirm_re = 1;
            $theft->compensation_payment_confirm = '0000-00-00';
            $theft->save();

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Wypłata odszkodowania została odtwierdzona.';
            $result['to_enable'][] = 'compensation_payment';
            $result['active'] = 1;
            $result['enable'] = 1;

            Histories::dos_history($theft->injury->id, 149, Auth::user()->id, 'wypłata odszkodowania.');
        }else {
            $theft->compensation_payment_confirm = date('Y-m-d');
            $theft->save();

            $result['alert'] = 'response-alert-info';
            $result['message'] = 'Wypłata odszkodowania została potwierdzona.';
            if ($theft->injury->object->gap == 1) {
                $result['to_show'] = array('gap-group');
                $theft->gap = Date('Y-m-d', strtotime("+3 days"));
                $theft->save();

                $result['to_set_val']['gap'] = $theft->gap;
                $result['to_enable'][] = 'gap';
                $result['to_enable'][] = 'label_check_gap';
            } else {
                $result['to_show'] = array('theft_dok_communication_panel');
            }

            if( $theft->compensation_payment_confirm_re == 1) {
                $result['enable'] = 1;
            }

            $result['to_disable'][] = 'compensation_payment';

            Histories::dos_history($theft->injury->id, 144, Auth::user()->id, 'wypłata odszkodowania.');
        }
        return json_encode($result);
    }

    public function setGap_confirm($id)
    {
        $theft = DosOtherInjuryTheft::find($id);

        $theft->gap_confirm = date('Y-m-d');
        $theft->save();

        $result = array();
        $result['alert']    = 'response-alert-info';
        $result['message']  = 'Wypłata GAP została potwierdzona.';
        $result['to_show'] = array('theft_dok_communication_panel');
        $result['to_disable'][] = 'gap';

        Histories::dos_history($theft->injury->id, 144, Auth::user()->id, 'wypłata GAP.' );

        return json_encode($result);
    }

    public function sendToDok($id)
    {
        $theft = DosOtherInjuryTheft::find($id);

        $theft->send_to_dok_date = date('Y-m-d H:i:s');
        $theft->save();

        $result = array();
        $result['alert']    = 'response-alert-info';
        $result['message']  = 'Potwierdzono przekazanie do DOK.';
        $result['to_disable'][] ='theft_send_to_dok_date';
        $result['label'] = 'alert_theft_send_to_dok_date';
        $result['label_content'] = substr($theft->send_to_dok_date, 0, -3);

        Histories::dos_history($theft->injury->id, 144, Auth::user()->id, 'przekazanie do DOK.' );
        return json_encode($result);

    }


}