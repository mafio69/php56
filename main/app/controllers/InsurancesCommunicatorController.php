<?php

class InsurancesCommunicatorController extends \BaseController {

    function __construct()
    {
        $this->beforeFilter('permitted:kartoteka_polisy#zarzadzaj');
    }

    public function getCreate($agreement_id)
    {
        return View::make('insurances.manage.card_file.dialog.add-conversation', compact('agreement_id'));
    }

    public function postStore($agreement_id)
    {
        $chat = LeasingAgreementChat::create(array(
                'leasing_agreement_id' => $agreement_id,
                'user_id'	=> Auth::user()->id,
                'topic'		=> Input::get('topic'),
            )
        );

        $content = preg_replace('/\s+/', ' ',Input::get('content'));

        LeasingAgreementChatMessage::create(array(
                'leasing_agreement_chat_id'	=> $chat->id,
                'user_id'	=> Auth::user()->id,
                'content'	=> $content
            )
        );

        return json_encode(array(
            'code' => 0
        ));
    }

    public function getClose($conversation_id)
    {
        return View::make('insurances.manage.card_file.dialog.close-conversation', compact('conversation_id'));
    }

    public function postClose($conversation_id)
    {
        $chat = LeasingAgreementChat::find($conversation_id);
        $chat->active = 5;
        $chat->save();

        Histories::leasingAgreementHistory($chat->leasing_agreement_id, 25, Auth::user()->id, $chat->topic);

        return json_encode(array(
            'code' => 0
        ));
    }

    public function getDeadline($conversation_id)
    {
        return View::make('insurances.manage.card_file.dialog.deadline-conversation', compact('conversation_id'));
    }

    public function postDeadline($conversation_id)
    {
        $conversations = LeasingAgreementChat::find($conversation_id);

        $conversations->deadline = Input::get('deadline');
        $conversations->deadline_user_id = Auth::user()->id;
        $conversations->save();

        return json_encode(array(
            'code' => 0
        ));

    }

    public function getReplay($conversation_id)
    {
        return View::make('insurances.manage.card_file.dialog.replay-conversation', compact('conversation_id'));
    }

    public function postReplay($conversation_id)
    {
        $content = preg_replace('/\s+/', ' ',Input::get('content'));

        LeasingAgreementChatMessage::create(array(
                'leasing_agreement_chat_id'	=> $conversation_id,
                'user_id'	=> Auth::user()->id,
                'content'	=> $content
            )
        );

        return json_encode(array(
            'code' => 0
        ));
    }

    public function getAccept($conversation_id)
    {
        return View::make('insurances.manage.card_file.dialog.accept-conversation', compact('conversation_id'));
    }

    public function postAccept($conversation_id)
    {
        $chat = LeasingAgreementChat::find($conversation_id);
        $chat->deadline = null;
        $chat->deadline_user_id = null;
        $chat->save();

        Histories::leasingAgreementHistory($chat->leasing_agreement_id, 26, Auth::user()->id, $chat->topic);

        return json_encode(array(
            'code' => 0
        ));
    }

    public function getRemoveDeadline($conversation_id)
    {
        return View::make('insurances.manage.card_file.dialog.remove-deadline-conversation', compact('conversation_id'));
    }

    public function postRemoveDeadline($conversation_id)
    {
        $chat = LeasingAgreementChat::find($conversation_id);
        $chat->deadline = null;
        $chat->deadline_user_id = null;
        $chat->save();

        return json_encode(array(
            'code' => 0
        ));
    }


    public function getDeleteMessage($conversation_id)
    {
        return View::make('insurances.manage.card_file.dialog.delete-message-conversation', compact('conversation_id'));
    }

    public function postDeleteMessage($conversation_id)
    {
        $chat = LeasingAgreementChatMessage::find($conversation_id);
        $chat->active = 5;
        $chat->delete_user_id = Auth::user()->id;
        $chat->save();

        return json_encode(array(
            'code' => 0
        ));
    }

}
