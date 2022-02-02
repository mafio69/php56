<?php


class EaInjury extends Eloquent
{
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

    protected $table = 'ea_injuries';

    protected $fillable = array(
        'injury_id',
        'vehicle_id',
        'vehicle_type',
        'workshop_id',

        'vehicle_vin',
        'vehicle_registration',
        'vehicle_brand',
        'vehicle_model',
        'vehicle_engine_capacity',
        'vehicle_year_production',
        'vehicle_first_registration',
        'vehicle_mileage',

        'owner_name',
        'client_name',

        'contract_number',
        'contract_end_leasing',
        'contract_status',
        'insurance_company_name',
        'insurance_expire_date',
        'insurance_policy_number',
        'insurance_amount',
        'insurance_own_contribution',
        'insurance_net_gross',
        'insurance_assistance',
        'insurance_assistance_name',
        'driver_name',
        'driver_surname',
        'driver_phone',
        'driver_email',
        'driver_city',
        'claimant_name',
        'claimant_surname',
        'claimant_phone',
        'claimant_email',
        'claimant_city',
        'injury_event_date',
        'injury_event_time',
        'injury_event_city',
        'injury_event_street',
        'injury_type_incident_id',
        'injury_event_description',
        'injury_damage_description',
        'injury_current_location',
        'injury_reported_insurance_company',
        'injury_type',
        'injury_number',
        'injury_insurance_company',
        'injury_police_notified',
        'injury_police_number',
        'injury_police_unit',
        'injury_police_contact',
        'injury_statement',
        'injury_taken_registration',
        'injury_towing',
        'injury_replacement_vehicle',
        'injury_vehicle_in_service',

        'case_number',
        'sales_program'
    );

    protected $dates = ['deleted_at'];


    public function description()
    {
        $description = 'Opis okoliczności szkody: '.$this->simplifyString($this->injury_event_description);

        $description .='&#13;&#10;&#13;&#10;';
        $description .= 'Opis uszkodzeń: '.$this->simplifyString($this->injury_damage_description);

        $description .='&#13;&#10;&#13;&#10;';
        $description .= 'Aktualna pozycja pojazdu: '.$this->simplifyString($this->injury_current_location);

        $description .='&#13;&#10;&#13;&#10;';
        $description .= 'Typ szkody: '.$this->simplifyString($this->injury_type);

        $description .='&#13;&#10;&#13;&#10;';
        $description .= 'ZU: '.$this->simplifyString($this->injury_insurance_company);

        return $description;
    }

    private function simplifyString($string)
    {
        return preg_replace('/\<br(\s*)?\/?\>/i','&#13;&#10;', $string);
    }

    public function injury()
    {
        return $this->belongsTo('Injury', 'injury_id');
    }

    public function tasks()
    {
        return $this->morphMany('Task', 'source');
    }
}
