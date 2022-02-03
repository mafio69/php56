<?php
class LeasingAgreementReport extends \Eloquent {

    protected $fillable = [
        'type',
        'insurance_company_id',
        'owner_id',
        'if_trial',
        'insurances_global_nr',
        'version',
        'filename',
        'user_id',
        'refunds_type_id',
        'general_contract',
        'if_foreign_policy',
        'if_sk'
    ];

    public function user()
    {
        return $this->belongsTo('User','user_id')->withTrashed();
    }

    public function insurance_company()
    {
        return $this->belongsTo('Insurance_companies');
    }

    public function owner()
    {
        return $this->belongsTo('Owners');
    }

    public function getPathToFileAttribute()
    {
        $pathToFile = \Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/reports/generated/'.$this->filename.'.xls';

        if(file_exists($pathToFile))
            return $pathToFile;

        return null;
    }
}