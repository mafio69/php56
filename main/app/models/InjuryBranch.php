<?php


class InjuryBranch extends Eloquent
{
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

    protected $fillable = [
        'injury_id',
        'branch_id',
        'user_id'
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function branch()
    {
        return $this->belongsTo('Branch')->withTrashed();
    }
}