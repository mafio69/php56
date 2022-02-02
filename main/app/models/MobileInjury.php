<?php

class MobileInjury extends Eloquent
{
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;
    protected $table = 'mobile_injury';

    protected $fillable = array(
        'registration',
        'nr_contract',
        'nip',
        'name_client',
        'adres_client',
        'city_client',
        'code_client',
        'nip',
        'notifier_surname',
        'notifier_name',
        'notifier_phone',
        'notifier_email',
        'injuries_type',
        'injuries_type_id',
        'marka',
        'model',
        'rdl',
        'ipl',
        'name_zu',
        'date_event',
        'event_city',
        'lat',
        'lng',
        'nr_injurie',
        'desc_event',
        'location_upl',
        'nr_case',
        'policeman_phone',
        'company',
        'active',
        'source',
        'injury_token',
        'number_repeat',
        'confirmation_token',
        'injury_id',
        'if_on_as_server',
        'new',
        'created_at'
    );

    protected $guarded = array();

    public function files()
    {
        return $this->hasMany('MobileInjuryFile', 'mobile_injury_id');
    }

    public function damages()
    {
        return $this->hasMany('MobileInjuryDamage', 'mobile_injury_id');
    }
    public function injuries_type()
    {
        return $this->belongsTo('Injuries_type', 'injuries_type_id');
    }

    public function generateSearchParameters()
    {
        $parameters = [
            'registration' => $this->registration,
            'contract_number' => $this->nr_contract
        ];

        if($this->nip != '')
        {
            $parameters['nip_company'] = $this->nip;
        }

        if($this->name_client != '')
        {
            $parameters['name_company'] = $this->name_client;
        }

        return $parameters;
    }

    public function damagesA()
    {
        $damagesA = [];
        foreach($this->damages as $damage)
        {
            $damagesA[$damage->mobile_damage_type_id] = 1;
        }

        return $damagesA;
    }

    public function description()
    {
        $description = preg_replace('/\<br(\s*)?\/?\>/i','&#13;&#10;',$this->desc_event);

        $description .= '&#13;&#10;&#13;&#10;Dane klienta: '.$this->name_client.'; '.$this->code_client.' '.$this->city_client.', '.$this->adres_client;
        if($this->nip != '')
            $description .= '; NIP:'.$this->nip;

        if($this->company != '')
        {
            $description .= '&#13;&#10;&#13;&#10;Warsztat: '.$this->company;
        }
        if($this->injuries_type > 0)
        {
            $description .= '&#13;&#10;&#13;&#10;Typ szkody: ';
            switch ($this->injuries_type){
                case 2:
                    $description .='komunikacyjna OC';
                    break;
                case 1:
                    $description .='komunikacyjna AC';
                    break;
                case 3:
                    $description .='komunikacyjna kradzież';
                    break;
                case 4:
                    $description .='majątkowa';
                    break;
                case 5:
                    $description .='majątkowa kradzież';
                    break;
                case 6:
                    $description .='komunikacyjna AC - Regres';
                    break;
            }

        }

        if($this->name_zu != '')
        {
            $description .= '&#13;&#10;&#13;&#10;Ubezpieczyciel: '.$this->name_zu;
        }

        return $description;
    }


    public function police()
    {
        if($this->police_unit != '' || $this->nr_case != '' || $this->policeman_phone)
        {
            $police = 1;
        }else{
            $police = -1;
        }

        return $police;
    }

    public function tasks()
    {
        return $this->morphMany('Task', 'source');
    }
}
