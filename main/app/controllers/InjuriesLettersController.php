<?php

class InjuriesLettersController extends \BaseController {
	/**
	 * InjuriesLettersController constructor.
	 */
	public function __construct()
	{
	    $this->beforeFilter('permitted:baza_pism#wejscie');
	}


	public function unprocessed()
	{
		$letters = InjuryLetter::where('is_unprocessed', 1)->where(function($query){
			$this->queryWheres($query);
			if(Input::has('document_type_id') && Input::get('document_type_id') != 0){
				$query->where('category', Request::get('document_type_id'));
			}
		})->with('user', 'uploadedDocumentType')->orderBy('id', 'desc')->paginate(Session::get('search.pagin', '10'));

		$uploadedDocumentTypes = InjuryUploadedDocumentType::with('subtypes')->orderBy('ordering')->get();

		return View::make('injuries.letters.unprocessed', compact('letters', 'uploadedDocumentTypes'));
	}

	public function nonAppended()
	{
		$letters = InjuryLetter::where('is_unprocessed', 0)->whereNull('injury_file_id')->where(function($query){
						$this->queryWheres($query);
						if(Input::has('document_type_id') && Input::get('document_type_id') != 0){
							$query->where('category', Request::get('document_type_id'));
						}
					})->with('user', 'uploadedDocumentType')->orderBy('id', 'desc')->paginate(Session::get('search.pagin', '10'));

        $uploadedDocumentTypes = InjuryUploadedDocumentType::with('subtypes')->orderBy('ordering')->get();

		return View::make('injuries.letters.nonAppended', compact('letters', 'uploadedDocumentTypes'));
	}

	public function appended()
	{
		$letters = InjuryLetter::where('is_unprocessed', 0)->whereNotNull('injury_file_id')->where(function($query){
						$this->queryWheres($query);
						if(Input::has('document_type_id') && Input::get('document_type_id') != 0){
							$query->where('category', Request::get('document_type_id'));
						}
					})->with('user','injury_file.injury', 'uploadedDocumentType')->orderBy('id', 'desc')->paginate(Session::get('search.pagin', '10'));

        $uploadedDocumentTypes = InjuryUploadedDocumentType::with('subtypes')->orderBy('ordering')->get();

        return View::make('injuries.letters.appended', compact('letters', 'uploadedDocumentTypes'));
	}

	private function queryWheres($query)
	{
		if(Input::has('term'))
		{
			$term = Input::get('term');
			$query->where('injury_nr', 'like', '%'.$term.'%')
				->orWhere('nr_contract', 'like', '%'.$term.'%')
				->orWhere('registration', 'like', '%'.$term.'%')
				->orWhere('name', 'like', '%'.$term.'%')
				->orWhere('nr_document', 'like', '%'.$term.'%');
		}
	}

	public function uploadLetter()
	{
		\Debugbar::disable();

		$result = array();
		$file = Input::file('file');

		if($file) {
			$destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/files';

			$randomKey  = sha1( time() . microtime() );
			$filename = $randomKey.'.'.$file->getClientOriginalExtension();

			if(!File::exists($destinationPath)) {
				File::makeDirectory($destinationPath,511, true);
			}

			$upload_success = Input::file('file')->move($destinationPath, $filename);

			if ($upload_success) {
				$result['redirect'] = URL::route('routes.get', ['injuries', 'letters', 'processing', $filename]);
				$result['status'] = 'success';
				return json_encode($result);
			} else {
				$result['status'] = 'error';
				$result['msg'] = 'Wystąpił błąd w trakcie wgrywania pliku. Skontaktuj się z administratorem.';
				return json_encode($result);
			}
		}
		return Response::json('error', 400);
	}

	public function uploadDialog()
	{
		return View::make('injuries.letters.dialog.fileUpload');
	}

	public function processing($filename)
	{
        $uploadedDocumentTypes = [];
	    $uploadedDocumentTypesDb = InjuryUploadedDocumentType::with('subtypes')->orderBy('ordering')->get();
	    foreach ($uploadedDocumentTypesDb as $uploadedDocumentType)
        {
            if($uploadedDocumentType->subtypes->count() == 0){
                $uploadedDocumentTypes[$uploadedDocumentType->id] = $uploadedDocumentType->name;
            }
        }
		return View::make('injuries.letters.processing', compact('filename', 'uploadedDocumentTypes'));
	}

	public function store()
	{
		$inputs = Input::all();
		if(Input::has('description')){
			$inputs['description'] = nl2br($inputs['description']);
		}
		$inputs['user_id'] = Auth::id();
		InjuryLetter::create($inputs);
		Flash::success('Dodano nowe pismo.');

		return Redirect::route('routes.get', ['injuries', 'letters', 'non-appended']);
	}

	public function download($id)
	{
		$file = InjuryLetter::find($id);
		$path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/files/'.$file->file;
		if(!is_null($file->name) && $file->name != '')
			$filename = Str::slug($file->name);
		elseif($file->uploadedDocumentType)
			$filename = $file->uploadedDocumentType->name.'_'.date('y-m-d');
		else
			$filename = $file->file;

		$downloader = new \Idea\Downloader\Downloader($path, $filename);
		return $downloader->download_on_disk();
	}

	public function delete($id)
	{
		return View::make('injuries.letters.dialog.delete', compact('id'));
	}

	public function destroy($id)
	{
		$letter = InjuryLetter::find($id);
		$letter->delete();

		Flash::success('Usunięto pismo.');
		return json_encode(['code' => 0]);
	}

	public function edit($id)
	{
		$letter = InjuryLetter::find($id);
        $uploadedDocumentTypes = [];
        $uploadedDocumentTypesDb = InjuryUploadedDocumentType::with('subtypes')->orderBy('ordering')->get();
        foreach ($uploadedDocumentTypesDb as $uploadedDocumentType)
        {
            if($uploadedDocumentType->subtypes->count() == 0){
                $uploadedDocumentTypes[$uploadedDocumentType->id] = $uploadedDocumentType->name;
            }
        }
		return View::make('injuries.letters.dialog.edit', compact('letter', 'uploadedDocumentTypes'));
	}

	public function update($id)
	{
		$inputs = Input::all();
		$inputs['is_unprocessed'] = 0;
		if(Input::has('description')){
			$inputs['description'] = nl2br($inputs['description']);
		}

		$letter = InjuryLetter::find($id);
		if(! $letter->user_id) $inputs['user_id'] = Auth::user()->id;
		$letter->update($inputs);

		Flash::success('Zaktualizowano dane pisma.');
		return json_encode(['code' => 0]);
	}

	public function reportNonAppended()
	{
		DB::disableQueryLog();
		Excel::create('zestawienie nieprzypisanych pism', function($excel) {
			$excel->sheet('zestawienie ', function($sheet){

				$sheet->appendRow([
					'typ dokumentu',
					'tytuł pisma',
					'nr szkody',
					'nr umowy',
					'nr rejestracyjny',
					'wprowadzający',
					'data wprowadzenia'
				]);

				InjuryLetter::whereNull('injury_file_id')->where(function($query){
					$this->queryWheres($query);
					if(Input::has('document_type_id') && Input::get('document_type_id') != 0){
						$query->where('category', Request::get('document_type_id'));
					}
				})->with('user', 'uploadedDocumentType')->orderBy('id', 'desc')->chunk(300, function($letters) use($sheet){
					foreach($letters as $letter){
						$sheet->appendRow(array(
								$letter->uploadedDocumentType->name,
								checkIfEmpty($letter->name),
								checkIfEmpty($letter->injury_nr),
								checkIfEmpty($letter->nr_contract),
								checkIfEmpty($letter->registration),
								$letter->user->name,
								substr($letter->created_at, 0, -3)
						));
					}
				});
			});

		})->download();
	}

	public function reportAppended()
	{
		DB::disableQueryLog();
		Excel::create('zestawienie nieprzypisanych pism', function($excel) {
			$excel->sheet('zestawienie ', function($sheet){

				$sheet->appendRow([
						'typ dokumentu',
						'nr sprawy',
						'tytuł pisma',
						'nr szkody',
						'nr umowy',
						'nr rejestracyjny',
						'wprowadzający',
						'data wprowadzenia'
				]);

				InjuryLetter::whereNotNull('injury_file_id')->where(function($query){
					$this->queryWheres($query);
					if(Input::has('document_type_id') && Input::get('document_type_id') != 0){
						$query->where('category', Request::get('document_type_id'));
					}
				})->with('user','injury_file.injury', 'uploadedDocumentType')->chunk(300, function($letters) use($sheet){
					foreach($letters as $letter){
						$sheet->appendRow(array(
								$letter->uploadedDocumentType->name,
								$letter->injury_file->injury->case_nr,
								checkIfEmpty($letter->name),
								checkIfEmpty($letter->injury_nr),
								checkIfEmpty($letter->nr_contract),
								checkIfEmpty($letter->registration),
								$letter->user->name,
								substr($letter->created_at, 0, -3)
						));
					}
				});
			});

		})->download();
	}


	public function assign($id)
	{
		$letter = InjuryLetter::find($id);
		$uploadedDocumentTypes = [];
		$uploadedDocumentTypesDb = InjuryUploadedDocumentType::with('subtypes')->orderBy('ordering')->get();
		foreach ($uploadedDocumentTypesDb as $uploadedDocumentType)
		{
			if($uploadedDocumentType->subtypes->count() == 0){
				$uploadedDocumentTypes[$uploadedDocumentType->id] = $uploadedDocumentType->name;
			}
		}

		return View::make('injuries.letters.dialog.assign', compact('letter', 'uploadedDocumentTypes'));
	}
}
