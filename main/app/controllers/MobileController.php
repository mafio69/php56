<?php

class MobileController extends BaseController {

    /**
     * Wyszukuje najbliższe warsztaty
     *
     * Parametry przekazywane post'em:
     * - lat -> współrzędna lat punktu, na podstawie którego ma być wyszukiwany warsztat
     * - lng -> współrzędna lng punktu, na podstawie którego ma być wyszukiwany warsztat
     * - type -> rodzaj warsztatu, id z tabeli typegarages
     * - radius -> promień obszaru objętego poszukiwaniami(km), domyślnie 1000km
     * - limit -> max ilość zwróconych warsztatów, domyślnie 1
     *
     * @return json lista wyszukanych warsztatów
     */
	public function findBranch()
	{
		\Debugbar::disable();

		$content = "~".date("Y-m-d, H:i:s",time())."~".$_SERVER['REMOTE_ADDR']."~".gethostbyaddr($_SERVER['REMOTE_ADDR'])." \n";

		$content .= "\t funkcja: findBranch() \n";

		$content .= "\t parametry: ".implode(",", Input::all());

		$content .= "\n\n";

		custom_log(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER'), 'mobileCalls', $content);

		
		$lat = Input::get('lat');
		$lng = Input::get('lng');

		if(  is_numeric(Input::get('radius')) ) {
			$promien = Input::get('radius');
		} else {
			$promien = 1000;
		}

		if(  is_numeric(Input::get('limit')) ) {
			$limit = Input::get('limit');
		} else {
			$limit = 0;
		}
		
		$json = array();

        if($limit == 0) {
            $branches = Branch::select(
                array(
                    'branches.*',
                    DB::raw('6371 * acos( cos( radians(' . $lat . ') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(' . $lng . ') ) + sin( radians(' . $lat . ') ) * sin( radians( lat ) ) ) AS distance')
                ))
                ->active()
                ->hasType(Input::get('type'))
                ->whereHas('company', function($query){
                    $query->has('groups', '>', 0);
                })
                ->having('distance', '<', $promien)
                ->orderBy('distance', 'asc')
                ->get();
        }else{
            $branches = Branch::select(
                array(
                    'branches.*',
                    DB::raw('6371 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( lat ) ) ) AS distance')
                ))
                ->active()
                ->hasType(Input::get('type'))
                ->whereHas('company', function($query){
                    $query->has('groups', '>', 0);
                })
                ->having('distance', '<', $promien)
                ->orderBy('distance', 'asc')
                ->take($limit)->get();
        }
			  	
		
		foreach ($branches as $k => $branch){

			$json[] = array(
					'id' 		=> $branch->id,
					'name'		=> $branch->company()->first()->name.' - '.$branch->short_name,
					'code' 		=> $branch->code,
					'city' 	    => $branch->city,
                    'street'	=> $branch->street,
					'lat'		=> $branch->lat,
					'lng'		=> $branch->lng,
					'phone'		=> $branch->phone,
					'email'		=> $branch->email,
					'distance'	=> round($branch->distance, 2)
				);	

		}
		return json_encode($json);

	}

    public function getCities()
    {
        \Debugbar::disable();

        $content = "~".date("Y-m-d, H:i:s",time())."~".$_SERVER['REMOTE_ADDR']."~".gethostbyaddr($_SERVER['REMOTE_ADDR'])." \n";

        $content .= "\t funkcja: getCities() \n";

        $content .= "\t parametry: ".implode(",", Input::all());

        $content .= "\n\n";

        custom_log(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER'), 'mobileCalls', $content);

        $type = Input::get('type');

        $cities = Branch::select('branches.city')
                    ->distinct()
                    ->active()
                    ->hasType($type)
                    ->whereHas('company', function($query){
                        $query->has('groups', '>', 0);
                    })
                    ->orderBy('branches.city')->get();

        $json = array();

        foreach($cities as $city){
            $json[] = array(
                'city' => $city->city
            );
        }

        return json_encode($json);
    }

    public function findBranchInCity()
    {
        \Debugbar::disable();

        $content = "~".date("Y-m-d, H:i:s",time())."~".$_SERVER['REMOTE_ADDR']."~".gethostbyaddr($_SERVER['REMOTE_ADDR'])." \n";

        $content .= "\t funkcja: findBranchInCity() \n";

        $content .= "\t parametry: ".implode(",", Input::all());

        $content .= "\n\n";

        custom_log(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER'), 'mobileCalls', $content);


        $city = Input::get('city');
        $city = str_replace('_', ' ', $city);

        if(  is_numeric(Input::get('limit')) ) {
            $limit = Input::get('limit');
        } else {
            $limit = 0;
        }

        $json = array();

        if($limit == 0){
            $branches = Branch::
                select('branches.*')
                ->where('branches.city', '=', $city)
                ->active()
                ->hasType(Input::get('type'))
                ->whereHas('company', function($query){
                    $query->has('groups', '>', 0);
                })
                ->orderBy('short_name', 'asc')
                ->get();
        }else {
            $branches = Branch::
            select('branches.*')
                ->where('branches.city', '=', $city)
                ->active()
                ->hasType(Input::get('type'))
                ->whereHas('company', function($query){
                    $query->has('groups', '>', 0);
                })
                ->orderBy('short_name', 'asc')
                ->take($limit)->get();
        }



        foreach ($branches as $k => $branch){

            $json[] = array(
                'id' 		=> $branch->id,
                'name'		=> $branch->company()->first()->name.' - '.$branch->short_name,
                'code' 		=> $branch->code,
                'city' 	    => $branch->city,
                'street'	=> $branch->street,
                'lat'		=> $branch->lat,
                'lng'		=> $branch->lng,
                'phone'		=> $branch->phone,
                'email'		=> $branch->email,
            );

        }
        return json_encode($json);

    }

    /**
     * Wylosowanie reklamy o zadanej rozdzielczości
     * @param $id_res
     * @return null|string
     */
    public function drawAdvert($id_res=NUL)
    {
        \Debugbar::disable();

        $json = array();

        if($id_res == NULL){
            return NULL;

        }else {
            $adverts = Adverts::whereResolution_type_id($id_res)->whereActive(0)->get();
            $advertsA = array();
            foreach ($adverts as $advert) {
                $advertsA[] = $advert;
            }
            if( count($advertsA) > 0 ) {
                $num = mt_rand(0, count($advertsA) - 1);
                $advert = $advertsA[$num];

                $json['id'] = $advert->id;
                $json['url'] = $advert->url;
            }else{
                $advertsAll = Adverts::whereActive(0)->get();
                if( !$advertsAll->isEmpty() )
                {
                    $adverts = Adverts::where('resolution_type_id' , '>', $id_res)->whereActive(0)->orderBy('resolution_type_id', 'asc')->get();
                    if( !$adverts->isEmpty() )
                    {
                        $advertsA = array();
                        foreach($adverts as $advert)
                        {
                            $advertsA[$advert->resolution_type_id][] = $advert;
                        }
                        $advertsA = $advertsA[key($advertsA)];

                        $num = mt_rand(0, count($advertsA) - 1);
                        $advert = $advertsA[$num];
                        $json['id'] = $advert->id;
                        $json['url'] = $advert->url;
                    }else{
                        $adverts = Adverts::where('resolution_type_id' , '<', $id_res)->whereActive(0)->orderBy('resolution_type_id', 'desc')->get();
                        $advertsA = array();
                        foreach($adverts as $advert)
                        {
                            $advertsA[$advert->resolution_type_id][] = $advert;
                        }
                        $advertsA = $advertsA[key($advertsA)];

                        $num = mt_rand(0, count($advertsA) - 1);
                        $advert = $advertsA[$num];
                        $json['id'] = $advert->id;
                        $json['url'] = $advert->url;
                    }
                }else
                    return NULL;
            }
        }

        return json_encode($json);
    }

    /**
     * Zwraca losową reklamę
     *
     * @param null $id_res
     * @param null $id
     * @return object $image
     */
	public function generateAdvert($id_res= NULL, $id = NULL)
	{
        \Debugbar::disable();
        if($id_res == NULL){
            return NULL;

        }else {
            if( $id == NULL ) {
                $files = Adverts::whereResolution_type_id($id_res)->whereActive(0)->get();
                $advertsA = array();
                foreach ($files as $file) {
                    $advertsA[] = $file->file;
                }
                if (count($advertsA) > 0) {
                    $num = mt_rand(0, count($advertsA) - 1);
                    $advert = $advertsA[$num];

                    $image = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/mobile/adverts/prepared/' . $advert);
                } else {
                    $filesAll = Adverts::whereActive(0)->get();
                    if (!$filesAll->isEmpty()) {
                        $files = Adverts::where('resolution_type_id', '>', $id_res)->whereActive(0)->orderBy('resolution_type_id', 'asc')->get();
                        if (!$files->isEmpty()) {
                            $advertsA = array();
                            foreach ($files as $file) {
                                $advertsA[$file->resolution_type_id][] = $file->file;
                            }
                            $advertsA = $advertsA[key($advertsA)];

                            $num = mt_rand(0, count($advertsA) - 1);
                            $advert = $advertsA[$num];
                            $image = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/mobile/adverts/prepared/' . $advert);
                        } else {
                            $files = Adverts::where('resolution_type_id', '<', $id_res)->whereActive(0)->orderBy('resolution_type_id', 'desc')->get();
                            $advertsA = array();
                            foreach ($files as $file) {
                                $advertsA[$file->resolution_type_id][] = $file->file;
                            }
                            $advertsA = $advertsA[key($advertsA)];

                            $num = mt_rand(0, count($advertsA) - 1);
                            $advert = $advertsA[$num];
                            $image = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/mobile/adverts/prepared/' . $advert);
                        }
                    } else
                        return NULL;
                }
            }else{
                $advert = Adverts::find($id);
                $image = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/mobile/adverts/prepared/' . $advert->file);
            }
        }

		return $image->response();
	}


    /**
     * Rejestruje zgłoszenie w systemie
     *
     * @return json status operacji
     */
    public function registerInjury()
    {
        \Debugbar::disable();

        $content = "~".date("Y-m-d, H:i:s",time())."~".$_SERVER['REMOTE_ADDR']."~".gethostbyaddr($_SERVER['REMOTE_ADDR'])." \n";

        $content .= "\t funkcja: registerInjury() \n";

        $content .= "\t parametry: ";

        foreach( Input::all() as $name => $v) {
            $content .= ", ".$name." -> ".$v;
        }

        $content .= "\n\n";

        custom_log(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER'), 'mobileCalls', $content);

        $input = Input::all();

        switch ($input['injuries_type_id']) {
            case 13:
                $input['injuries_type_id'] = 11;
                break;
            case 14:
                $input['injuries_type_id'] = 1;
                break;
            case 15:
                $input['injuries_type_id'] = 12;
                break;
            case 2:
                $input['injuries_type_id'] = 12;
                break;
            case 3:
                $input['injuries_type_id'] = 11;
                break;
            default:
                break;
        }
        $input['notifier_email'] = strtolower($input['notifier_email']);

        $validator = Validator::make($input ,
            array(
                'notifier_email' => 'required|email',
                'date_event'     => 'date'
            )
        );

        $result = array();

        if($validator -> fails() || (!Input::has('registration') && !Input::has('nr_contract')) ){
            $result['status'] = 1;
            $result['desc'] = 'Brak poprawnych wymaganych danych.';
        }else {

//            if(isset($input['date_event']) && $input['date_event'] != ''){
//                $date_event = explode('-', $input['date_event']);
//                $input['date_event'] = $date_event[2].'-'.$date_event[1].'-'.$date_event[0];
//            }

            $confirmation_token = sha1( time() . microtime(). rand(100000, 999999) );
            $input['confirmation_token'] = $confirmation_token;

            $injury = MobileInjury::create($input);

            $group_name = '';
            if( ($injury->source == 0 || $injury->source == 3)  && $injury->injuries_type()->first()) {
                $group_name = $injury->injuries_type()->first()->name;
            }else {
                if ($injury->injuries_type == 2)
                    $group_name = 'komunikacyjna OC';
                elseif($injury->injuries_type == 1)
                    $group_name = 'komunikacyjna AC';
                elseif($injury->injuries_type == 3)
                    $group_name = 'komunikacyjna kradzież';
                elseif($injury->injuries_type == 4)
                    $group_name = 'majątkowa';
                elseif($injury->injuries_type == 5)
                    $group_name = 'majątkowa kradzież';
                elseif($injury->injuries_type == 6)
                    $group_name = 'komunikacyjna AC - Regres';
            }

            if (strpos($group_name, 'kradzież') !== false) {
                $task_group_id = 3;
            }else{
                $task_group_id = 1;
            }

            $task = Task::create([
                'task_source_id' => 2, //druk online
                'from_email' => $injury->notifier_email,
                'from_name' => $injury->notifier_name.' '.$injury->notifier_surname,
                'subject' => $injury->nr_contract.' # '.$injury->registration,
                'content' => $injury->description(),
                'task_group_id' => $task_group_id,
                'task_date' => $injury->created_at
            ]);

            $injury->tasks()->save($task);

            if($injury->source == 1)
                $template = 'mobile.info_template_web';
            else
                $template = 'mobile.info_template_phone';
            $html = View::make($template, compact('injury'));
            $name= str_random(32).'.pdf';

            PDF::loadHTML($html)->setPaper('a4')->setOrientation('portrait')->setWarnings(false)->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$name);

            $task->files()->create([
                'filename' => $name,
                'original_filename' => 'zgłoszenie.pdf',
                'mime' => 'application/pdf'
            ]);

            \Idea\Tasker\Tasker::assign($task);

            if( isset($input['damage_type']) && $input['damage_type'] != '' ){
                $damage_type = explode(',', $input['damage_type']);

                foreach($damage_type as $v){

                    MobileInjuryDamage::create(array(
                        'mobile_injury_id'      => $injury->id,
                        'mobile_damage_type_id' => $v
                    ));
                }
            }

            $input['logo'] = public_path().'/assets/css/images/idea-getin-logo.png';

            $input['confirmation_url'] = route('mobile.confirm_injury', $confirmation_token);


            Mail::send('emails.mobile.confirmation', $input, function($message) use ($input)
            {
                $message->to($input['notifier_email'],  $input['notifier_email'])->subject('[IdeaLeasing] Potwierdzenie zgłoszenia szkody do systemu');
            });

            $result['status'] = 0;
            $result['desc'] = 'Szkoda została zarejestrowana w systemie.';
        }

        return json_encode($result);
    }

    public function injuryAttachImg()
    {
        \Debugbar::disable();

        $content = "~".date("Y-m-d, H:i:s",time())."~".$_SERVER['REMOTE_ADDR']."~".gethostbyaddr($_SERVER['REMOTE_ADDR'])." \n";

        $content .= "\t funkcja: injuryAttachImg() \n";

        $content .= "\t parametry: ".implode(",", Input::all());

        $content .= "\n\n";

        custom_log(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER'), 'mobileCalls', $content);


        $input = Input::all();

        $validator = Validator::make($input ,
            array(
                'file' => 'required|image'
            )
        );

        $result = array();

        if( !isset($input['token']) || $input['token'] == '' ) {
            $result['status'] = 1;
            $result['desc'] = 'Nie przesłano tokena.';
        }else if( $validator -> fails() ){
            $result['status'] = 1;
            $result['desc'] = 'Nie przesłano prawidłowego zdjęcia.';
        }else {
            $injury = MobileInjury::whereInjury_token($input['token'])->first();

            $randomKey  = sha1( time() . microtime().rand(10000, 999999) );

            $extension  = Input::file('file')->getClientOriginalExtension();

            if($extension == '') $extension = 'jpg';

            $filename   = $randomKey.'.'.$extension;

            $path       = '/mobile/images/full';
            $path_min       = '/mobile/images/min';
            $path_thumb       = '/mobile/images/thumb';

            // Move the file and determine if it was succesful or not
            $upload_success = Input::file('file')->move( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path , $filename );

            if( $upload_success ) {

                $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$filename)->resize(320, null, true);
                $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_min.'/'.$filename);

                $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$filename)->resize(null, 100, true);
                $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_thumb.'/'.$filename);

                $image = MobileInjuryFile::create(array(
                    'mobile_injury_id' => $injury->id,
                    'file'		=> $filename,
                ));

                $result['status'] = 0;
                $result['desc'] = 'Zdjęcie zostało dodane do szkody.';

            } else {
                $result['status'] = 1;
                $result['desc'] = 'Wystąpił błąd podczas przetwarzania pliku.';
            }
        }


        $content = "\t odpowiedż: ".implode(",", $result);

        $content .= "\n\n";

        custom_log(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER'), 'mobileCalls', $content);


        return json_encode($result);

    }

    public function confirmInjury($token)
    {
        $injury_m = MobileInjury::whereConfirmation_token($token)->first();
        if( $injury_m ){
            if($injury_m->active == 5)
            {
                $injury_m->active = 0;
                $injury_m->save();

                $message = "Dziękujemy, zgłoszenie zostało potwierdzone w systemie.";
            }else if($injury_m->active == 0 || $injury_m->active == '-1')
            {
                $message = "Zadana szkoda została już potwierdzona.";
            }else if($injury_m->active == 9)
            {
                $message = "Szukana szkoda została usunięta z systemu.";
            }
        }else
        {
            $message = "Szukana szkoda nie istnieje w systemie.";
        }
        return View::make('mobile.confirmation', compact('message'));
    }


    public function lastLog()
    {
        $folder = Config::get('webconfig.WEBCONFIG_LOGS_FOLDER');
        $file = 'mobileCalls';
        $dateNow = explode('-',date("Y-m-d-H-i-s"));
        $dateNow = array(
            'year' => $dateNow[0],
            'month' => $dateNow[1],
            'day' => $dateNow[2],
            'hour' => $dateNow[3],
            'minute' => $dateNow[4],
            'second' => $dateNow[5]
        );
        $logDir = $dateNow['year'].'-'.$dateNow['month'].'/';
        if(!is_dir($folder.'/'.$logDir)){mkdir($folder.'/'.$logDir,0777,true);}

        if(File::exists($folder."/".$logDir."/".$file.".log")) {
            return Response::make(File::get($folder . "/" . $logDir . "/" . $file . ".log"), 200, array('Content-Type' => 'text/plain')); ;
        }
        else return 'brak dzisiejszego loga';
    }

}
