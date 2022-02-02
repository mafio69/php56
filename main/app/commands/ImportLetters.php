<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Fetch\Server;

class ImportLetters extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'letters:import';

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
		$server = new Server(Config::get('mail.host'));
		$server->setAuthentication(Config::get('mail.username'), Config::get('mail.password'));
		$messages = $server->search('UNSEEN', 100);

		foreach ($messages as $message) {
			$attachments = $message->getAttachments();
			if(is_array($attachments)){
				foreach ($attachments as $attachment) {
					$ext = (new SplFileInfo($attachment->getFileName()))->getExtension();
					$filename = time() . rand(1000, 9999);
					if($ext != '') $filename = $filename . '.' . $ext;

					$attachment->saveAs(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').'/files/' . $filename);
					InjuryLetter::create([
						'file' => $filename,
						'is_unprocessed' => 1
					]);
				}
			}
		}

		$server->expunge();
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
