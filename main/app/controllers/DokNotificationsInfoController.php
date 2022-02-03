<?php

class DokNotificationsInfoController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

	public function postDocument($id){
		$id_notification = $id;

		$input = Input::all();
		
		$randomKey  = sha1( time() . microtime() );

        $extension  = Input::file('file')->getClientOriginalExtension();

        $filename   = $randomKey.'.'.$extension;

        $path       = '/files';
       

        // Move the file and determine if it was succesful or not
        $upload_success = Input::file('file')->move( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path , $filename );

		if( $upload_success ) {

			$file = DokFiles::create(array(
					'dok_notification_id' => $id_notification,
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
				$img = DokFiles::find($v);
				File::delete(public_path() . $path . $img->file);
				$img->delete();
			}
		}
		echo '0';
	}

	public function setDocumentSet(){
		$input = Input::get('files');
		$path  = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').'/files/';
		foreach ($input as $k => $v) {
			$file = DokFiles::find($v);
			$file->category = Input::get('fileType');
			$file->name = Input::get('content');
			$file->save();
			Histories::dok_history($file->dok_notification_id, 5, Auth::user()->id, 'Kategoria '.Config::get('definition.dokFileCategory.'.Input::get('fileType')).' - <a target="_blank" href="'.URL::route('dok.notifications.downloadDoc', array($v)).'">pobierz</a>');

			
		}

		return $file->category;
	}

	public static function downloadDoc($id) {
		ob_start();
		$file = DokFiles::find($id);
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

	public static function downloadGenerateDoc($id) {
		ob_start();
		$file = DokFiles::find($id);

		$path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".Config::get('definition.dokDocumentCategoryFolders.'.$file->category)."/".$file->file;

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

	public function setDelDoc($id)
	{
		$file = DokFiles::find($id);
		$file->active = '9';
		$file->touch();

		
		Histories::dok_history($file->dok_notification_id, 6, Auth::user()->id, '<a target="_blank" href="'.URL::route('dok.notifications.downloadDoc', array($id)).'">pobierz</a>');
		if( $file->save() ) echo $id;
	}

	public function setDelDocConf($id)
	{
		$file = DokFiles::find($id);
		$file->active = '9';
		$file->touch();
		Histories::dok_history($file->dok_notification_id, 6, Auth::user()->id, '-1', '<a target="_blank" href="'.URL::route('dok.notifications.downloadGenerateDoc', array($id)).'">pobierz</a> Przyczyna usunięcia:'.Input::get('content'));
		if( $file->save() ) echo $id;
	}

    public function setPriority($id)
    {
        $notification = DokNotifications::find($id);
        $notification->priority = Input::get('priority');

        Histories::dok_history($id, 8, Auth::user()->id);

        if( $notification->save() ){
            $result['code'] = 0;
            $result['message'] = "Priorytet zgłoszenia został zmieniony.";
            return json_encode($result);
        }
    }


	

}
