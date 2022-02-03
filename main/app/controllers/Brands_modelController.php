<?php

class Brands_modelController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /brands_model
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index($id)
	{
		$brand = Brands::find($id);
		$models = $brand->models()->with('generations')->orderBy('name')->paginate(Session::get('search.pagin', '10'));
		return View::make('settings.brands.models.index', compact('brand', 'models'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /brands_model/create
	 *
	 * @return Response
	 */
	public function create($brand_id)
	{
	    $brand = Brands::find($brand_id);
        return View::make('settings.brands.models.create', compact('brand'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /brands_model
	 *
	 * @return Response
	 */
	public function store($brand_id)
	{
        $brand = Brands::find($brand_id);

        Brands_model::create([
            'typ' => $brand->typ,
            'brand_id' => $brand->id,
            'name' => Input::get('name')
        ]);

        return json_encode(['code' => 0]);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /brands_model/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$model = Brands_model::find($id);

        return  View::make('settings.brands.models.edit', compact('model'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /brands_model/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$model = Brands_model::find($id);
		$model->update(Input::all());

		return json_encode(['code' => 0]);
	}

	public function delete($id)
    {
        $model = Brands_model::find($id);

        return View::make('settings.brands.models.delete', compact('model'));
    }
	/**
	 * Remove the specified resource from storage.
	 * DELETE /brands_model/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$model = Brands_model::find($id);
		$model->delete();

		return json_encode(['code' => 0]);
	}

}