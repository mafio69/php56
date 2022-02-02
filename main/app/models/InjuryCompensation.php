<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class InjuryCompensation extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'injury_compensations';

    protected $fillable =
        [
        'is_premiumable',
        'injury_note_id',
        'mode',
        'injury_id',
        'injury_files_id',
        'user_id',
        'date_decision',
        'injury_compensation_decision_type_id',
        'receive_id',
        'compensation',
        'net_gross',
        'remarks',
    ];
    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::updated(
            function ($record) {

                $dirty = $record->getDirty();
                $is_data_update = false;

                $info = [];
                foreach ($dirty as $changed_field => $changed_value) {
                    switch ($changed_field) {
                        case 'receive_id':
                            $info[] = 'Odbiorca odszkodowania: ' . (Receives::find($record->getOriginal('receive_id')) ? Receives::find($record->getOriginal('receive_id'))->name : '') . ' -> ' . ($record->receive ? $record->receive->name : '');                           
                            $is_data_update = true;
                            break;
                        case 'injury_compensation_decision_type_id':
                            $info[] = 'Rodzaj decyzji: ' . (InjuryCompensationDecisionType::find($record->getOriginal('injury_compensation_decision_type_id')) ? InjuryCompensationDecisionType::find($record->getOriginal('injury_compensation_decision_type_id'))->name : '') . ' -> ' . ($record->decisionType ? $record->decisionType->name : '');
                            $is_data_update = true;
                            break;
                        case 'net_gross':
                            $info[] = 'Kwota netto/brutto: ' .($record->getOriginal('net_gross') 
                            && $record->getOriginal('net_gross') != ''?
                            Config::get('definition.compensationsNetGross')[$record->getOriginal('net_gross')]:'') . ' -> ' . 
                            Config::get('definition.compensationsNetGross')[$changed_value];
                            $is_data_update = true;
                            break;
                        case 'compensation':
                            $info[] = 'Kwota: ' . $record->getOriginal('compensation') . ' -> ' . $changed_value;
                            $is_data_update = true;
                            break;
                        case 'remarks':
                            $info[] = 'Uwagi: ' . $record->getOriginal('remarks') . ' -> ' . $changed_value;
                            $is_data_update = true;
                            break;
                        case 'date_decision':
                            $info[] = 'Data dacyzji: ' . $record->getOriginal('date_decision') . ' -> ' . $changed_value;
                            $is_data_update = true;
                            break;

                    }
                }
                if($is_data_update)Histories::history($record->injury_id, 155, Auth::user()->id, '-1', implode('; ', $info));
                return true;
            });
    }

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function injury_files()
    {
        return $this->belongsTo('InjuryFiles');
    }

    public function injury_file()
    {
        return $this->belongsTo('InjuryFiles', 'injury_files_id');
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function decisionType()
    {
        return $this->belongsTo('InjuryCompensationDecisionType', 'injury_compensation_decision_type_id')->withTrashed();
    }

    public function receive()
    {
        return $this->belongsTo('Receives');
    }

    public function getModeNameAttribute()
    {
        if ($this->mode == 1)return 'Pierwsza wypłata';
        if ($this->mode == 2) return 'Dopłata';

        return '';
    }

    public function note()
    {
        return $this->belongsTo('InjuryNote', 'injury_note_id');
    }

    public function premium()
    {
        return $this->hasOne('InjurySapPremium', 'injury_compensation_id');
    }

}
