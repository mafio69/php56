<?php

class Injury extends Eloquent
{
    protected $table = 'injury';

    protected $fillable = array(
        'sap_id',
        'sap_date',
        'user_id',
        'vehicle_id',
        'vehicle_type',
        'contract_status_id',
        'client_id',
        'driver_id',
        'leader_id',
        'leader_assign_date',
        'settlements_leader_id',
        'settlements_leader_assign_date',
        'notifier_surname',
        'notifier_name',
        'notifier_phone',
        'notifier_email',
        'injuries_type_id',
        'task_inspection',
        'task_orderedParts',
        'task_pickup',
        'task_authorization',
        'offender_id',
        'injury_nr',
        'case_nr',
        'ea_case_number',
        'info',
        'remarks',
        'remarks_damage',
        'police',
        'police_nr',
        'police_unit',
        'police_contact',
        'date_event',
        'event_post',
        'event_city',
        'event_street',
        'if_map',
        'if_map_correct',
        'lat',
        'lng',
        'receive_id',
        'invoicereceives_id',
        'branch_id',
        'type_incident_id',
        'contact_person',
        'if_statement',
        'if_registration_book',
        'if_towing',
        'if_courtesy_car',
        'if_door2door',
        'if_theft',
        'if_driver_fault',
        'issue_fee',
        'date_admission',
        'date_total_theft_register',
        'date_end',
        'date_end_normal',
        'date_end_total',
        'date_end_theft',
        'step',
        'prev_step',
        'injury_step_stage_id',
        'injury_total_step_stage_id',
        'current_injury_repair_stage_id',
        'total_status_id',
        'theft_status_id',
        'canceled_chat_id',
        'locked_status',
        'way_of',
        'if_vip',
        'settlement_cost_estimate',
        'dsp_notification',
        'vindication',
        'reported_ic',
        'in_service',
        'if_il_repair',
        'il_repair_info',
        'il_repair_info_description',
        'source',
        'time_event',
        'original_branch_id',
        'insurance_company_id',
        'injury_policy_id',
        'gap_forecast',
        'is_cas_case',
        'cas_offer_agreement',
        'sap_stanszk',
        'sap_rodzszk',
        'active',
        'skip_in_ending_report',
        'created_at',
        'updated_at'
    );

    public static function boot(){
        parent::boot();

        static::updated(function ($record){
            $dirty = $record->getDirty();
            if(isset($dirty['branch_id']) && is_numeric($dirty['branch_id']) && $dirty['branch_id'] > 0){
                $record->branches()->create([
                    'branch_id' => $dirty['branch_id'],
                    'user_id' => Auth::check() ? Auth::user()->id : null
                ]);
            }
        });

        static::addGlobalScope(new \Idea\Scopes\InjuryUserManageableScope());

        static::updated(
            function ($record) {

                $dirty = $record->getDirty();
                foreach ($dirty as $changed_field => $changed_value) {
                    switch ($changed_field) {
                        case 'step':
                            InjuryStepHistory::create([
                                'user_id' => Auth::user()->id,
                                'injury_id' => $record->id,
                                'prev_step_id' => $record->getOriginal('step'),
                                'next_step_id' => $record->step,
                                'injury_step_stage_id' => isset($dirty['injury_step_stage_id'])?$record->injury_step_stage_id:null
                            ]);
                            break;
                    }
                }
                return true;
            });
    }

    public function mobileInjury()
    {
        return $this->hasOne('MobileInjury', 'injury_id');
    }

    public function injuries_type()
    {
        return $this->belongsTo('Injuries_type');
    }

    public function vehicle()
    {
        return $this->morphTo()->withTrashed();
    }

    public function leader()
    {
        return $this->belongsTo('User', 'leader_id')->withTrashed();
    }

    public function settlementsLeader()
    {
        return $this->belongsTo('User', 'settlements_leader_id')->withTrashed();
    }

    public function scopeVehicleExists($query, $parameter, $value, $whereType = 'orWhere', $whereMethod = 'like')
    {
        return $query->$whereType(function ($query) use($parameter, $value, $whereMethod){
                    $query->where(function ($query)  use($parameter, $value, $whereMethod){
                        $query->where('vehicle_type', 'Vehicles')
                            ->whereHas('vehicleFromVehicle', function($subquery) use($parameter, $value, $whereMethod){
                                $subquery->where($parameter, $whereMethod, '%'.$value.'%');
                            });
                    })
                    ->orWhere(function ($query) use($parameter, $value, $whereMethod) {
                        $query->where('vehicle_type', 'VmanageVehicle')
                            ->whereHas('vehicleFromVmanageVehicle', function ($subquery) use ($parameter, $value, $whereMethod) {
                                $subquery->where($parameter, $whereMethod, '%' . $value . '%');
                            });
                    });
            });
    }

    public function scopeVehicleExistsLikeEnd($query, $parameter, $value, $whereType = 'orWhere', $whereMethod = 'like')
    {
        return $query->$whereType(function ($query) use($parameter, $value, $whereMethod){
            $query->where(function ($query)  use($parameter, $value, $whereMethod){
                $query->where('vehicle_type', 'Vehicles')
                    ->whereHas('vehicleFromVehicle', function($subquery) use($parameter, $value, $whereMethod){
                        $subquery->where($parameter, $whereMethod, '%'.$value);
                    });
            })
                ->orWhere(function ($query) use($parameter, $value, $whereMethod) {
                    $query->where('vehicle_type', 'VmanageVehicle')
                        ->whereHas('vehicleFromVmanageVehicle', function ($subquery) use ($parameter, $value, $whereMethod) {
                            $subquery->where($parameter, $whereMethod, '%' . $value);
                        });
                });
        });
    }

    public function scopeVehicleExistsLikeStart($query, $parameter, $value, $whereType = 'orWhere', $whereMethod = 'like')
    {
        return $query->$whereType(function ($query) use($parameter, $value, $whereMethod){
            $query->where(function ($query)  use($parameter, $value, $whereMethod){
                $query->where('vehicle_type', 'Vehicles')
                    ->whereHas('vehicleFromVehicle', function($subquery) use($parameter, $value, $whereMethod){
                        $subquery->where($parameter, $whereMethod, $value.'%');
                    });
            })
                ->orWhere(function ($query) use($parameter, $value, $whereMethod) {
                    $query->where('vehicle_type', 'VmanageVehicle')
                        ->whereHas('vehicleFromVmanageVehicle', function ($subquery) use ($parameter, $value, $whereMethod) {
                            $subquery->where($parameter, $whereMethod, $value.'%');
                        });
                });
        });
    }

    public function scopeVehicleOwnerGroup($query, $parameters)
    {
        return $query->where(function ($query) use($parameters){
            $query->where(function ($query)  use($parameters){
                $query->where('vehicle_type', 'Vehicles')
                    ->whereHas('vehicleFromVehicle', function($subquery) use($parameters){
                        $subquery->whereHas('owner', function($q2) use($parameters)
                        {
                            $q2->whereHas('group', function($q3) use($parameters)
                            {
                                $q3->whereIn('id', $parameters);
                            });
                        });
                    });
            })->orWhere(function ($query) use($parameters) {
                $query->where('vehicle_type', 'VmanageVehicle')
                    ->whereHas('vehicleFromVmanageVehicle', function ($subquery) use ($parameters) {
                        $subquery->whereHas('owner', function($q2) use($parameters)
                        {
                            $q2->whereHas('group', function($q3) use($parameters)
                            {
                                $q3->whereIn('id', $parameters);
                            });
                        });
                    });
            });
        });
    }

    public function scopeVehicleOwnerColumn($query, $column , $value)
    {
        return $query->where(function ($query) use($column, $value){
            $query->where(function ($query)  use($column, $value){
                $query->where('vehicle_type', 'Vehicles')
                    ->whereHas('vehicleFromVehicle', function($query) use($column, $value){
                        $query->whereHas('owner', function($query) use($column, $value)
                        {
                            $query->where($column, $value);
                        });
                    });
            })->orWhere(function ($query) use($column, $value) {
                $query->where('vehicle_type', 'VmanageVehicle')
                    ->whereHas('vehicleFromVmanageVehicle', function ($query) use ($column, $value) {
                        $query->whereHas('owner', function($query) use($column, $value)
                        {
                            $query->where($column, $value);
                        });
                    });
            });
        });
    }

    public function scopeVehicleOwnerData($query, $column , $value)
    {
        return $query->where(function ($query) use($column, $value){
            $query->where(function ($query)  use($column, $value){
                $query->where('vehicle_type', 'Vehicles')
                    ->whereHas('vehicleFromVehicle', function($query) use($column, $value){
                        $query->whereHas('owner', function($query) use($column, $value)
                        {
                            $query->whereHas('data', function($query) use($column, $value)
                            {
                                $query->where('parameter_id', $column)->where('value', $value);
                            });
                        });
                    });
            })->orWhere(function ($query) use($column, $value) {
                $query->where('vehicle_type', 'VmanageVehicle')
                    ->whereHas('vehicleFromVmanageVehicle', function ($query) use ($column, $value) {
                        $query->whereHas('owner', function($query) use($column, $value)
                        {
                            $query->whereHas('data', function($query) use($column, $value)
                            {
                                $query->where('parameter_id', $column)->where('value', $value);
                            });
                        });
                    });
            });
        });
    }

    public function scopeVehicleOwner($query,  $values)
    {
        return $query->where(function ($query) use( $values){
            $query->where(function ($query)  use($values){
                $query->where('vehicle_type', 'Vehicles')
                    ->whereHas('vehicleFromVehicle', function($query) use($values){
                        $query->whereIn('owner_id', $values);
                    });
            })->orWhere(function ($query) use($values) {
                $query->where('vehicle_type', 'VmanageVehicle')
                    ->whereHas('vehicleFromVmanageVehicle', function ($query) use ($values) {
                        $query->whereIn('owner_id', $values);
                    });
            });
        });
    }

    public function vehicleFromVehicle()
    {
        return $this->belongsTo('Vehicles', 'vehicle_id');
    }

    public function vehicleFromVmanageVehicle()
    {
        return $this->belongsTo('VmanageVehicle', 'vehicle_id')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo('Clients');
    }

    public function driver()
    {
        return $this->belongsTo('Drivers');
    }

    public function offender()
    {
        return $this->belongsTo('Offenders');
    }
    public function receive()
    {
        return $this->belongsTo('Receives');
    }
    public function invoicereceive()
    {
        return $this->belongsTo('Invoicereceives', 'invoicereceives_id');
    }
    public function type_incident()
    {
        return $this->belongsTo('Type_incident');
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo('Branch')->withTrashed();
    }

    public function originalBranch()
    {
        return $this->belongsTo('Branch','original_branch_id')->withTrashed();
    }

    public function branches()
    {
        return $this->hasMany('InjuryBranch')->orderBy('created_at', 'desc');
    }

    public function getInfo()
    {
        return $this->belongsTo('Text_contents', 'info');
    }

    public function getRemarks()
    {
        return $this->belongsTo('Text_contents', 'remarks');
    }

    public function chat()
    {
        return $this->hasMany('InjuryChat', 'injury_id');
    }

    public function invoices()
    {
        return $this->hasMany('InjuryInvoices', 'injury_id');
    }

    public function activeInvoices()
    {
        return $this->hasMany('InjuryInvoices', 'injury_id')->where('active', 0);
    }

    public function compensations()
    {
        return $this->hasMany('InjuryCompensation', 'injury_id');
    }

    public function estimates()
    {
        return $this->hasMany('InjuryEstimate', 'injury_id');
    }

    public function wreck()
    {
        return $this->hasOne('InjuryWreck', 'injury_id')->where('active', 1);
    }

    public function previousWrecks()
    {
        return $this->hasMany('InjuryWreck', 'injury_id')->whereNull('scrapped')->where('active', 0);
    }

    public function totalRepair()
    {
        return $this->hasOne('InjuryTotalRepair', 'injury_id');
    }

    public function totalStatus()
    {
        return $this->belongsTo('InjuryTotalStatuses', 'total_status_id');
    }

    public function theft()
    {
        return $this->hasOne('InjuryTheft', 'injury_id');
    }

    public function theftStatus()
    {
        return $this->belongsTo('InjuryTheftStatuses', 'theft_status_id');
    }

    public function documents()
    {
        return $this->hasMany('InjuryFiles', 'injury_id');
    }

    public function getDocument($type, $category)
    {
        return $this->documents()->whereType($type)->whereCategory($category);
    }

    public function scopeDocument($query, $type, $category)
    {
        return $query->documents()->whereType($type)->whereCategory($category);
    }

    public function damages()
    {
        return $this->hasMany('InjuryDamage', 'injury_id');
    }

    public function insuranceCompany()
    {
        return $this->belongsTo('Insurance_companies', 'insurance_company_id');
    }

    public function injuryPolicy()
    {
        return $this->belongsTo('InjuryPolicy');
    }

    /**
     * @param $wayCompare
     * @return bool
     */
    public function checkIfWreckAlert($wayCompare)
    {
        if($this->wreck)
        {
            if( dateCompareAlert($wayCompare, $this->wreck->alert_repurchase, $this->wreck->alert_repurchase_confirm) )
                return true;

            if( dateCompareAlert($wayCompare,  $this->wreck->alert_buyer, $this->wreck->alert_buyer_confirm ) )
                return true;

            if( dateCompareAlert($wayCompare,  $this->wreck->pro_forma_request, $this->wreck->pro_forma_request_confirm ) )
                return true;

            if( dateCompareAlert($wayCompare,  $this->wreck->payment, $this->wreck->payment_confirm ) )
                return true;

            if( dateCompareAlert($wayCompare,  $this->wreck->invoice_request, $this->wreck->invoice_request_confirm ) )
                return true;

            return false;
        }else
            return false;
    }

    public function checkIfRepairAlert($wayCompare)
    {
        if($this->totalRepair)
        {
            if(dateCompareAlert($wayCompare, $this->totalRepair->alert_receive, $this->totalRepair->alert_receive_confirm) )
                return true;

            return false;
        }else
            return false;
    }

    public function checkIfTheftAlert($wayCompare)
    {
        if($this->theft)
        {
            if( dateCompareAlert($wayCompare, $this->theft->send_zu, $this->theft->send_zu_confirm) )
                return true;

            if( dateCompareAlert($wayCompare,  $this->theft->police_memo, $this->theft->police_memo_confirm ) )
                return true;

            if( dateCompareAlert($wayCompare,  $this->theft->redemption_investigation, $this->theft->redemption_investigation_confirm ) )
                return true;

            if( dateCompareAlert($wayCompare,  $this->theft->deregistration_vehicle, $this->theft->deregistration_vehicle ) )
                return true;

            if( dateCompareAlert($wayCompare,  $this->theft->compensation_payment, $this->theft->compensation_payment_confirm ) )
                return true;

            if( $this->vehicle->gap == 1 && dateCompareAlert($wayCompare,  $this->theft->gap, $this->theft->gap_confirm ) )
                return true;

            return false;
        }else
            return false;
    }

    /**
     * @param $wayCompare
     * @return array
     */
    public function getWreckAlerts($wayCompare)
    {
        $result = array();
        if($this->wreck)
        {
            $preExp = ($wayCompare == '==') ? 'Dzisiaj mija ' : 'Minął ';
            if( dateCompareAlert($wayCompare, $this->wreck->alert_repurchase, $this->wreck->alert_repurchase_confirm) )
                $result[] = $preExp.'termin napłynięcia deklaracji.';

            if( dateCompareAlert($wayCompare,  $this->wreck->alert_buyer, $this->wreck->alert_buyer_confirm ) )
                $result[] = $preExp.'termin zwrotu potwierdzenia odkupu.';

            if( dateCompareAlert($wayCompare,  $this->wreck->pro_forma_request, $this->wreck->pro_forma_request_confirm ) )
                $result[] = $preExp.'termin wystawienia faktury pro forma.';

            if( dateCompareAlert($wayCompare,  $this->wreck->payment, $this->wreck->payment_confirm ) )
                $result[] = $preExp.'termin płatności.';

            if( dateCompareAlert($wayCompare,  $this->wreck->invoice_request, $this->wreck->invoice_request_confirm ) )
                $result[] = $preExp.'wystawienia faktury.';

        }
        return $result;
    }

    public function getRepairAlerts($wayCompare)
    {
        $result = array();
        if($this->totalRepair) {
            $preExp = ($wayCompare == '==') ? 'Dzisiaj mija ' : 'Minął ';

            if( dateCompareAlert($wayCompare, $this->totalRepair->alert_receive, $this->totalRepair->alert_receive_confirm) )
                $result[] = $preExp.'termin napłynięcia wniosku o naprawę.';

        }
        return $result;
    }

    public function getTheftAlerts($wayCompare)
    {
        $result = array();
        if($this->theft)
        {
            $preExp = ($wayCompare == '==') ? 'Dzisiaj mija ' : 'Minął ';
            if( dateCompareAlert($wayCompare, $this->theft->send_zu, $this->theft->send_zu_confirm) )
                $result[] = $preExp.'termin zgłoszenia do ZU.';

            if( dateCompareAlert($wayCompare,  $this->theft->police_memo, $this->theft->police_memo_confirm ) )
                $result[] = $preExp.'termin wystawienia notatki policyjnej.';

            if( dateCompareAlert($wayCompare,  $this->theft->redemption_investigation, $this->theft->redemption_investigation_confirm ) )
                $result[] = $preExp.'termin umorzenia dochodzenia.';

            if( dateCompareAlert($wayCompare,  $this->theft->deregistration_vehicle, $this->theft->deregistration_vehicle_confirm ) )
                $result[] = $preExp.'wyrejestrowania pojazdu.';

            if( dateCompareAlert($wayCompare,  $this->theft->compensation_payment, $this->theft->compensation_payment_confirm ) )
                $result[] = $preExp.'wypłaty odszkodowania.';

            if( $this->vehicle->gap == 1 && dateCompareAlert($wayCompare,  $this->theft->gap, $this->theft->gap ) )
                $result[] = $preExp.'wypłaty GAP.';

        }
        return $result;
    }

    public function historyEntries()
    {
        return $this->hasMany('InjuryHistory', 'injury_id');
    }

    public function stepHistory()
    {
        return $this->hasMany('InjuryStepHistory', 'injury_id');
    }



    public function generated_authorizations()
    {
        return $this->hasMany('InjuryFiles', 'injury_id')->whereType(3)->whereIn('category', [
            2,3,7,8,26,27,31,32
        ]);
    }

    public function uploaded_invoices()
    {
        return $this->hasMany('InjuryFiles', 'injury_id')->whereType(2)->whereIn('category', [
            3,4
        ]);
    }

    public function date_total_status()
    {
        return $this->hasMany('InjuryHistory', 'injury_id')->where('history_type_id', 30);
    }

    public function date_finished_status()
    {
        return $this->hasMany('InjuryHistory', 'injury_id')->where('history_type_id', 114);
    }

    public function status()
    {
        return $this->belongsTo('InjurySteps', 'step');
    }

    public function totalStatusesHistory()
    {
        return $this->morphedByMany('InjuryTotalStatuses', 'status', 'injury_statuses_history')->withTimestamps();
    }

    public function theftStatusesHistory()
    {
        return $this->morphedByMany('InjuryTheftStatuses', 'status', 'injury_statuses_history')->withTimestamps();
    }

    public function stageHistories()
    {
        return $this->hasMany('InjuryStepStageHistory');
    }

    public function stepStage(){
        return $this->belongsTo('InjuryStepStage', 'injury_step_stage_id');
    }

    public function totalStepStage(){
        return $this->belongsTo('InjuryStepStage', 'injury_total_step_stage_id');
    }

    public function repairStages()
    {
        return $this->hasMany('InjuryRepairStage');
    }

    public function currentRepairStage()
    {
        return $this->belongsTo('InjuryRepairStage', 'current_injury_repair_stage_id');
    }

    public function injuryGap()
    {
        return $this->hasOne('InjuryGap', 'injury_id');
    }

    public function contractStatus()
    {
        return $this->belongsTo('ContractStatus', 'contract_status_id');
    }

    public function edb()
    {
        return $this->hasMany('InjuryFiles', 'injury_id')->whereType(3)->whereIn('category', [
            6, 49, 52, 60
        ]);
    }

    public function changes()
    {
        return $this->hasMany('InjuryChange');
    }

    public function original($param){
        $find=$this->changes()->where('name',$param)->orderBy('created_at')->first();
        if($find)
          return $find->value;
        else
          return $this->$param;
    }

    public function injuryCessionAmount()
    {
        return $this->hasOne('InjuryCessionAmount', 'injury_id');
    }

//    public function getStanszkAttribute()
//    {
//        if( //WYPŁACONE
//            $this->notes()->where('referenceable_type' , 'InjuryInvoices')->count() > 0
//        ){
//            return 2;
//        }elseif( //ODMOWA
//        $this->documents()->where('document_type', 'InjuryUploadedDocumentType')->whereIn('document_id', [25,26,27,28,29,30,31,32,33,34,35,36] )->first()
//        ){
//            return 3;
//        }elseif( //W TOKU
//            !$this->documents()->where('document_type', 'InjuryUploadedDocumentType')->where('document_id', 6 )->first()
//            ||
//             in_array($this->sap_rodzszk, ['TOT', 'KRA'])
//        ){
//            return 1;
//        }elseif( // WYPŁACONE
//            $this->documents()->where('document_type', 'InjuryUploadedDocumentType')->where('document_id', 6 )->first()
//            &&
//            ! in_array($this->sap_rodzszk, ['TOT', 'KRA'])
//        ){
//            return 2;
//        }
//
//        return null;
//    }

    public function repairInformation()
    {
        return $this->belongsTo('RepairInformation', 'il_repair_info');
    }

    public function notes()
    {
        return $this->hasMany('InjuryNote')->orderBy('id', 'desc');
    }

    public function sap()
    {
        return $this->hasOne('InjurySapEntity', 'injury_id');
    }

    public function sapPremiums()
    {
        return $this->hasMany('InjurySapPremium', 'injury_id');
    }

    public function tasks()
    {
        return $this->belongsToMany('Task', 'task_injury')->withTimestamps()->orderBy('created_at', 'desc');
    }

    public function forwardedInvoices() {
        return $this->activeInvoices()->where(function($query){
            $query->where('injury_invoice_status_id', 1);
            $query->orWhere('injury_invoice_status_id', 3);
        });
    }

    public function setInjuryNrAttribute($value)
    {
        $this->attributes['injury_nr'] = mb_strtoupper($value, 'UTF-8');
    }
}
