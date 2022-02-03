<?php

class BrandsController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:lista_marek_samochodow#wejscie');
    }

	public function index()
	{
		Paginator::setPageName('brands');
        $query = Brands::with('models')->orderBy('typ', 'asc')->orderBy('name', 'asc');

        if(Input::has('filter_types') && Input::get('filter_types', 0 ) > 0){
            $query->where('typ', Input::get('filter_types'));
        }

        if(Input::has('filter_name') && Input::get('filter_name', '' ) != ''){
            $query->where('name', 'like', '%'.Input::get('filter_name').'%');
        }

		$brands = $query->paginate(Session::get('search.pagin', '10'));

		Paginator::setPageName('brands_multibrands');
        $query = Brands::with('models')->orderBy('typ', 'asc')->orderBy('name', 'asc');

        if(Input::has('filter_types') && Input::get('filter_types', 0 ) > 0){
            $query->where('typ', Input::get('filter_types'));
        }

        if(Input::has('filter_name') && Input::get('filter_name', '' ) != ''){
            $query->where('name', 'like', '%'.Input::get('filter_name').'%');
        }
        $query->where('if_multibrand',1);

        $brands_multibrands = $query->paginate(Session::get('search.pagin', '10'));
     	

        return View::make('settings.brands.index', compact('brands', 'brands_multibrands'));
	}


	public function getCreate()
	{
        return View::make('settings.brands.create');
	}

	public function postCreate()
	{
		$brand = new Brands();
	    $brand->name = Input::get('name');
	    $brand->typ = Input::get('typ');
	    $brand->save();

		if($brand){
			return json_encode(['code' => 0]);
		}else{
			return 'Wystąpił błąd w trakcie dodawania marki. Skontaktuj się z administratorem.';
		}
	}

	public function getEdit($id)
	{
		$brand = Brands::find( $id );

        return View::make('settings.brands.edit', compact('brand'));
	}

	public function set($id)
	{
		$brand = Brands::find( $id );

		$brand->name = Input::get('name');
		$brand->typ = Input::get('typ');
		if($brand->save()){
			return '0';
		}else{
			return 'Wystąpił błąd w trakcie zapisu zmian. Skontaktuj się z administratorem.';
		}
	}

	public function getDelete($id)
	{
		$brand = Brands::find( $id );

        return View::make('settings.brands.delete', compact('brand'));
	}

	public function delete($id)
	{
		$brand = Brands::find( $id );
	    $brand->delete();

        return '0';
	}

	public function setMultibrand(){
			$brand = Brands::find(Input::get('id'));
			$brand->if_multibrand = !$brand->if_multibrand;
			$brand->save();
			
			return '0';
	}

}
