<?php

class TaskAddressBookController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('permitted:zadania#ksiazka_adresowa#wejscie');
    }

    public function getIndex()
    {
        $addresses = TaskAddressBook::orderBy('email')->paginate(Session::get('search.pagin', '20'));

        return View::make('tasks.address-book.index', compact('addresses'));
    }

    public function getCreate()
    {
        return View::make('tasks.address-book.create');
    }

    public function postStore()
    {
        TaskAddressBook::create([
            'email' => Input::get('email'),
            'name' => Input::get('name', '') != '' ? Input::get('name') : null
        ]);

        return json_encode(['code' => 0]);
    }

    public function getEdit($id)
    {
        $item = TaskAddressBook::find($id);
        return View::make('tasks.address-book.edit', compact('item'));
    }

    public function postUpdate($id)
    {
        $item = TaskAddressBook::find($id);
        $item->update([
            'email' => Input::get('email'),
            'name' => Input::get('name', '') != '' ? Input::get('name') : null
        ]);
        return json_encode(['code' => 0]);
    }

    public function getDelete($id)
    {
        $item = TaskAddressBook::find($id);
        return View::make('tasks.address-book.delete', compact('item'));
    }

    public function postDelete($id)
    {
        $item = TaskAddressBook::find($id);
        $item->delete();

        return json_encode(['code' => 0]);
    }

    public function postSearch()
    {
        $term = Input::get('term');

        $emails = TaskAddressBook::where(function($query) use($term){
            $query->where('email', 'like', '%'.$term.'%');
            $query->orWhere('name', 'like', '%'.$term.'%');
        })->get();

        $result = array();

        foreach($emails as $k => $v){
            $result[] = array(
                "id" => $v->id,
                "label" => $v->email . ' - ' . $v->name,
                "value" => $v->email,
            );
        }

        return json_encode($result);
    }
}