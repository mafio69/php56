<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class LeasingAgreementInsurance extends \Eloquent {
    use SoftDeletingTrait;

	protected $fillable = [
        'user_id',
        'leasing_agreement_id',
        'leasing_agreement_insurance_group_row_id',
        'if_foreign_policy',
        'if_cession',
        'if_continuation',
        'if_refund_contribution',
        'if_load_decision',
        'if_dismantled',
        'if_sold',
        'if_rounding',
        'date_dismantled_sold',
        'insurance_number',
        'notification_number',
        'months',
        'leasing_agreement_installment_id',
        'insurance_date',
        'date_from',
        'date_to',
        'contribution',
        'commission',
        'contribution_commission',
        'rate',
        'contribution_lessor',
        'contribution_lessor_currency_id',
        'last_year_lessor_contribution',
        'rate_lessor',
        'rate_vbl',
        'leasing_agreement_insurance_type_id',
        'leasing_agreement_mismatching_reason_id',
        'status',
        'insurance_company_id',
        'leasing_agreement_payment_way_id',
        'refund',
        'refunded_insurance_id',
        'resumed_insurance_id',
        'promo_code',
        'commission_value',
        'commission_date',
        'commission_refund_value',
        'active'
    ];
    protected $dates = ['deleted_at'];

    public function leasingAgreement()
    {
        return $this->belongsTo('LeasingAgreement');
    }

    public function insurance_group_row()
    {
        return $this->belongsTo('LeasingAgreementInsuranceGroupRow', 'leasing_agreement_insurance_group_row_id');
    }

    public function insuranceType()
    {
        return $this->belongsTo('LeasingAgreementInsuranceType','leasing_agreement_insurance_type_id');
    }

    public function insuranceCompany()
    {
        return $this->belongsTo('Insurance_companies');
    }

    public function leasingAgreementPaymentWay()
    {
        return $this->belongsTo('LeasingAgreementPaymentWay');
    }

    public function detectProblem()
    {
        if($this->date_from == '0000-00-00' || $this->date_to == '0000-00-00')
            return true;

        return false;
    }

    public function scopeActive($query)
    {
        return $query->where('active', '=', 1);
    }

    public function refundedInsurance()
    {
        return $this->belongsTo('LeasingAgreementInsurance', 'refunded_insurance_id');
    }

    public function refundInsurance()
    {
        return $this->hasOne('LeasingAgreementInsurance', 'refunded_insurance_id');
    }

    public function resumedInsurance()
    {
        return $this->belongsTo('LeasingAgreementInsurance', 'resumed_insurance_id', 'id');
    }

    public function resumingInsurance()
    {
        return $this->hasOne('LeasingAgreementInsurance', 'resumed_insurance_id');
    }

    public function installments()
    {
        return $this->belongsTo('LeasingAgreementInsuranceInstallment', 'leasing_agreement_installment_id');
    }

    public function coverages()
    {
        return $this->hasMany('LeasingAgreementInsuranceCoverage');
    }

    public function payments()
    {
        return $this->hasMany('LeasingAgreementInsurancePayment');
    }

    public function mismatchingReason()
    {
        return $this->belongsTo('LeasingAgreementMismatchingReason', 'leasing_agreement_mismatching_reason_id');
    }

    public function packages()
    {
        return $this->belongsToMany('LeasingAgreementInsuranceGroupRowPackage', 'leasing_agreement_insurance_package', 'leasing_agreement_insurance_id', 'leasing_agreement_insurance_group_row_package_id');
    }

    public function isMarked()
    {
        $notification_number = explode('/', $this->notification_number);

        if($notification_number[1] > 2018 || ($notification_number[1] == 2018 && $notification_number[0] >= 10))
        {
            return true;
        }

        return false;
    }

    public function generalContract(){
        $groupRow = $this->leasingAgreement->insurance_group_row()->first();
        if(! $groupRow) return null;

        if($this->isMarked() ) {
            if($this->edb){
                return $groupRow->general_contract .' z dnia 10.02.2021 r.';
            }

            if(strpos($groupRow->rate->name, 'zakres ograniczony') !== false  ){
                return 'GL50/000639/18/A z dnia 28 września 2018 roku';
            }

            return 'GL50/000638/18/A z dnia 28 września 2018 roku';
        }

        if(strpos($groupRow->rate->name, 'zakres ograniczony') !== false  ){
            return '31/GLK2/IDEA/2016 z dnia 22 marca 2016 roku';
        }

        return '30/GLK2/IDEA/2015 z dnia 16 grudnia 2015 roku';
    }

    public function getEdbAttribute(){  //ergo hestia edb
        return $this->insurance_company_id == 320;
    }

    public function mark()
    {
        if($this->isMarked()) {
            if($this->edb){
                return 'bg-info';
            }

            return 'bg-danger';
        }

        return '';
    }
}
