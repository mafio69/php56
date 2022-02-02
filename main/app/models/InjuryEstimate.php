<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class InjuryEstimate extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'injury_estimates';

    protected $fillable =
        [
            'injury_id',
            'injury_file_id',
            'user_id',
            'net',
            'gross',
            'report',
        ];
    protected $dates = ['deleted_at','created_at'];

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function injury_file()
    {
        return $this->belongsTo('InjuryFiles', 'injury_file_id');
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

}
