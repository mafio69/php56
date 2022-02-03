<?php


use Illuminate\Database\Eloquent\SoftDeletingTrait;

class LeasingAgreementFile extends Eloquent
{
    use SoftDeletingTrait;
    protected $fillable = ['leasing_agreement_id', 'type', 'category', 'user_id', 'file', 'name', 'nr', 'value', 'active'];
    public $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function agreement()
    {
        return $this->belongsTo('LeasingAgreement', 'leasing_agreement_id');
    }

    public function category_object()
    {
        return $this->belongsTo('LeasingAgreementDocumentType', 'category');
    }

}
