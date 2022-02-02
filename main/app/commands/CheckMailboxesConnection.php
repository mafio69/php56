<?php
ini_set('display_errors', 0);
error_reporting(0);

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckMailboxesConnection extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:check-mailboxes-connection';

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
	    $mailbox_id = $this->argument('mailbox');

	    if($mailbox_id){
	        $mailbox = TaskMailbox::find($mailbox_id);
            $this->checkMailbox($mailbox);
        }else {
            TaskMailbox::get()->each(function ($mailbox) {
                $this->checkMailbox($mailbox);
            });
        }
	}

    /**
     * @param $mailbox
     */
    protected function checkMailbox($mailbox)
    {
        $server = new Fetch\Server($mailbox->server);
        $server->setAuthentication($mailbox->login, Crypt::decrypt($mailbox->password));

        try {
            $server->getImapStream();
            $mailbox->update(['is_valid' => 1]);
        } catch (RuntimeException $exception) {
            $this->error($exception->getTraceAsString());
            $mailbox->update(['is_valid' => 0]);
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
		    [
		        'mailbox', InputArgument::OPTIONAL, 'Mailbox ID'
            ]
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
