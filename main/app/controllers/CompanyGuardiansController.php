<?php

class CompanyGuardiansController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:uzytkownicy#podglad_uzytkownika#wejscie', ['only' => 'getShow']);
        $this->beforeFilter('permitted:uzytkownicy#podglad_uzytkownika#blokowanie_konta', ['only' => ['getLockAccount', 'postLockAccount', 'getUnlockAccount', 'postUnlockAccount']]);
        $this->beforeFilter('permitted:uzytkownicy#podglad_uzytkownika#ustawianie_hasla', ['only' => ['getResetPassword', 'postGenerate']]);
        $this->beforeFilter('permitted:uzytkownicy#lista_uzytkownikow#ustawianie_podpisu', ['only' => ['postUploadSignature', 'postSignature']]);
    }

    public function postPhone()
    {
        $phone = Request::get('phone');

        $guardian = CompanyGuardian::find(Request::get('guardian_id'));
        $guardian->phone = $phone;
        $guardian->save();

        return json_encode(['code' => 0]);
    }

    
	public function postUploadGuardiansFile()
	{
        $file = Input::file('file');
        $result = array();
        $file = Input::file('file');

        $mimes = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();

        if( !in_array($mimes , ['text/plain', 'application/vnd.ms-fontobject']) || $extension != 'csv'){
            $result['status'] = 'error';
            $result['msg'] = 'Niepoprawny format pliku. Obsługiwany format to .csv';
            return json_encode($result);
        }

        if($file) {
            $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/companies/';

            $randomKey  = sha1( time() . microtime() );
            $filename = $randomKey.'.'.$file->getClientOriginalExtension();

            $upload_success = Input::file('file')->move($destinationPath, $filename);

            if ($upload_success) {
                // $result = $this->parseCSV($filename);
                $result = $this->processGuardiansFile($destinationPath.'/'.$filename);
                return json_encode($result);
            } else {
                $result['status'] = 'error';
                $result['msg'] = 'Wystąpił błąd w trakcie wgrywania pliku. Skontaktuj się z administratorem.';
                return json_encode($result);
            }
        }
        return Response::json('error', 400);
	}
	 public function getUploadGuardiansFileDialog(){
		return View::make('companies.dialog.assign_guardian_file_upload');
     }
     
     private function processGuardiansFile($file){

        $result = array();

         $reader = Excel::load($file, function($reader) use(&$result){
            try {
            $array = [];
            foreach ($reader->getWorksheetIterator() as $worksheet) {
                $sheet = $worksheet->toArray();

                foreach ($sheet as $k => $row) {
                        array_push($array, explode(';', $row[0]));
                }
            }
            
            foreach($array as $row) {
                $nip = $row[0];
                $login = $row[1];               
                $phone = $row[2];
                
                $company = Company::where('nip', $nip)->first();
                $user = User::where('login', $login)->first();
                if(!is_null($user) && !is_null($company)){			
                    $guardian = CompanyGuardian::where('user_id', $user->id)->first();
                    if(count($guardian)<1){
                        $guardian = new CompanyGuardian();
                        $guardian->user_id = $user->id;
                        $guardian->save();
                    }
                    if(!is_null($phone)){
                        $guardian->phone = $phone;
                        $guardian->save();
                    }
                    $company->guardian_id = $guardian->id;
                    $company->save();
                }
                $result['status'] = 'success';
            }
            } catch(\Exception $e){
                $result['status'] = 'error';
                $result['msg'] = 'Wystąpił błąd w trakcie wgrywania pliku. Skontaktuj się z administratorem.';
            }
         });
         return $result;
     }

}
