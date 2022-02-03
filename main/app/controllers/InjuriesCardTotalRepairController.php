<?php

/**
 * Class InjuriesCardTotalRepairController
 * Zarządzanie kartoteką szkody -> naprawa po szkodzie całkowitej
 */
class InjuriesCardTotalRepairController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

    public function setAlert_receive_confirm($id)
    {
        $injuryTotal = InjuryTotalRepair::find($id);

        $injuryTotal->alert_receive_confirm = date('Y-m-d');
        $injuryTotal->save();

        $result = array();
        $result['alert']    = 'response-alert-info';
        $result['message']  = 'Napłynięcie wniosku zostało potwierdzone.';
        $result['to_show'] = array('repair_confirmation_data');
        $result['to_disable'][] = 'alert_receive';
        return json_encode($result);
    }

    public function setAcceptation($total_repair_id, $acceptation_id)
    {
        $totalRepair = InjuryTotalRepair::find($total_repair_id);

        $acceptation = InjuryTotalRepairAcceptation::create(array(
            'injury_total_repair_id' => $total_repair_id,
            'injury_total_repair_acceptation_type_id' => $acceptation_id,
            'date_acceptation' => date('Y-m-d H:i:s'),
            'user_id' => Auth::user()->id
        ));

        $result = array();
        $result['alert'] = 'response-alert-info';
        $result['message'] = 'Nadesłanie dokumentu zostało potwierdzone.';
        $result['to_disable'][] = 'l_acceptation_' . $acceptation_id;
        $result['label'] = 'alert_acceptation_' . $acceptation_id;
        $result['label_content'] = substr($acceptation->date_acceptation, 0, -3) . '<br>' . Auth::user()->name;
        if ($totalRepair->hasAllAcceptations()){
            $result['to_show'] = array('dok_communication_panel');
        }

        return json_encode($result);
    }

    public function sendToDok($id)
    {
        $totalRepair = InjuryTotalRepair::find($id);

        $totalRepair->send_to_dok_date = date('Y-m-d H:i:s');

        $totalRepair->save();

        $result = array();
        $result['alert']    = 'response-alert-info';
        $result['message']  = 'Potwierdzono przekazanie do DOK.';
        $result['to_disable'][] ='send_to_dok_date';
        $result['label'] = 'alert_send_to_dok_date';
        $result['label_content'] = substr($totalRepair->send_to_dok_date, 0, -3);
        return json_encode($result);

    }


}