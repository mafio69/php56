<?php

class TaskBlackListController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('permitted:grupy_zadan#wejscie');
    }

    public function getIndex()
    {
        $blackList = TaskBlackList::orderBy('email')->paginate(Session::get('search.pagin', '20'));

        return View::make('tasks.black-list.index', compact('blackList'));
    }

    public function getCreate()
    {
        return View::make('tasks.black-list.create');
    }

    public function postStore()
    {
        TaskBlackList::create([
            'email' => Input::get('email'),
            'topic' => Input::get('topic', '') != '' ? Input::get('topic') : null
        ]);

        return json_encode(['code' => 0]);
    }

    public function getEdit($id)
    {
        $item = TaskBlackList::find($id);
        return View::make('tasks.black-list.edit', compact('item'));
    }

    public function postUpdate($id)
    {
        $item = TaskBlackList::find($id);
        $item->update([
            'email' => Input::get('email'),
            'topic' => Input::get('topic', '') != '' ? Input::get('topic') : null
        ]);
        return json_encode(['code' => 0]);
    }

    public function getDelete($id)
    {
        $item = TaskBlackList::find($id);
        return View::make('tasks.black-list.delete', compact('item'));
    }

    public function postDelete($id)
    {
        $item = TaskBlackList::find($id);
        $item->delete();

        return json_encode(['code' => 0]);
    }
}