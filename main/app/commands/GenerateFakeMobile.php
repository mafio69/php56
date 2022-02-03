<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateFakeMobile extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'debug:fake-mobile';

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
		MobileInjury::where('active', '=', '0')
            ->where(function($query){
                $query->where('source', 0);
                $query->orWhereIn('injuries_type', [2,1,3,6]);
            })->latest('id')->limit(10)->get()->each(function($injury){
            $group_name = '';
            if( ($injury->source == 0 || $injury->source == 3)  && $injury->injuries_type()->first()) {
                $group_name = $injury->injuries_type()->first()->name;
            }else {
                if ($injury->injuries_type == 2)
                    $group_name = 'komunikacyjna OC';
                elseif($injury->injuries_type == 1)
                    $group_name = 'komunikacyjna AC';
                elseif($injury->injuries_type == 3)
                    $group_name = 'komunikacyjna kradzież';
                elseif($injury->injuries_type == 4)
                    $group_name = 'majątkowa';
                elseif($injury->injuries_type == 5)
                    $group_name = 'majątkowa kradzież';
                elseif($injury->injuries_type == 6)
                    $group_name = 'komunikacyjna AC - Regres';
            }

            if (strpos($group_name, 'kradzież') !== false) {
                $task_group_id = 3;
            }else{
                $task_group_id = 1;
            }

            $task = Task::create([
                'task_source_id' => 2, //druk online
                'from_email' => $injury->notifier_email,
                'from_name' => $injury->notifier_name.' '.$injury->notifier_surname,
                'subject' => $injury->nr_contract.' # '.$injury->registration,
                'content' => $injury->description(),
                'task_group_id' => $task_group_id,
                'task_date' => $injury->created_at
            ]);

            $injury->tasks()->save($task);

            \Idea\Tasker\Tasker::assign($task);
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
