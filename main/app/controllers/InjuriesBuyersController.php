<?php

class InjuriesBuyersController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:zarzadzanie_nabywcami#wejscie');
    }

    public function getIndex()
    {
        $buyers = Buyer::where(function($query){
            if(Input::has('term')){
                $term = Input::get('term');
                $query->where('name', 'like', '%'.$term.'%')->orWhere('nip', 'like', '%'. $term . '%');
            }
        })->orderBy('name')->paginate(Session::get('search.pagin', '10'));

        return View::make('buyers.index', compact('buyers'));
    }

    public function getEdit($buyer_id)
    {
        $buyer = Buyer::find($buyer_id);
        return View::make('buyers.edit', compact('buyer'));
    }

    public function postUpdate($buyer_id)
    {
        $buyer = Buyer::find($buyer_id);
        $buyer->update(Input::all());

        Flash::success('Zaktualizowano dane nabywcy '.$buyer->name);
        return Redirect::to('injuries/buyers');
    }

    public function getCreate()
    {
        if(Input::has('referrer'))
            $referrer = Input::get('referrer');
        else
            $referrer = null;

        return View::make('buyers.create', compact('referrer'));
    }

    public function postStore()
    {
        $buyer = Buyer::where('name', Input::get('name'))->first();

        if($buyer)
        {
            Flash::error('W systemie istnieje już nabywca o podanej nazwie.');
            return Redirect::back()->withInput();
        }

        $buyer  = Buyer::create(Input::all());
        Flash::success('Dodano nabywcę '.$buyer->name.' do systemu.');

        if(Input::has('referrer') && Input::get('referrer') != '') {
            $partials = explode('/', Input::get('referrer'));
            $injury_id = $partials[2];
            $injury = Injury::find($injury_id);
            $injury->wreck->buyer_id = $buyer->id;
            $injury->wreck->save();
            Histories::history($injury->id, 144, Auth::user()->id, 'zmiana nabywcy na - '.$buyer->name );

            return Redirect::to(Input::get('referrer') . '#selling_wreck');
        }

        return Redirect::to('injuries/buyers');
    }

    public function getDisable($buyer_id)
    {
        $buyer = Buyer::find($buyer_id);
        return View::make('buyers.disable', compact('buyer'));
    }

    public function postDisable($buyer_id)
    {
        $buyer = Buyer::find($buyer_id);
        $buyer->active = 1;
        $buyer->save();

        Flash::success('Dezaktywowano nabywcę '.$buyer->name);
        $result['code'] = 0;
        return json_encode($result);
    }

    public function getActivate($buyer_id)
    {
        $buyer = Buyer::find($buyer_id);
        return View::make('buyers.activate', compact('buyer'));
    }

    public function postActivate($buyer_id)
    {
        $buyer = Buyer::find($buyer_id);
        $buyer->active = 0;
        $buyer->save();

        Flash::success('Aktywowano nabywcę '.$buyer->name);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getDelete($buyer_id)
    {
        $buyer = Buyer::find($buyer_id);
        return View::make('buyers.delete', compact('buyer'));
    }

    public function postDelete($buyer_id)
    {
        $buyer = Buyer::find($buyer_id);
        $buyer->delete();

        Flash::success('Usunięto nabywcę '.$buyer->name);

        $result['code'] = 0;
        return json_encode($result);
    }


}
