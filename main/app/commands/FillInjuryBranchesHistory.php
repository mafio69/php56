<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FillInjuryBranchesHistory extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:fill-injury-branches-history';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		Injury::whereHas('historyEntries', function($query){
		    $query->where('history_type_id', 31);
        }, '=' , 1)->chunk(1000, function($injuries){
            $injuries->load('historyEntries');
            foreach ($injuries as $injury){
                $history = $injury->historyEntries->filter(function($entry){
                    return $entry->history_type_id == 31;
                })->first();

                $injuryBranch = new InjuryBranch();

                $injuryBranch->injury_id = $injury->id;
                $injuryBranch->branch_id = $injury->branch_id;
                $injuryBranch->user_id = $history->user_id;
                $injuryBranch->created_at = $history->created_at;
                $injuryBranch->updated_at = $history->updated_at;

                $injuryBranch->save(['timestamps' => false]);
            }
        });

        Injury::whereHas('historyEntries', function($query){
            $query->where('history_type_id', 31);
        }, '>' , 1)->chunk(100, function($injuries){
            $injuries->load('historyEntries', 'historyEntries.injury_history_content');

            foreach($injuries as $injury)
            {
                $entries = $injury->historyEntries->filter(function($entry){
                    return $entry->history_type_id == 31;
                })->all();

                foreach($entries as $i => $entry)
                {
                    if( ++$i == count($entries)){
                        if($injury->branch_id > 0){
                            $injuryBranch = new InjuryBranch();

                            $injuryBranch->injury_id = $injury->id;
                            $injuryBranch->branch_id = $injury->branch_id;
                            $injuryBranch->user_id = $entry->user_id;
                            $injuryBranch->created_at = $entry->created_at;
                            $injuryBranch->updated_at = $entry->updated_at;

                            $injuryBranch->save(['timestamps' => false]);
                        }
                    }elseif($entry->value == '-1'){
                        $content = explode('->', $entry->injury_history_content->content);

                        if(isset($content[1]) && trim($content[1]) != '' ){
                            $branch = Branch::where('short_name', trim($content[1]))->first();
                            if( $branch ){
                                $injuryBranch = new InjuryBranch();

                                $injuryBranch->injury_id = $injury->id;
                                $injuryBranch->branch_id = $branch->id;
                                $injuryBranch->user_id = $entry->user_id;
                                $injuryBranch->created_at = $entry->created_at;
                                $injuryBranch->updated_at = $entry->updated_at;

                                $injuryBranch->save(['timestamps' => false]);
                            }
                        }
                    }
                }
            }
        });
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
		);
	}

}
