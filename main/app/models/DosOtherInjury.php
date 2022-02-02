<?php

class DosOtherInjury extends Eloquent
{
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

    protected $table = 'dos_other_injury';

    protected $guarded = array();

    protected $dates = ['deleted_at'];

    public static function boot(){
        parent::boot();

        static::addGlobalScope(new \Idea\Scopes\DosOtherInjuryUserManageableScope());
    }


    public function injuries_type()
    {
        return $this->belongsTo('DosInjuryType', 'injuries_type_id')->withTrashed();
    }

    public function object()
    {
        return $this->belongsTo('Objects');
    }
    public function client()
    {
        return $this->belongsTo('Clients');
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
        return $this->belongsTo('DosOtherInjuryTypeIncident');
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
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
        return $this->hasMany('DosOtherInjuryChat', 'injury_id');
    }

    public function invoices()
    {
        return $this->hasMany('DosOtherInjuryInvoices', 'injury_id');
    }

    public function documents()
    {
        return $this->hasMany('DosOtherInjuryFiles', 'injury_id');
    }

    public function getDocument($type, $category)
    {
        return $this->documents()->whereType($type)->whereCategory($category);
    }

    public function theft()
    {
        return $this->hasOne('DosOtherInjuryTheft', 'injury_id');
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

            if( $this->object->gap == 1 && dateCompareAlert($wayCompare,  $this->theft->gap, $this->theft->gap_confirm ) )
                return true;

            return false;
        }else
            return false;
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

            if( $this->object->gap == 1 && dateCompareAlert($wayCompare,  $this->theft->gap, $this->theft->gap ) )
                $result[] = $preExp.'wypłaty GAP.';

        }
        return $result;
    }


    public function uploaded_invoices()
    {
        return $this->hasMany('DosOtherInjuryFiles', 'injury_id')->whereType(2)->whereIn('category', [
            3,4
        ]);
    }

    public function date_total_status()
    {
        return $this->hasMany('DosOtherInjuryHistory', 'injury_id')->where('history_type_id', 30);
    }

    public function date_finished_status()
    {
        return $this->hasMany('DosOtherInjuryHistory', 'injury_id')->where('history_type_id', 114);
    }

    public function historyEntries()
    {
        return $this->hasMany('DosOtherInjuryHistory', 'injury_id');
    }

    public function leader()
    {
        return $this->belongsTo('User', 'leader_id')->withTrashed();
    }

    public function status()
    {
        return $this->belongsTo('DosOtherInjurySteps', 'step');
    }

    public function compensations()
    {
        return $this->hasMany('DosOtherInjuryCompensation', 'injury_id')->orderBy('id', 'asc');
    }

    public function scopeFilter($query,$request){

        if(Session::get('search.injury_type', '0') != 0)
            $query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

        if(Session::get('search.user_id', '0') != 0)
            $query ->where('user_id', '=', Session::get('search.user_id') );

        if(Session::get('search.leader_id', '0') != 0)
            $query ->where('leader_id', '=', Session::get('search.leader_id') );

        if(Session::get('search.locked_status', '0') == 1)
            $query ->whereIn('locked_status', array(5, '-5'));

        //czy ustawione jest filtrowanie wyszukiwaniem
        if($request->has('term')){

            $query->where(function($query) use($request){

                if(Input::has('case_nr')){
                    $query -> orWhere('case_nr', 'like', '%'.$request->get('term').'%');
                }

                if($request->has('injury_nr')){
                    $query -> orWhere('injury_nr', 'like', '%'.$request->get('term').'%');
                }

                if($request->has('leasing_nr')){
                    $query -> orWhereHas('object', function($q) use($request)
                    {
                        $q -> where('nr_contract', 'like', '%'.$request->get('term').'%');
                    });
                }

                if($request->has('address')){
                    $query -> orWhere('event_city', 'like', '%'.$request->get('term').'%');
                    $query -> orWhere('event_post', 'like', '%'.$request->get('term').'%');
                    $query -> orWhere('event_street', 'like', '%'.$request->get('term').'%');
                }

                if($request->has('surname')){
                    $query -> orWhere('notifier_surname', 'like', '%'.$request->get('term').'%');
                }

                if($request->has('client')){
                    $query -> orWhereHas('client', function($q) use($request)
                    {
                        $q -> where('name', 'like', '%'.$request->get('term').'%');
                    });
                }

                if($request->has('firmID')){
                    $query -> orWhereHas('client', function($q) use($request)
                    {
                        $q -> where('firmID', 'like', '%'.$request->get('term').'%');
                    });
                }

                if($request->has('NIP')){
                    $query -> orWhereHas('client', function($q) use($request)
                    {
                        $q -> where('NIP', 'like', '%'.$request->get('term').'%');
                    });
                }

            });
        }

        return $query;
    }
}
