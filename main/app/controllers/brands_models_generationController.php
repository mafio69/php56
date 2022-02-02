<?php

class Brands_models_generationController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /brands_models_generation
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function index($id)
	{
		$model = Brands_model::find($id);
		$generations = $model->generations()->paginate(15);
		return View::make('settings.brands.models.generations.index', compact('model', 'generations'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /brands_models_generation/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /brands_models_generation
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /brands_models_generation/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /brands_models_generation/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /brands_models_generation/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /brands_models_generation/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}