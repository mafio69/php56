<?php

class DosOtherInjuriesChatController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

    public function create($id)
    {
        return View::make('dos.other_injuries.dialog.chat.create', compact('id'));
    }

    public function post($id)
    {

        $status = count_receivers(Input::all());

        $chat =DosOtherInjuryChat::create(array(
                'injury_id' => $id,
                'user_id'	=> Auth::user()->id,
                'topic'		=> Input::get('topic'),
                'status'	=> $status
            )
        );

        if( get_chat_group() == 1 )
            $dos_read = date('Y-m-d H:i:s');
        else
            $dos_read = null;

        if( get_chat_group() == 3 )
            $info_read = date('Y-m-d H:i:s');
        else
            $info_read = null;

        if( get_chat_group() == 2 )
            $branch_read = date('Y-m-d H:i:s');
        else
            $branch_read = null;

        $content = preg_replace('/\s+/', ' ',Input::get('content'));

        if($content != '') {
            libxml_use_internal_errors(true);
            $doc = new DOMDocument('1.0', 'UTF-8');
            $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
            $doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $content = $doc->saveHTML();
        }

        DosOtherInjuryChatMessages::create(array(
                'chat_id'	=> $chat->id,
                'user_id'	=> Auth::user()->id,
                'content'	=> $content,
                'status'	=> $status,
                'dos_read'	=> $dos_read,
                'info_read'	=> $info_read,
                'branch_read' => $branch_read
            )
        );

        echo 0;

    }

    public function replay($id)
    {
        $conversation = DosOtherInjuryChat::find($id);
        $status = get_receivers($conversation->status);
        return View::make('dos.other_injuries.dialog.chat.replay', compact('id', 'status'));
    }

    public function postReplay($id)
    {
        $status = count_receivers(Input::all());

        if( get_chat_group() == 1 )
            $dos_read = date('Y-m-d H:i:s');
        else
            $dos_read = null;

        if( get_chat_group() == 3)
            $info_read = date('Y-m-d H:i:s');
        else
            $info_read = null;

        if( get_chat_group() == 2 )
            $branch_read = date('Y-m-d H:i:s');
        else
            $branch_read = null;

        $content = preg_replace('/\s+/', ' ',Input::get('content'));

        if($content != '') {
            libxml_use_internal_errors(true);
            $doc = new DOMDocument('1.0', 'UTF-8');
            $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
            $doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $content = $doc->saveHTML();
        }

        DosOtherInjuryChatMessages::create(array(
                'chat_id'	=> $id,
                'user_id'	=> Auth::user()->id,
                'content'	=> $content,
                'status'	=> $status,
                'dos_read'	=> $dos_read,
                'info_read'	=> $info_read,
                'branch_read' => $branch_read
            )
        );

        echo 0;

    }

    public function deadline($id)
    {
        $conversation = DosOtherInjuryChat::find($id);
        return View::make('dos.other_injuries.dialog.chat.deadline', compact('id'));
    }

    public function postDeadline($id)
    {
        $conversations = DosOtherInjuryChat::find($id);

        $conversations->deadline = Input::get('deadline');
        $conversations->save();

        echo 0;

    }

    function checkConversation(){
        $conversations = DosOtherInjuryChat::where('injury_id', '=', Input::get('injury_id'))->with('messages')->get();

        $colDate = get_chat_role();
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
        return View::make('dos.other_injuries.dialog.chat.close', compact('id'));
    }

    public function postClose($id)
    {
        $chat = DosOtherInjuryChat::find($id);
        $chat->active = 5;
        $chat->save();

        Histories::dos_history($chat->injury_id, 134, Auth::user()->id, $chat->topic);

        echo 0;

    }

    public function accept($id)
    {
        return View::make('dos.other_injuries.dialog.chat.accept', compact('id'));
    }

    public function postAccept($id)
    {
        $chat = DosOtherInjuryChat::find($id);
        $chat->deadline = null;
        $chat->save();

        echo 0;
    }

    public function removeDeadline($id)
    {
        return View::make('dos.other_injuries.dialog.chat.removeDeadline', compact('id'));
    }

    public function postremoveDeadline($id)
    {
        $chat = DosOtherInjuryChat::find($id);
        $chat->deadline = null;
        $chat->save();

        echo 0;
    }

}
