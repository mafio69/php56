<?php

class DosOtherInjuriesDocsController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

    public static function downloadGenerateDoc($id) {
        ob_start();
        $file = DosOtherInjuryFiles::find($id);

        $documentType = DosOtherInjuryDocumentType::find($file->category);

        $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".$documentType->short_name."/".$file->file;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $pathParts = pathinfo($path);

        $name = rand('10000','99999');
        // Prepare the headers
        $headers = array(
            'Content-Description' => 'File Transfer',
            'Content-Type' => finfo_file($finfo, $path),
            'Content-Transfer-Encoding' => 'binary',
            'Expires' => 0,
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
            'Content-Length' => File::size($path),
            'Content-Disposition' => 'inline; filename="' . $name . '.' . $pathParts['extension'] . '"'
        );
        finfo_close($finfo);

        $response = new Symfony\Component\HttpFoundation\Response('', 200, $headers);

        // If there's a session we should save it now
        if (Config::get('session.driver') !== '') {
            Session::save();
        }

        // Below is from http://uk1.php.net/manual/en/function.fpassthru.php comments
        session_write_close();
        if (ob_get_contents()) ob_end_clean();
        $response->sendHeaders();
        if ($file = fopen($path, 'rb')) {
            while (!feof($file) and (connection_status() == 0)) {
                print(fread($file, 1024 * 8));
                flush();
            }
            fclose($file);
        }

        // Finish off, like Laravel would
        Event::fire('laravel.done', array($response));
        //$response->foundation->finish();

        exit;
    }

    public function getGenerateDocs($id, $key)
    {
        $documentType = DosOtherInjuryDocumentType::find($key);
        $injury = DosOtherInjury::find($id);
        $ideaOffices = IdeaOffices::whereActive(0)->get();

        return View::make('dos.other_injuries.dialog.generateDocument', compact('id', 'key', 'documentType', 'injury', 'ideaOffices'));
    }

    public function generateDoc($id, $document_type_id)
    {
        set_time_limit(120);

        ob_start();

        $injury = DosOtherInjury::find($id);
        $inputs = Input::all();
        $remarks = Text_contents::find($injury->remarks_damage);
        $idea = Idea_data::whereOwner_id($injury->object()->first()->owner_id)->get();
        if(isset($inputs['idea_office_id']))
            $ideaOffice = IdeaOffices::find($inputs['idea_office_id']);
        else
            $ideaOffice = '';

        $documentType = DosOtherInjuryDocumentType::find($document_type_id);

        $ideaA = array();
        foreach($idea as $setting)
        {
            $ideaA[$setting->parameter_id] = $setting->value;
        }

        $html = View::make('dos.other_injuries.docs_templates.' . $documentType->short_name,
            compact('injury', 'damage', 'damageSet', 'remarks', 'inputs', 'ideaA', 'ideaOffice')
        );

        if (!File::exists(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/' . $documentType->short_name))
            File::makeDirectory(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/' . $documentType->short_name);


        $randomKey = sha1(time() . microtime());
        $pdf = PDF::loadHTML($html)->setPaper('a4')->setOrientation('portrait')->setWarnings(false)
            ->save(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/' . $documentType->short_name . '/' . $id . '_' . $randomKey . '.pdf');

        $file = DosOtherInjuryFiles::create(array(
            'injury_id' => $id,
            'type' => 3,
            'category' => $document_type_id,
            'user_id' => Auth::user()->id,
            'file' => $id . '_' . $randomKey . '.pdf',
            'name' => ($documentType->fee == 1 && Input::get('issue_fee') == 0) ? 'Wygenerowano bez naliczenia opłat: ' . Input::get('reason') : ''
        ));

        if($documentType->fee == 1 && Input::get('issue_fee') == 0)
            $reason = " <b>Wygenerowano bez naliczenia opłat: ".Input::get('reason')."</b>";
        else
            $reason = '';

        Histories::dos_history($id, 126, Auth::user()->id, '-1', 'Kategoria '.$documentType->name.' - <a target="_blank" href="'.URL::route('dos.other.injuries.downloadGenerateDoc', array($file->id)).'">pobierz</a>'.$reason);

        if($documentType->task_authorization == 1)
            $injury->task_authorization = 1;


        if($documentType->fee == 1 && Input::has('issue_fee') && Input::get('issue_fee') == 1 && $injury->vehicle->owner->wsdl != ''){
            $contract = $injury->vehicle->nr_contract;
            $issuedate = $injury->date_event;
            $issuenumber = $injury->case_nr;
            $issuetype = 'B';
            $username = substr(Auth::user()->login, 0, 10);
            $owner_id = $injury->vehicle->owner_id;
            $feeamount = '0';

            $data = new Idea\Structures\ADDISSUEFEEInput($issuenumber, $feeamount, $username);

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('addIssueFee');

            $xml = $webservice->getResponseXML();

            if ($xml->Error->ErrorCde == 'ERR0000') {
                $injury->issue_fee = $xml->feeAmount;
            } else if ($xml->Error->ErrorCde == 'ERR0010') {

                $data = new Idea\Structures\REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

                if ($xml->Error->ErrorCde != 'ERR0000') {
                    $injury->issue_fee = 1;
                } else {
                    $data = new Idea\Structures\ADDISSUEFEEInput($issuenumber, $feeamount, $username);

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('addIssueFee');

                    $xml = $webservice->getResponseXML();

                    if ($xml->Error->ErrorCde == 'ERR0000')
                        $injury->issue_fee = $xml->feeAmount;
                    else
                        $injury->issue_fee = 1;
                }
            }
        }else
            $injury->issue_fee = 0;

        $injury->touch();
        $injury->save();

        switch($document_type_id){

        }

        return URL::route('dos.other.injuries.downloadGenerateDoc', array($file->id));
    }

    public static function downloadDoc($id) {
        ob_start();
        $file = DosOtherInjuryFiles::find($id);
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$file->file;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $pathParts = pathinfo($path);

        $name = rand('10000','99999');
        // Prepare the headers
        $headers = array(
            'Content-Description' => 'File Transfer',
            'Content-Type' => finfo_file($finfo, $path),
            'Content-Transfer-Encoding' => 'binary',
            'Expires' => 0,
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
            'Content-Length' => File::size($path),
            'Content-Disposition' => 'inline; filename="' . $name . '.' . $pathParts['extension'] . '"'
        );
        finfo_close($finfo);

        $response = new Symfony\Component\HttpFoundation\Response('', 200, $headers);

        // If there's a session we should save it now
        if (Config::get('session.driver') !== '') {
            Session::save();
        }

        // Below is from http://uk1.php.net/manual/en/function.fpassthru.php comments
        session_write_close();
        if (ob_get_contents()) ob_end_clean();
        $response->sendHeaders();
        if ($file = fopen($path, 'rb')) {
            while (!feof($file) and (connection_status() == 0)) {
                print(fread($file, 1024 * 8));
                flush();
            }
            fclose($file);
        }

        // Finish off, like Laravel would
        Event::fire('laravel.done', array($response));
        //$response->foundation->finish();

        exit;
    }

    public function postImage($id, $key)
    {

        $id_injury = $id;
        $category = $key;

        $input = Input::all();
        $rules = array(
            'file' => 'image',
        );

        $validation = Validator::make($input, $rules);

        if ($validation->fails())
        {
            return Response::json(array('status' => 'error', 'description' => 'przesłany plik nie jest zdjęciem'));
        }

        $randomKey  = sha1( time() . microtime() );

        $extension  = Input::file('file')->getClientOriginalExtension();

        $filename   = $randomKey.'.'.$extension;

        $path       = '/images/full';
        $path_min       = '/images/min';
        $path_thumb       = '/images/thumb';

        // Move the file and determine if it was succesful or not
        $upload_success = Input::file('file')->move( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path , $filename );

        $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$filename)->resize(320, null, true);
        $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_min.'/'.$filename);

        $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$filename)->resize(null, 100, true);
        $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_thumb.'/'.$filename);

        if( $upload_success ) {

            $image =DosOtherInjuryFiles::create(array(
                'injury_id' => $id_injury,
                'type'		=> 1,
                'category'	=> $category,
                'user_id'	=> Auth::user()->id,
                'file'		=> $filename,
            ));

            Histories::dos_history($id_injury, 22, Auth::user()->id, ' - <a target="_blank" href="'.URL::route('dos.other.injuries.downloadImg', array($image->id)).'">pobierz</a>');

            return Response::json(array('status' => 'seccess', 'file' => $filename));
        } else {
            return Response::json(array('status' => 'error'));
        }
    }

    public function postDocument($id){
        $id_injury = $id;

        $input = Input::all();

        $randomKey  = sha1( time() . microtime() );

        $extension  = Input::file('file')->getClientOriginalExtension();

        $filename   = $randomKey.'.'.$extension;

        $path       = '/files';


        // Move the file and determine if it was succesful or not
        $upload_success = Input::file('file')->move( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path , $filename );

        if( $upload_success ) {

            $file = DosOtherInjuryFiles::create(array(
                'injury_id' => $id_injury,
                'type'		=> 2,
                'category'	=> 0,
                'user_id'	=> Auth::user()->id,
                'file'		=> $filename,
            ));

            return Response::json(array('status' => 'success', 'file' => $filename, 'id' => $file->id));
        } else {
            return Response::json(array('status' => 'error'));
        }
    }

    public function setDocumentDel(){
        if(Input::has('files')) {
            $input = Input::get('files');
            $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/files/';
            foreach ($input as $k => $v) {
                $img = DosOtherInjuryFiles::find($v);
                File::delete(public_path() . $path . $img->file);
                $img->delete();
            }
        }
        echo '0';
    }

    public function setDocumentSet(){
        $input = Input::get('files');
        $fileType = Input::get('fileType');
        $path  = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').'/files/';
        foreach ($input as $k => $v) {
            $file = DosOtherInjuryFiles::find($v);
            $file->category = Input::get('fileType');
            $file->name = Input::get('content');
            $file->save();
            Histories::dos_history($file->injury_id, 20, Auth::user()->id, 'Kategoria '.Config::get('definition.fileCategory.'.Input::get('fileType')).' - <a target="_blank" href="'.URL::route('dos.other.injuries.downloadDoc', array($v)).'">pobierz</a>');

            if(Input::get('fileType') == 3 || Input::get('fileType') == 4){
                DosOtherInjuryInvoices::create(array(
                        'injury_id' 		=> $file->injury_id,
                        'injury_files_id'	=> $file->id,
                        'invoicereceives_id'=> $file->injury()->first()->invoicereceives_id,
                        'created_at'		=> $file->created_at,
                        'updated_at'		=> $file->updated_at
                    )
                );
            }

            if(Input::get('fileType') == 6 || Input::get('fileType') == 37)
            {
                DosOtherInjuryCompensation::create(array(
                    'injury_id' => $file->injury_id,
                    'injury_files_id'	=> $file->id,
                    'user_id' => Auth::user()->id
                ));
            }
        }

        return $file->category;
    }

    public function setDelDoc($id)
    {
        $file = DosOtherInjuryFiles::find($id);
        $file->active = '9';
        $file->touch();

        if($file->type == 2 && ($file->category == 3 || $file->category == 4) ){
            $invoices = DosOtherInjuryInvoices::where('injury_files_id', '=', $id)->get();

            foreach($invoices as $k => $invoice){
                $invoice->active = 9;
                $invoice->touch();
                $invoice->save();
            }
        }

        Histories::dos_history($file->injury_id, 21, Auth::user()->id, '<a target="_blank" href="'.URL::route('dos.other.injuries.downloadDoc', array($id)).'">pobierz</a>');
        if( $file->save() ) echo $id;
    }

    public function setDelDocConf($id)
    {
        $file = DosOtherInjuryFiles::find($id);
        $file->active = '9';
        $file->touch();
        Histories::dos_history($file->injury_id, 21, Auth::user()->id, '-1', '<a target="_blank" href="'.URL::route('dos.other.injuries.downloadGenerateDoc', array($id)).'">pobierz</a> Przyczyna usunięcia:'.Input::get('content'));
        if( $file->save() ) echo $id;
    }

    public static function downloadImg($id) {
        ob_start();
        $file = DosOtherInjuryFiles::find($id);
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/images/full/".$file->file;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $pathParts = pathinfo($path);

        $name = rand('10000','99999');
        // Prepare the headers
        $headers = array(
            'Content-Description' => 'File Transfer',
            'Content-Type' => finfo_file($finfo, $path),
            'Content-Transfer-Encoding' => 'binary',
            'Expires' => 0,
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
            'Content-Length' => File::size($path),
            'Content-Disposition' => 'inline; filename="' . $name . '.' . $pathParts['extension'] . '"'
        );
        finfo_close($finfo);

        $response = new Symfony\Component\HttpFoundation\Response('', 200, $headers);

        // If there's a session we should save it now
        if (Config::get('session.driver') !== '') {
            Session::save();
        }

        // Below is from http://uk1.php.net/manual/en/function.fpassthru.php comments
        if (ob_get_contents()) session_write_close();
        ob_end_clean();
        $response->sendHeaders();
        if ($file = fopen($path, 'rb')) {
            while (!feof($file) and (connection_status() == 0)) {
                print(fread($file, 1024 * 8));
                flush();
            }
            fclose($file);
        }

        // Finish off, like Laravel would
        Event::fire('laravel.done', array($response));
        //$response->foundation->finish();

        exit;
    }

    public function setDelImage($id)
    {
        $file = DosOtherInjuryFiles::find($id);
        $file->active = '9';
        $file->touch();
        Histories::dos_history($id, 23, Auth::user()->id, '<a target="_blank" href="'.URL::route('dos.other.injuries.downloadImg', array($id)).'">pobierz</a>');
        if( $file->save() ) echo $id;
    }

}
