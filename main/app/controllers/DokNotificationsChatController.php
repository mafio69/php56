<?php

class DokNotificationsChatController extends BaseController {


    public function create($id)
    {
        return View::make('dok.notifications.dialog.chat.create', compact('id'));
    }

    public function post($id)
    {

        $status = count_receivers(Input::all());

        $chat =DokChat::create(array(
                'dok_notification_id' => $id,
                'user_id'	=> Auth::user()->id,
                'topic'		=> Input::get('topic'),
                'status'	=> $status
            )
        );

        if( get_chat_group() == 1 )
            $dok_read = date('Y-m-d H:i:s');
        else
            $dok_read = null;

        if( get_chat_group() == 3 )
            $info_read = date('Y-m-d H:i:s');
        else
            $info_read = null;



        DokChatMessages::create(array(
                'chat_id'	=> $chat->id,
                'user_id'	=> Auth::user()->id,
                'content'	=> Input::get('content'),
                'status'	=> $status,
                'dok_read'	=> $dok_read,
                'info_read'	=> $info_read
            )
        );

        echo 0;

    }

    public function replay($id)
    {
        $conversation = DokChat::find($id);
        $status = get_receivers($conversation->status);
        return View::make('dok.notifications.dialog.chat.replay', compact('id', 'status'));
    }

    public function postReplay($id)
    {
        $status = count_receivers(Input::all());

        if( get_chat_group() == 1 )
            $dok_read = date('Y-m-d H:i:s');
        else
            $dok_read = null;

        if( get_chat_group() == 3)
            $info_read = date('Y-m-d H:i:s');
        else
            $info_read = null;

        DokChatMessages::create(array(
                'chat_id'	=> $id,
                'user_id'	=> Auth::user()->id,
                'content'	=> Input::get('content'),
                'status'	=> $status,
                'dok_read'	=> $dok_read,
                'info_read'	=> $info_read
            )
        );

        echo 0;

    }

    public function deadline($id)
    {
        return View::make('dok.notifications.dialog.chat.deadline', compact('id'));
    }

    public function postDeadline($id)
    {
        $conversations = DokChat::find($id);

        $conversations->deadline = Input::get('deadline');
        $conversations->save();

        echo 0;

    }

    /**
     *
     */
    function checkConversation(){
        $conversations = DokChat::where('dok_notification_id', '=', Input::get('dok_notification_id'))->with('messages')->get();

        $colDate = $this->get_chat_role();
        foreach ($conversations as $k => $conversation) {

            foreach ($conversation->messages as $k => $message) {
                $status = get_receivers($message->status);
                if( $status[get_chat_group()-1] == 1 && $message->$colDate == ''){
                    $message->$colDate = date('Y-m-d H:i:s');
                    $message->save();
                }
            }

        }
    }

    public function close($id)
    {
        return View::make('dok.notifications.dialog.chat.close', compact('id'));
    }

    public function postClose($id)
    {
        $chat = DokChat::find($id);
        $chat->active = 5;
        $chat->save();


        echo 0;

    }

    public function accept($id)
    {
        return View::make('dok.notifications.dialog.chat.accept', compact('id'));
    }

    public function postAccept($id)
    {
        $chat = DokChat::find($id);
        $chat->deadline = null;
        $chat->save();

        echo 0;
    }

    public function removeDeadline($id)
    {
        return View::make('dok.notifications.dialog.chat.removeDeadline', compact('id'));
    }

    public function postremoveDeadline($id)
    {
        $chat = DokChat::find($id);
        $chat->deadline = null;
        $chat->save();

        echo 0;
    }

    private function get_chat_role()
    {

            if( get_chat_group() == 1 )
                return 'dok_read';

            if( get_chat_group() == 3 )
                return 'info_read';

            if( get_chat_group() == 2 )
                return 'branch_read';
    }

}