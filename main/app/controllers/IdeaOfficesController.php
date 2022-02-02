<?php

class IdeaOfficesController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:baza_oddzialow_idealeasing#wejscie');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $offices = IdeaOffices::whereActive(0)->get();

        return View::make('settings.idea_offices.index', compact('offices'));
    }

    public function create()
    {
        return View::make('settings.idea_offices.create');
    }

    public function post()
    {
        $input = Input::all();

        $validator = Validator::make($input ,
            array(
                'name' => 'required',
                'city' => 'required',
                'post' => 'required',
                'street' => 'required'
            )
        );

        if($validator -> fails()){
            return Redirect::back()->withInput()->withErrors($validator);
        }else{
            IdeaOffices::create($input);

            return Redirect::route('idea.offices');
        }
    }

    public function edit($id)
    {
        $office = IdeaOffices::find($id);
        return View::make('settings.idea_offices.edit', compact('office'));
    }

    public function update($id)
    {
        $input = Input::all();

        $validator = Validator::make($input ,
            array(
                'name' => 'required',
                'city' => 'required',
                'post' => 'required',
                'street' => 'required'
            )
        );

        if($validator -> fails()){
            return Redirect::back()->withInput()->withErrors($validator);
        }else {
            $office = IdeaOffices::find($id);

            $office->fill($input);
            $office->save();

            return Redirect::route('idea.offices');
        }
    }

    public function delete($id)
    {
        $office = IdeaOffices::find($id);
        return View::make('settings.idea_offices.delete', compact('office'));

    }

    public function destroy($id)
    {
        $office = IdeaOffices::find($id);
        $office->active = 9;
        $office->save();

        $result['code'] = 0;
        return json_encode($result);
    }
}
