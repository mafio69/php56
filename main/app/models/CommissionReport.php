<?php

class CommissionReport extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletingTrait;
    protected $table = 'commission_reports';
    protected $fillable = [
    	'test_report_id',
    	'commission_step_id',
        'user_id',
	    'filename',
        'filename_settled',
        'filename_accounting',
	    'report_number',
	    'is_trial',
	    'is_individual',
	    'is_uptodate'
    ];
    protected $dates = ['deleted_at'];

	public static function boot()
	{
		parent::boot();

		CommissionReport::creating(function($report)
		{
			if($report->is_trial == 1 ) $is_trial = 'T'; else $is_trial = 'P';
			if($report->is_individual == 1 ) $is_individual = 'I'; else $is_individual = 'G';

			$latestReport = CommissionReport::where('is_trial', $report->is_trial)->where('is_individual', $report->is_individual)->orderBy('id', 'desc')->first();
			if(! $latestReport){
				$report_number = $is_trial.'/'.$is_individual.'/1/'.date('Y');
			}else{
				$report_number_parts = explode('/', $latestReport->report_number);
				if($report_number_parts[3] != date('Y'))
				{
					$report_number = $is_trial.'/'.$is_individual.'/1/'.date('Y');
				}else{
					$report_number = $is_trial.'/'.$is_individual.'/'.($report_number_parts[2] + 1).'/'.date('Y');
				}
			}

			$report->report_number = $report_number;
		});
	}

    public function user()
    {
    	return $this->belongsTo('User');
    }

    public function commissions()
    {
    	return $this->hasMany('Commission', 'commission_individual_report_id');
    }

    public function testReport()
    {
    	return $this->belongsTo('CommissionReport', 'test_report_id');
    }
}
