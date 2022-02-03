<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SystemRemoveTasks extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:remove-tasks';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Aktualizacja spraw pod nowe statusy.';

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
        $tasks = Task::select(DB::raw("COUNT(*) count, uid,task_date, task_group_id, created_at"))
            ->groupBy('created_at', 'task_date', 'uid', 'task_group_id')
            ->havingRaw("COUNT(*) > 1")
            ->where('created_at', '>', '2021-12-13 09:44:08')->get();

        foreach($tasks as $task)
        {
            $infectedTasks = Task::where('uid', $task->uid)->where('created_at', $task->created_at)->where('task_date', $task->task_date)->get();
            foreach($infectedTasks as $k => $infectedTask)
            {
                if($infectedTask->currentInstance->task_step_id == 1 && $k > 0){
                    $infectedTask->currentInstance->delete();
                    $infectedTask->delete();
                }else{
                    echo $infectedTask->currentInstance->task_step_id.' '.$infectedTask->id.PHP_EOL;
                }
            }
        }
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
