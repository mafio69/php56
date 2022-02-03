<?php

class ChatController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:kartoteka_szkody#komunikator');
        $this->beforeFilter('permitted:kartoteka_szkody#komunikator#dodaj_wpis', ['only' => ['create', 'post']]);
        $this->beforeFilter('permitted:kartoteka_szkody#komunikator#zarzadzaj_wpisem', ['only' => ['replay', 'postReplay', 'deadline', 'postDeadline', 'close', 'postClose', 'accept', 'postAccept', 'removeDeadline', 'postremoveDeadline']]);
        $this->beforeFilter('permitted:kartoteka_szkody#komunikator#usun_wpis', ['only' => ['deleteMessage', 'removeMessage']]);
    }

	public function create($id)
	{
        return View::make('injuries.dialog.chat.create', compact('id'));
	}

	public function post($id)
	{

		$status = count_receivers(Input::all());

		$chat =InjuryChat::create(array(
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

		InjuryChatMessages::create(array(
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
		$conversation = InjuryChat::find($id);
		$status = get_receivers($conversation->status);
        return View::make('injuries.dialog.chat.replay', compact('id', 'status'));
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

		InjuryChatMessages::create(array(
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
        return View::make('injuries.dialog.chat.deadline', compact('id'));
	}

	public function postDeadline($id)
	{
		$conversations = InjuryChat::find($id);

		$conversations->deadline = Input::get('deadline');
		$conversations->deadline_user_id = Auth::user()->id;
		$conversations->save();

		echo 0;

	}

	function checkConversation(){
		$conversations = InjuryChat::where('injury_id', '=', Input::get('injury_id'))->with('messages')->get();

		$colDate = get_chat_role();
		foreach ($conversations as $k => $conversation) {

			foreach ($conversation->messages as $k => $message) {
				$status = get_receivers($message->status);
				if( isset($status[get_chat_group()-1]) && $status[get_chat_group()-1] == 1 && $message->$colDate == ''){
					$message->$colDate = date('Y-m-d H:i:s');
					$message->save();
				}
			}

		}
	}

	public function close($id)
	{
        return View::make('injuries.dialog.chat.close', compact('id'));
	}

	public function postClose($id)
	{
		$chat = InjuryChat::find($id);
		$chat->active = 5;
		$chat->save();

		Histories::history($chat->injury_id, 134, Auth::user()->id, $chat->topic);

		echo 0;
	}

    public function accept($id)
	{
    	return View::make('injuries.dialog.chat.accept', compact('id'));
	}

    public function postAccept($id)
    {
        $chat = InjuryChat::find($id);
        $chat->deadline = null;
		$chat->deadline_user_id = null;
        $chat->save();

        echo 0;
    }

    public function removeDeadline($id)
    {
        return View::make('injuries.dialog.chat.removeDeadline', compact('id'));
    }

    public function postremoveDeadline($id)
    {
        $chat = InjuryChat::find($id);
        $chat->deadline = null;
		$chat->deadline_user_id = null;
        $chat->save();

        echo 0;
    }

	public function deleteMessage($id)
	{
		return View::make('injuries.dialog.chat.deleteMessage', compact('id'));
	}
	public function removeMessage($id)
	{
		$chat = InjuryChatMessages::find($id);
		$chat->active = 5;
		$chat->delete_user_id = Auth::user()->id;
		$chat->save();

        if($chat->note ){
            $sap = new Idea\SapService\Sap();

            $notesToRemove[0] = $chat->note;
            $result = $sap->szkodaNotKasuj($chat->chat->injury, $notesToRemove);
            if(isset($result['ftNotatkaKeys'])){
                foreach($result['ftNotatkaKeys'] as $notatkaKey){
                    InjuryNote::where('injury_id', $chat->chat->injury->id)->where('roknotatki', $notatkaKey['roknotatki'])->where('nrnotatki', $notatkaKey['nrnotatki'])->delete();
                }
            }else{
                Flash::error('Wystąpił błąd w trakcie usuwania notatek.');
            }
        }

		echo 0;
	}

	public function sendToSap($id)
    {
        $message = InjuryChatMessages::find($id);
        $injury = $message->chat->injury;
        $sap = new Idea\SapService\Sap();

        $msg = strip_tags($message->content);
        $nbsp = html_entity_decode("&nbsp;");
        $msg = html_entity_decode($msg);
        $msg = str_replace($nbsp, " ", $msg);
        $msg = preg_replace("/[\\n\\r]+/", "", $msg);
        $msg = trim($msg);
        $msg = preg_replace('!\s+!', ' ', $msg);

        $result = $sap->szkodaNotUtworz($injury, [$msg]);

        $errors = [];
        if(isset($result['ftReturn']) && is_array($result['ftReturn'])){
            foreach($result['ftReturn'] as $ftReturn){
                if($ftReturn['typ'] =='E'){
                    $errors[] = $ftReturn;
                }
            }
        }

        if(count($errors) > 0){
            Flash::error('Wystąpił błąd w trakcie wysyłki notatek.');
            return Redirect::back();
        }

        foreach($result['ftNotatkaN'] as $note_item => $note){
            $injuryNote = InjuryNote::create([
                'referenceable_id' => $message->id,
                'referenceable_type' => 'InjuryChatMessages',
                'injury_id' => $injury->id,
                'user_id' => Auth::user()->id,
                'roknotatki' => $note['roknotatki'],
                'nrnotatki'=> $note['nrnotatki'],
                'obiekt'=> $note['obiekt'],
                'temat'=> $note['temat'],
                'data'=> $note['data'],
                'uzeit'=> $note['uzeit'],
            ]);

            $message->note()->associate($injuryNote);
            $message->save();
        }


        return Redirect::back();
    }

}
