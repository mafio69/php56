<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FakeTask extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'debug:fake-task';

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
	    for ($i = 0; $i < 10; $i++)
        {
            $task = Task::create([
                'task_source_id' => rand(1, 4),
                'task_type_id' => rand(1, 14),
                'subject' => Str::random(),
                'content' => Str::random().' '.Str::random().' '.Str::random().' '.Str::random(),
            ]);
        }
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
