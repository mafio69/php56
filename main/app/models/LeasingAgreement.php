<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class LeasingAgreement extends \Eloquent {
    use SoftDeletingTrait;

	protected $fillable = [
        'client_id',
        'user_id',
        'owner_id',
        'nr_contract',
        'nr_agreement',
        'installments',
        'months',
        'rate',
        'contribution',
        'initial_rate',
        'initial_contribution',
        'loan_net_value',
        'loan_gross_value',
        'leasing_agreement_type_id',
        'status',
        'date_acceptation',
        'net_gross',
        'leasing_agreement_payment_way_id',
        'insurance_from',
        'insurance_to',
        'detect_problem',
        'potential_cession',
        'has_yacht',
        'leasing_agreement_insurance_group_row_id',
        'reported_to_resume',
        'import_insurance_company',
        'archive',
        'withdraw',
        'withdraw_reason_id',
        'withdraw_reason',
        'creating_way',
        'filename',
        'remarks',
        'if_reportable',
        'if_foreign'
    ];
    protected $dates = ['deleted_at'];


    public function client()
    {
        return $this->belongsTo('Clients');
    }

    public function owner()
    {
        return $this->belongsTo('Owners');
    }

    public function leasingAgreementType()
    {
        return $this->belongsTo('LeasingAgreementType');
    }

    public function leasingAgreementPaymentWay()
    {
        return $this->belongsTo('LeasingAgreementPaymentWay');
    }

    public function objects()
    {
        return $this->hasMany('LeasingAgreementObject');
    }

    public function insurances()
    {
        return $this->hasMany('LeasingAgreementInsurance');
    }

    public function refundedInsurances()
    {
        return $this->hasMany('LeasingAgreementInsurance', 'leasing_agreement_id')->where('if_refund_contribution', 1);
    }

    public function activeInsurance()
    {
        return $this->insurances()->active()->first();
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function cessions()
    {
        return $this
            ->belongsToMany('Clients', 'leasing_agreement_cessions', 'leasing_agreement_id', 'client_id')
            ->withPivot(['leasing_agreement_insurance_id', 'current_client_id'])
            ->whereNull('leasing_agreement_cessions.deleted_at') // Table `leasing_agreement_cessions` has column `deleted_at`
            ->withTimestamps(); // Table `leasing_agreement_cessions` has columns: `created_at`, `updated_at`

    }

    public function history()
    {
        return $this->hasMany('LeasingAgreementHistory');
    }

    public function insurance_group_row()
    {
        return $this->belongsTo('LeasingAgreementInsuranceGroupRow', 'leasing_agreement_insurance_group_row_id');
    }

    public function updateLoans()
    {
        $objects = $this->objects()->get();

        $loan_net_value = 0;
        $loan_gross_value = 0;

        foreach($objects as $object)
        {
            $loan_net_value += $object->net_value;
            $loan_gross_value += $object->gross_value;
        }
        $this->loan_net_value = $loan_net_value;
        $this->loan_gross_value = $loan_gross_value;
        $this->save();

    }

    public function files()
    {
        return $this->hasMany('LeasingAgreementFile', 'leasing_agreement_id');
    }


    public function withCurrentResume()
    {
        return $this->hasMany('LeasingAgreementInsurance')->where(function($query){
            $to = \Carbon\Carbon::now()->endOfMonth();
            $from = \Carbon\Carbon::now()->startOfMonth();
            $query->active()->whereBetween('date_to',array($from,$to));
        });
    }

    public function withArchiveResume()
    {
        return $this->hasMany('LeasingAgreementInsurance')->where(function($query){
            $to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth();
            $query->active()->where('date_to', '<', $to);
        });
    }

    public function conversations()
    {
        return $this->hasMany('LeasingAgreementChat');
    }
}
