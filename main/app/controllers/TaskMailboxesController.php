<?php

class TaskMailboxesController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('permitted:skrzynki_pocztowe#wejscie');
    }

	public function getIndex()
	{
		$mailboxes = TaskMailbox::with('taskSource', 'taskGroup')->get();

		return View::make('tasks.mailboxes.index', compact('mailboxes'));
	}


    public function getCreate()
    {
        $taskSources = TaskSource::lists('name', 'id');
        $taskGroups = TaskGroup::get();

        return View::make('tasks.mailboxes.create', compact('taskSources', 'taskGroups'));
    }

    public function postStore()
    {
        $mailbox = TaskMailbox::create([
            'name' => Input::get('name'),
            'server' => Input::get('server'),
            'login' => Input::get('login'),
            'password' => Crypt::encrypt(Input::get('password')),
            'task_source_id' => Input::get('task_source_id'),
            'task_group_id' => Input::get('task_group_id')
        ]);

        foreach(Input::get('mails', []) as $k => $mail){
            TaskMailboxMail::create([
                'task_mailbox_id' => $mailbox->id,
                'mail' => $mail,
                'task_group_id' => Input::get('mail_task_groups')[$k]
            ]);
        }

        Artisan::call('system:check-mailboxes-connection',[
            $mailbox->id
        ]);

        return Redirect::to(url('tasks/mailboxes'));
    }

    public function getEdit($mailbox_id)
    {
        $mailbox = TaskMailbox::find($mailbox_id);
        $taskSources = TaskSource::lists('name', 'id');
        $taskGroups = TaskGroup::get();

        return View::make('tasks.mailboxes.edit', compact('mailbox', 'taskSources', 'taskGroups'));
    }

    public function postUpdate($mailbox_id)
    {
        $mailbox = TaskMailbox::find($mailbox_id);

        $mailbox->update([
            'name' => Input::get('name'),
            'server' => Input::get('server'),
            'login' => Input::get('login'),
            'password' => Input::get('password') ? Crypt::encrypt(Input::get('password')) : $mailbox->password,
            'task_source_id' => Input::get('task_source_id'),
            'task_group_id' => Input::get('task_group_id')
        ]);

        $mailbox->mails()->delete();

        foreach(Input::get('mails', []) as $k => $mail){
            TaskMailboxMail::create([
                'task_mailbox_id' => $mailbox->id,
                'mail' => $mail,
                'task_group_id' => Input::get('mail_task_groups')[$k]
            ]);
        }

        Artisan::call('system:check-mailboxes-connection',[
            'mailbox' => $mailbox->id
        ]);

        return Redirect::to(url('tasks/mailboxes'));
    }

    public function getDelete($mailbox_id)
    {
        $mailbox = TaskMailbox::find($mailbox_id);

        return View::make('tasks.mailboxes.delete', compact('mailbox'));
    }

    public function postDelete($mailbox_id)
    {
        $mailbox = TaskMailbox::find($mailbox_id);
        $mailbox->delete();

        return json_encode(['code' => 0]);
    }

    public function getAppendMail()
    {
        $taskGroups = TaskGroup::get();
        $task_group_id= Input::get('task_group_id');
        return View::make('tasks.mailboxes.mail', compact('taskGroups', 'task_group_id'));
    }
}