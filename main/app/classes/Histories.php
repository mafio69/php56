<?php
class Histories {

    public static function history($injury, $zdarzenie,  $user = null,   $value='', $wpis='') {
        if(is_null($user))
            $user = Auth::user()->id;

    	$data = date('Y-m-d H:i:s');
        
		if($zdarzenie == 0 ){
			$history = InjuryHistory::create(array(
					'injury_id' => $injury,
					'user_id'	=> $user,
					'history_type_id'	=> $zdarzenie,
					'created_at'	=> $data,
					'value'		=> $value
				));

			InjuryHistoryContent::create(array(
					'injury_history_id' => $history->id,
					'content' 			=> $wpis
				));
			
		}else{			
			$history = InjuryHistory::create(array(
					'injury_id' => $injury,
					'user_id'	=> $user,
					'history_type_id'	=> $zdarzenie,
					'created_at'	=> $data,
					'value'		=> $value
				));
			
			if( $value == '-1'){
				InjuryHistoryContent::create(array(
					'injury_history_id' => $history->id,
					'content' 			=> $wpis
				));
			}
		}
    }

    public static function dok_history($injury, $zdarzenie,  $user,   $value='', $wpis='') {
    	$data = date('Y-m-d H:i:s');
        
		if($zdarzenie == 0 ){
			$history = DokNotification_history::create(array(
					'dok_notification_id' => $injury,
					'user_id'	=> $user,
					'dok_history_type_id'	=> $zdarzenie,
					'created_at'	=> $data,
					'value'		=> $value
				));

			DokHistory_content::create(array(
					'dok_history_id' => $history->id,
					'content' 			=> $wpis
				));
			
		}else{			
			$history = DokNotification_history::create(array(
					'dok_notification_id' => $injury,
					'user_id'	=> $user,
					'dok_history_type_id'	=> $zdarzenie,
					'created_at'	=> $data,
					'value'		=> $value
				));
			
			if( $value == '-1'){
				DokHistory_content::create(array(
					'dok_history_id' => $history->id,
					'content' 			=> $wpis
				));
			}
		}
    }

    public static function dos_history($injury, $zdarzenie,  $user,   $value='', $wpis='') {
        $data = date('Y-m-d H:i:s');

        if($zdarzenie == 0 ){
            $history = DosOtherInjuryHistory::create(array(
                'injury_id' => $injury,
                'user_id'	=> $user,
                'history_type_id'	=> $zdarzenie,
                'created_at'	=> $data,
                'value'		=> $value
            ));

            DosOtherInjuryHistoryContent::create(array(
                'injury_history_id' => $history->id,
                'content' 			=> $wpis
            ));

        }else{
            $history = DosOtherInjuryHistory::create(array(
                'injury_id' => $injury,
                'user_id'	=> $user,
                'history_type_id'	=> $zdarzenie,
                'created_at'	=> $data,
                'value'		=> $value
            ));

            if( $value == '-1'){
                DosOtherInjuryHistoryContent::create(array(
                    'dos_other_injury_history_id' => $history->id,
                    'content' 			=> $wpis
                ));
            }
        }
    }

    public static function leasingAgreementHistory($leasingAgreement, $historyType,  $user = null,   $value='', $content='') {

        if(is_null($user))
            $user = Auth::user()->id;

        if($historyType == 0 ){
            $history = LeasingAgreementHistory::create(array(
                'leasing_agreement_id' => $leasingAgreement,
                'user_id'	=> $user,
                'leasing_agreement_history_type_id'	=> $historyType,
                'value'		=> $value,
                'notification_number' => Auth::user()->insurances_global_nr
            ));

            LeasingAgreementHistoryContent::create(array(
                'leasing_agreement_history_id' => $history->id,
                'content' 			=> $content
            ));

        }else{
            $history = LeasingAgreementHistory::create(array(
                'leasing_agreement_id' => $leasingAgreement,
                'user_id'	=> $user,
                'leasing_agreement_history_type_id'	=> $historyType,
                'value'		=> $value,
                'notification_number' => Auth::user()->insurances_global_nr
            ));

            if( $value == '-1'){
                LeasingAgreementHistoryContent::create(array(
                    'leasing_agreement_history_id' => $history->id,
                    'content' 			=> $content
                ));
            }
        }

        return $history->id;
    }

}