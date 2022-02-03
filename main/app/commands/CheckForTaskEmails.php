<?php

use EmailReplyParser\Parser\EmailParser;
use Idea\Tasker\Tasker;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class CheckForTaskEmails extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'system:check-for-task-emails';

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
        $mailboxes = TaskMailbox::where('is_valid', 1)->get();

        foreach($mailboxes as $mailbox)
        {
            $server = new Fetch\Server($mailbox->server);
            $server->setAuthentication($mailbox->login, Crypt::decrypt($mailbox->password));

            if(!$server->hasMailBox('Zadania')){
                $server->createMailBox('Zadania');
            }

            $messages = $server->getMessages();
            foreach($messages as $message)
            {
                try{
                    $this->createTask($message, $server, $mailbox);
                }catch (Symfony\Component\Debug\Exception\FatalErrorException $e){
                    \Log::error('email error',[$message]);
                }catch (Exception $e) {
                    \Log::error('email error',[$message]);
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

    private function createTask($message, $server, $mailbox)
    {
        $subject = $message->getSubject();

        if (in_array(substr($subject, 0, 8),  ['=?UTF-8?', '=?utf-8?'])) {
            $subject = mb_decode_mimeheader($subject);
        } elseif (substr($subject, 0, 13) == '=?iso-8859-2?') {
            $subject = mb_decode_mimeheader($subject);
        }

        $from = $message->getAddresses('from');
        $from_address = (isset($from['address']))?$from['address']:'';
        if ($from_address != ''){
            $from_address = mb_decode_mimeheader($from_address);
            $blackList = TaskBlackList::where('email', $from_address)->get();

            foreach ($blackList as $item)
            {
                if(! $item->topic || strpos( mb_strtoupper( iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $subject ) ), mb_strtoupper( iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $item->topic ) ) ) !== false ){
                    $message->moveToMailBox('Zadania');
                    return;
                }
            }
        }

        $body=imap_body($server->getImapStream(),$message->getUid(),FT_UID);
        $filename= slug(trim($message->getSubject())).time().rand(0, 1000).'.eml';
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/". $filename;
        file_put_contents($path, $message->getRawHeaders().$body);

        try{
            $parser = new \Idea\Tasker\TaskParser($path);
        }catch (Symfony\Component\Debug\Exception\FatalErrorException $e){
            \Log::error('parse error',[$message]);
            $message->moveToMailBox('Zadania');
            return;
        }catch (Exception $e) {
            \Log::error('parse error',[$message]);
            $message->moveToMailBox('Zadania');
            return;
        }

        $task = Task::create([
            'task_source_id' => $mailbox->task_source_id,
            'uid' => $message->getUid(),
            'to_email' => implode(',', $parser->getToAddress()),
            'to_name' => implode(',', $parser->getToName()),
            'from_email' => $parser->getFromAddress(),
            'from_name' => $parser->getFromName(),
            'cc_email' => implode(',',$parser->getCcAddress()),
            'cc_name' => implode(',',$parser->getCcName()),
            'subject' => $parser->getSubject(),
            'content' => $parser->getContent(),
            'task_group_id' => $mailbox->task_group_id,
            'task_date' => $parser->getDate()
        ]);

        $mailbox->tasks()->save($task);

        $task->files()->create([
            'filename' => $filename,
            'original_filename' => 'email.eml',
            'mime' => 'message/rfc822'
        ]);

        foreach ($parser->getAttachments() as $attachment) {
            $task->files()->create($attachment);
        }

        Tasker::assign($task);

        $message->moveToMailBox('Zadania');
    }
}
