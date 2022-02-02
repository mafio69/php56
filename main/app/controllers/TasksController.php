<?php

use Carbon\Carbon;
use Idea\Docer\Docer;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TasksController extends \BaseController {

    public function postSwitchVisibility()
    {
        Session::put('task.visibility', ! Session::get('task.visibility', false));
    }

    public function getShow($task_id)
    {
        $taskInstance = TaskInstance::with('task.comments.user', 'task.files', 'task.replies.user')->findOrFail($task_id);
        $loadInSection = false;

        Session::put('task.section', 'details');
        Session::put('task.id', $taskInstance->id);

        return View::make('tasks.module.task', compact('taskInstance', 'loadInSection'));
    }

    public function postCollect()
    {
        $task_instance_id = Input::get('task_id');
        $taskInstance = TaskInstance::findOrFail($task_instance_id);

        if($taskInstance->user_id != Auth::user()->id){
            $taskInstance->update([
                'date_complete' => \Carbon\Carbon::now(),
                'task_step_id' => 7
            ]);

            TaskStepHistory::create([
                'task_instance_id' => $taskInstance->id,
                'task_step_id' => 7
            ]);

            $taskInstance = TaskInstance::create([
                'task_id' => $taskInstance->task_id,
                'user_id' => Auth::user()->id,
                'task_step_id' => 1
            ]);

            TaskStepHistory::create([
                'task_instance_id' => $taskInstance->id,
                'task_step_id' => 1
            ]);

            $taskInstance->task->update([
                'current_task_instance_id' => $taskInstance->id,
            ]);
            if(Auth::user()->taskGroups()->first())
            {
                $taskInstance->task->update([
                    'task_group_id' => Auth::user()->taskGroups()->first()->id
                ]);
            }
        }

        $taskInstance->update([
            'date_collect' => \Carbon\Carbon::now(),
            'task_step_id' => 2
        ]);

        TaskStepHistory::create([
            'task_instance_id' => $taskInstance->id,
            'task_step_id' => 2
        ]);

        Cache::forget('task.stats');
        Cache::forget('task.user.'.\Auth::user()->id);

        Session::put('task.section', 'details');
        Session::put('task.id', $taskInstance->id);
        $loadInSection = false;
        return View::make('tasks.module.task', compact('taskInstance', 'loadInSection'));
    }

    public function postAttachInjury($task_id)
    {
        $task = Task::findOrFail($task_id);
        $injury = Injury::find(Input::get('injury_id'));

        $task->injuries()->attach(Input::get('injury_id'));

        $link = $task->case_nb.' <a class="task-show-details" data-task="'.$task->currentInstance->id.'" >
                        przejdź
                    </a>';
        Histories::history(Request::get('injury_id'), 220, Auth::user()->id, $link );

        if($task->task_source_id == 2){
            $taskFile = $task->files()->first();
            $path = '/files';

            copy(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/" . $taskFile->filename, Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER'). $path .'/'.$taskFile->filename);
            $file = InjuryFiles::create([
                'injury_id' => $injury->id,
                'type' => 2,
                'user_id' => Auth::user()->id,
                'file' => $taskFile->filename
            ]);
            $fileType = 1;
            Docer::setDocumentType($file, $fileType);
            Docer::processDocumentInjury($injury, $fileType);
        }

        return Redirect::back();
    }

    public function getInjuryFileType($file_id, $injury_id)
    {
        $documentTypes = InjuryUploadedDocumentType::whereNull('parent_id')->orderBy('ordering')->whereNull('hidden')->get();

        return View::make('tasks.module.document', compact('file_id', 'injury_id' , 'documentTypes'));
    }

    public function getInjuryFileTypes($injury_id)
    {
        $documentTypes = InjuryUploadedDocumentType::whereNull('parent_id')->orderBy('ordering')->whereNull('hidden')->get();

        return View::make('tasks.module.documents', compact('injury_id' , 'documentTypes'));
    }

    public function postAttachFile()
    {
        $injury = Injury::findOrFail(Input::get('injury_id'));
        $taskFile = TaskFile::findOrFail(Input::get('file_id'));

        $path = '/files';

        copy(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/" . $taskFile->filename, Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER'). $path .'/'.$taskFile->filename);

        $file = InjuryFiles::create([
            'injury_id' => $injury->id,
            'type' => 2,
            'user_id' => Auth::user()->id,
            'file' => $taskFile->filename
        ]);

        $fileType = Input::get('fileType');
        $uploadedDocumentType = InjuryUploadedDocumentType::find($fileType);
        if($uploadedDocumentType->subtypes->count() > 0){
            $fileType = Input::get('fileSubType');
        }

        Docer::setDocumentType($file, $fileType, Input::get('amount') , Input::get('content'));
        Docer::processDocumentInjury($injury, $fileType);

        if(in_array($file->category, [4,3,6,2])){
            return json_encode(['code' => 1, 'url' => url('injuries/info', [$injury->id]).'#settlements']);
        }else{
            return json_encode(['code' => 1, 'url' => url('injuries/info', [$injury->id]).'#documentation']);
        }
    }

    public function postAttachFiles()
    {
        $injury = Injury::findOrFail(Input::get('injury_id'));

        foreach(Input::get('taskFiles', []) as $file_id) {
            $taskFile = TaskFile::findOrFail($file_id);

            $path = '/files';

            copy(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/emails/" . $taskFile->filename, Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path . '/' . $taskFile->filename);

            $file = InjuryFiles::create([
                'injury_id' => $injury->id,
                'type' => 2,
                'user_id' => Auth::user()->id,
                'file' => $taskFile->filename
            ]);

            $fileType = Input::get('fileType');
            $uploadedDocumentType = InjuryUploadedDocumentType::find($fileType);
            if ($uploadedDocumentType->subtypes->count() > 0) {
                $fileType = Input::get('fileSubType');
            }

            Docer::setDocumentType($file, $fileType, Input::get('amount'), Input::get('content'));
            Docer::processDocumentInjury($injury, $fileType);
        }

        if(in_array($file->category, [4,3,6,2])){
            return json_encode(['code' => 1, 'url' => url('injuries/info', [$injury->id]).'#settlements']);
        }else{
            return json_encode(['code' => 1, 'url' => url('injuries/info', [$injury->id]).'#documentation']);
        }
    }

    public function getInjuryImageType($file_id, $injury_id)
    {
        $documentTypes = Config::get('definition.imageCategory');

        return View::make('tasks.module.image', compact('file_id', 'injury_id' , 'documentTypes'));
    }

    public function getInjuryImageTypes($injury_id)
    {
        $documentTypes = Config::get('definition.imageCategory');

        return View::make('tasks.module.images', compact('injury_id' , 'documentTypes'));
    }

    public function postAttachImage()
    {
        $injury = Injury::findOrFail(Input::get('injury_id'));
        $taskFile = TaskFile::findOrFail(Input::get('file_id'));
        $fileType = Input::get('fileType');

        $path       = '/images/full';
        $path_min       = '/images/min';
        $path_thumb       = '/images/thumb';

        copy(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/" . $taskFile->filename, Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER'). $path .'/'.$taskFile->filename);

        $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$taskFile->filename)->resize(320, null, true);
        $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_min.'/'.$taskFile->filename);

        $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$taskFile->filename)->resize(null, 100, true);
        $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_thumb.'/'.$taskFile->filename);


        $image = InjuryFiles::create([
            'injury_id' => $injury->id,
            'type' => 1,
            'category'	=> $fileType,
            'user_id' => Auth::user()->id,
            'file' => $taskFile->filename
        ]);

        Histories::history($injury->id, 22, Auth::user()->id, 'Kategoria '.Config::get('definition.imageCategory.'.$fileType).' - <a target="_blank" href="'.URL::route('injuries-downloadImg', array($image->id)).'">pobierz</a>');

        return json_encode(['code' => 0]);
    }

    public function postAttachImages()
    {
        $injury = Injury::findOrFail(Input::get('injury_id'));
        $fileType = Input::get('fileType');

        foreach(Input::get('taskFiles', []) as $file_id) {
            $taskFile = TaskFile::findOrFail($file_id);

            $path       = '/images/full';
            $path_min       = '/images/min';
            $path_thumb       = '/images/thumb';

            copy(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/" . $taskFile->filename, Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER'). $path .'/'.$taskFile->filename);

            $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$taskFile->filename)->resize(320, null, true);
            $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_min.'/'.$taskFile->filename);

            $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$taskFile->filename)->resize(null, 100, true);
            $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_thumb.'/'.$taskFile->filename);


            $image = InjuryFiles::create([
                'injury_id' => $injury->id,
                'type' => 1,
                'category'	=> $fileType,
                'user_id' => Auth::user()->id,
                'file' => $taskFile->filename
            ]);

            Histories::history($injury->id, 22, Auth::user()->id, 'Kategoria '.Config::get('definition.imageCategory.'.$fileType).' - <a target="_blank" href="'.URL::route('injuries-downloadImg', array($image->id)).'">pobierz</a>');
        }
        return json_encode(['code' => 0]);
    }

    public function getSwitchTabContent()
    {
        $tab = Input::get('tab');

        Session::put('task.section', $tab);

        return View::make('tasks.module.tab-'.$tab);
    }

    public function getChangeType($task_instance_id)
    {
        $taskInstance = TaskInstance::findOrFail($task_instance_id);

        $types = TaskType::orderBy('task_group_id')->orderBy('task_subgroup_id')->with('group', 'subgroup')->get();

        return View::make('tasks.module.change-task-instance-type', compact('taskInstance', 'types'));
    }

    public function getChangeTaskType($task_id)
    {
        $task = Task::findOrFail($task_id);

        $types = TaskType::orderBy('task_group_id')->orderBy('task_subgroup_id')->with('group', 'subgroup')->get();

        return View::make('tasks.module.change-task-type', compact('task', 'types'));
    }

    public function postUpdateType()
    {
        $task = Task::findOrFail(Input::get('task_id'));

        $task->update(['task_type_id' => Input::get('task_type_id')]);

        if(Input::get('pass') == 1) {
            $task->currentInstance->update([
                'task_step_id' => 3
            ]);

            TaskStepHistory::create([
                'task_instance_id' => $task->currentInstance->id,
                'task_step_id' => 3
            ]);

            \Idea\Tasker\Tasker::assign($task);
        }

        Cache::forget('task.stats');
        Cache::forget('task.user.'.\Auth::user()->id);

        return json_encode(['code' => 0]);
    }

    public function postUpdateTaskType()
    {
        $task = Task::findOrFail(Input::get('task_id'));

        $task->update(['task_type_id' => Input::get('task_type_id')]);

        return json_encode(['code' => 0]);
    }

    public function getComplete($task_instance_id)
    {
        $taskInstance = TaskInstance::findOrFail($task_instance_id);
        if($taskInstance->date_complete)
        {
            return 'Obsługa zadania została już zakończona.';
        }

        $types = TaskType::where('task_group_id', $taskInstance->task->task_group_id)
                            ->orderBy('task_subgroup_id')->orderBy('id')
                            ->with('group', 'subgroup')->get();


        return View::make('tasks.module.complete', compact('taskInstance', 'types'));
    }

    public function postComplete()
    {
        $taskInstance = TaskInstance::findOrFail(Input::get('task_instance_id'));

        $taskInstance->update([
            'date_complete' => \Carbon\Carbon::now(),
            'task_step_id' => 4
        ]);

        $taskInstance->task->update([
             'task_type_id' => Input::get('task_type_id')
        ]);

        TaskStepHistory::create([
            'task_instance_id' => $taskInstance->id,
            'task_step_id' => 4
        ]);

        Cache::forget('task.stats');
        Cache::forget('task.user.'.\Auth::user()->id);

        return json_encode(['code' => 0]);
    }

    public function getCompleteWithoutAction($task_instance_id)
    {
        $taskInstance = TaskInstance::findOrFail($task_instance_id);

        if($taskInstance->date_complete)
        {
            return 'Obsługa zadania została już zakończona.';
        }

        return View::make('tasks.module.complete-without-action', compact('taskInstance'));
    }

    public function postCompleteWithoutAction()
    {
        $taskInstance = TaskInstance::findOrFail(Input::get('task_instance_id'));

        $taskInstance->update([
            'date_complete' => \Carbon\Carbon::now(),
            'task_step_id' => 5
        ]);

        TaskStepHistory::create([
            'task_instance_id' => $taskInstance->id,
            'task_step_id' => 5,
            'description' => Input::get('description')
        ]);

        Cache::forget('task.stats');
        Cache::forget('task.user.'.\Auth::user()->id);


        return json_encode(['code' => 0]);
    }

    public function getPassTask($task_instance_id)
    {
        $taskInstance = TaskInstance::findOrFail($task_instance_id);

        if($taskInstance->date_complete)
        {
            return 'Obsługa zadania została już zakończona.';
        }

        $groups = TaskGroup::where('id', '!=', ($taskInstance->task && $taskInstance->task->task_group_id != 3 && $taskInstance->task->task_group_id != 5 ) ? $taskInstance->task->task_group_id : 0 )->lists('name', 'id');
        $groups = ['' => '--- wybierz ---'] + $groups;

        return View::make('tasks.module.pass-task', compact('taskInstance', 'groups'));
    }

    public function getLoadTaskUsers()
    {
        $task_group_id = Input::get('task_group_id');

        $users = \User::where(function($query)use($task_group_id){
            $query->whereHas('taskGroups', function ($query) use($task_group_id){
                $query->where('id', $task_group_id);
            })->orWhere('without_restrictions_task_group', 1);
        })->whereHas('taskExcludes', function ($query) {
            $query->whereDate('absence', '=', Carbon::now()->startOfDay());
        }, '<', 1)->lists('name', 'id');

        $allUsers = \User::where(function($query)use($task_group_id){
            $query->whereHas('taskGroups', function ($query) use($task_group_id){
                $query->where('id', $task_group_id);
            })->orWhere('without_restrictions_task_group', 1);
        })->lists('name', 'id');


        return View::make('tasks.module.task-users-list', compact('users', 'allUsers'))->render();
    }

    public function postChangeTaskGroup()
    {
        $taskInstance = TaskInstance::findOrFail(Input::get('task_instance_id'));

        $taskInstance->task->update(['task_group_id' => Input::get('task_group_id')]);

        $taskInstance->update([
            'date_complete' => \Carbon\Carbon::now(),
            'task_step_id' => 3
        ]);

        TaskStepHistory::create([
            'task_instance_id' => $taskInstance->id,
            'task_step_id' => 3,
            'description' => Input::get('description')
        ]);

        $user = User::find(Input::get('user_id'));

        if(!$user && Input::get('task_group_id') == 3)
        {
            $term = Input::get('term');
            $parameter = Input::get('term_parameter');
            if($term && $parameter) {
                $query = Injury::whereIn('step', [30, 31, 32, 33]);

                $query->where(function($query) use($parameter,$term){
                    if($parameter == 'registration'){
                        $query->vehicleExists('registration', $term);
                    }elseif($parameter == 'nr_contract'){
                        $query->vehicleExists('nr_contract', $term);
                    }elseif($parameter == 'injury_nr'){
                        $query->where(function($query) use($term){
                            $query->where('injury_nr', 'like', $term);

                            $query->orWhereHas('injuryGap', function($query) use($term){
                                $query->where('injury_number', 'like', $term);
                            });
                        });
                    }elseif($parameter == 'case_nr'){
                        $query->where('case_nr', 'like', $term);
                    }
                });


                $injury = $query->orderBy('id', 'desc')->has('leader')->with('leader')->first();
                if($injury && $injury->leader){
                    $leader = $injury->leader;
                    $user = \User::where(function($query){
                        $query->whereHas('taskGroups', function ($query) {
                            $query->where('id', 3);
                        })->orWhere('without_restrictions_task_group', 1);
                    })->whereHas('taskExcludes', function ($query) {
                        $query->whereDate('absence', '=', Carbon::now()->startOfDay());
                    }, '<', 1)->where('id', $leader->id)->first();
                }

            }
        }

        if($user){
            \Idea\Tasker\Tasker::attachToUser($taskInstance->task, $user);
        }else {
            $status = \Idea\Tasker\Tasker::assign($taskInstance->task, [$taskInstance->user_id]);
            if($status['status'] == 'error'){
                $taskInstance->task->update(['current_task_instance_id' => null]);
            }
        }

        Cache::forget('task.stats');
        Cache::forget('task.user.'.\Auth::user()->id);

        Session::put('task.section', 'inprogress');

        return json_encode(['code' => 0]);
    }

    public function getAddComment($task_id)
    {
        $task = Task::findOrFail($task_id);

        return View::make('tasks.module.add-comment', compact('task'));
    }

    public function postStoreComment()
    {
        $task = Task::findOrFail(Input::get('task_id'));

        $task->comments()->create(['content' => Input::get('content'), 'user_id' => Auth::user()->id]);

        return json_encode(['code' => 0]);
    }


    public function postAssign()
    {
        $task_id = Input::get('task_id');
        $task = Task::findOrFail($task_id);
        $result = \Idea\Tasker\Tasker::assign($task);

        if($result['status'] == 'error'){
            Flash::error('Nie udało się znaleźć pracownika do obsługi zadania');
        }

        return Redirect::back();
    }

    public function getAttachment($file_id)
    {
        ob_start();

        $file = TaskFile::findOrFail($file_id);

        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$file->filename;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $headers = array(
            'Content-Type: '.finfo_file($finfo, $path),
        );
        $name = $file->original_filename;
        $name = preg_replace('/[^a-zA-Z0-9\-\._]/','_', $name);
  
        return Response::download($path, $name, $headers);
    }

    public function getPreview($file_id)
    {
        $file = TaskFile::findOrFail($file_id);

       return View::make('tasks.module.preview', compact('file'));
    }

    public function getPreviewDoc($file_id)
    {
        $file = TaskFile::find($file_id);
        if($file && preg_match('/^\d+$/', $file_id)){
            $filename = $file->filename;
        }else{
            $filename = $file_id;
        }

        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/emails/" . $filename;

        $response = Response::make(File::get($path), 200);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $response->header('Content-Type', finfo_file($finfo, $path));
        finfo_close($finfo);

        return $response;
    }

    public function getInsidePreview($file_id)
    {
        $file = TaskFile::findOrFail($file_id);

        Session::put('task.visibility', true);
        Session::put('task.section', 'inside-preview');
        Session::put('task.file', $file->id);

        return View::make('tasks.module.inside-preview', compact('file'));
    }

    public function postUploadEmail()
    {
        Session::put('taskupload.url', URL::previous());
        $taskMailer = new \Idea\Tasker\TaskMailer();

        $validator = Validator::make(
            Input::all(),
            array('task_file' => 'required|mimes:eml,txt')
        );
        $ext =  pathinfo(Input::file('task_file')->getClientOriginalName(), PATHINFO_EXTENSION);
        if ($validator->fails() && $ext != 'msg' )
        {
            Flash::error('Niepoprawny format pliku email. Dopuszczalne to eml,txt,msg');
            return Redirect::back();
        }

        if($ext == 'msg' )
        {
            $filename = Str::random();
            Input::file('task_file')->move(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/", $filename.'.msg');

            $command = 'msgconvert  --verbose '.$filename.'.msg';

            $process = new Process($command, Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/", null, null, 360);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            $filename = $filename.'.eml';
        }else{
            $filename = Str::random().'.eml';
            Input::file('task_file')->move(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/", $filename);
        }

        $parser = new Phemail\MessageParser();
        $message = $parser->parse(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/". $filename);


        $fromA = (new \Idea\Mail\Mail_RFC822())->parseAddressList($message->getHeaderValue('from'));

        $from_address = '';

        if(! $fromA){
            $parser = new PhpMimeMailParser\Parser();
            $parser->setPath(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/". $filename);
            $from_address = $parser->getAddresses('from');

            if(! $from_address || $from_address == '') {
                Flash::error('Niepoprawny format pliku email.');
                return Redirect::back();
            }
        }

        if(is_array($fromA)){
            foreach($fromA as $from){
                $from_address = $taskMailer->decodePersonAddressData($from->mailbox.'@'.$from->host);
            }
        }

//        if(TaskMailboxMail::where('mail', $from_address)->has('mailbox')->count() > 0){
//            Flash::error('Odrzucone źródło maila - skontaktuj się z administratorem.');
//            return Redirect::back();
//        }


        $to = (new \Idea\Mail\Mail_RFC822())->parseAddressList($message->getHeaderValue('to'));

        $to_name = [];
        $to_address = [];

        foreach($to as $to_entity){
            $to_name[] = $taskMailer->decodePersonAddressData($to_entity->personal);

            $to_address[] = mb_strtolower($taskMailer->decodePersonAddressData($to_entity->mailbox.'@'.$to_entity->host));
        }

        $cc = (new \Idea\Mail\Mail_RFC822())->parseAddressList($message->getHeaderValue('cc'));
        if($cc) {
            foreach ($cc as $to_entity) {
                $to_name[] = $taskMailer->decodePersonAddressData($to_entity->personal);

                $to_address[] = mb_strtolower($taskMailer->decodePersonAddressData($to_entity->mailbox . '@' . $to_entity->host));
            }
        }

        $user_alternative_emails = Auth::user()->emails->lists('email');

        if(!in_array( mb_strtolower( Auth::user()->email ), $to_address) && count( array_intersect(  $to_address, $user_alternative_emails) ) == 0){
            \Log::info('invalid '.mb_strtolower(Auth::user()->email), $to_address);
            Flash::error('Adresat maila nie pokrywa się z mailem pracownika - skontaktuj się z administratorem.');
            return Redirect::back();
        }

//        foreach($to_address as $address){
//            if(TaskMailboxMail::where('mail', $address)->has('mailbox')->count() > 0){
//                Flash::error('Odrzucone źródło maila - skontaktuj się z administratorem.');
//                return Redirect::back();
//            }
//        }

        return Redirect::to(url('tasks/proceed-email', [$filename]));
    }

    public function getProceedEmail($filename)
    {
        $taskGroups = TaskGroup::get();
        return View::make('tasks.proceed-email', compact('filename', 'taskGroups'));
    }

    public function postStoreEmail()
    {
        $filename = Input::get('filename');
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/". $filename;

        $parser = new \Idea\Tasker\TaskParser($path);

        $task = Task::create([
            'task_source_id' => 1,
            'source_id' => 1,
            'source_type' => 'TaskSource',
            'to_email' => implode(',', $parser->getToAddress()),
            'to_name' => implode(',', $parser->getToName()),
            'from_email' => $parser->getFromAddress(),
            'from_name' => $parser->getFromName(),
            'cc_email' => implode(',',$parser->getCcAddress()),
            'cc_name' => implode(',',$parser->getCcName()),
            'subject' => $parser->getSubject(),
            'content' => $parser->getContent(),
            'task_group_id' => Input::get('task_group_id'),
            'task_date' => $parser->getDate()
        ]);

        $task->files()->create([
            'filename' => $filename,
            'original_filename' => 'email.eml',
            'mime' => 'message/rfc822'
        ]);

        foreach ($parser->getAttachments() as $attachment) {
            $task->files()->create($attachment);
        }

        \Idea\Tasker\Tasker::attachToUser($task, Auth::user());

        return Redirect::to(Session::get('taskupload.url'));
    }

    public function getReply($task_instance_id)
    {
        $taskInstance = TaskInstance::with('task.injuries')->find($task_instance_id);
        $referer = Request::server('HTTP_REFERER');

        $footers = Auth::user()->footers()->lists('name', 'id');

        $sender = null;
        if($taskInstance->task->source_type == 'TaskMailbox'){
            $senderMail = $taskInstance->task->source->mails()->first();
            $sender = $senderMail->mail;
        }
        return View::make('tasks.module.reply', compact('taskInstance', 'referer', 'footers', 'sender'));
    }

    public function postReply()
    {
        $taskInstance = TaskInstance::find(Input::get('task_instance_id'));

        $receivers = [];

        $sender = Input::get('sender');
        if(! $sender || $sender == '' || !filter_var($sender, FILTER_VALIDATE_EMAIL)){
            Flash::error('Niepoprawny adres nadawcy');
            return Redirect::to(Input::get('referer'));
        }

        $mailer = new Idea\Mail\Mailer($taskInstance->task->mailbox);
        $mailer->setFrom($sender);
        $mailer->addAddress($taskInstance->task->from_email, $taskInstance->task->from_name);
        $receivers[] = $taskInstance->task->from_email;
        $mailer->setSubject('Re: '. $taskInstance->task->subject);

        foreach(explode(',', Input::get('emails')) as $email){
            $email = trim($email);
            if($email != '' && filter_var($email, FILTER_VALIDATE_EMAIL)){
                $mailer->addAddress($email);
                $receivers[] = $email;
            }
        }

        foreach(explode(',', Input::get('bcc_emails')) as $email){
            $email = trim($email);
            if($email != '' && filter_var($email, FILTER_VALIDATE_EMAIL)){
                $mailer->addBccAddress($email);
                $receivers[] = $email;
            }
        }

        foreach(explode(',', Input::get('cc_emails')) as $email){
            $email = trim($email);
            if($email != '' && filter_var($email, FILTER_VALIDATE_EMAIL)){
                $mailer->addCcAddress($email);
                $receivers[] = $email;
            }
        }

        $html = Input::get('content');
        preg_match_all('/(src|background)=["\'](.*)["\']/Ui', $html, $images);
        if (array_key_exists(2, $images)) {
            foreach ($images[2] as $imgindex => $url) {
                $cid = md5($url) . '@phpmailer.0';
                $file_id = basename($url);
                $file_path = $url;
                if (str_contains($url, ['/tasks/preview-doc/'])) {
                    $file_path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/emails/" . $file_id;
                    $filename = $file_id;
                }
                if (file_exists($file_path) && $mailer->addEmbeddedImage($file_path, $cid, $filename) ) {
                    $html = preg_replace(
                        '/' . $images[1][$imgindex] . '=["\']' . preg_quote($url, '/') . '["\']/Ui',
                        $images[1][$imgindex] . '="cid:' . $cid . '"',
                        $html
                    );
                }
            }
        }

        if(Input::get('footer_id'))
        {
            $footer = UserFooter::find(Input::get('footer_id'));
            if($footer) {
                $html = str_replace('%%FOOTER%%', $footer->footer, $html);
            }
        }

        $mailer->setBody($html);

        if(Input::hasFile('attachment')) {
            foreach (Input::file('attachment') as $attachment) {
                $mailer->addAttachment($attachment->getPathName(), $attachment->getClientOriginalName());
            }
        }

        foreach($taskInstance->task->files as $file){
            if($file->original_filename != 'email.eml') {
                $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$file->filename;
                $mailer->addAttachment($path, $file->filename);
            }
        }

        foreach (Input::get('files', []) as $file_id)
        {
            $file = InjuryFiles::find($file_id);
            if($file->type == 2){
                $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$file->file;
            }else{
                $documentType = InjuryDocumentType::find($file->category);
                $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".$documentType->short_name."/".$file->file;
            }
            $pathParts = pathinfo($path);
            $name = rand('10000','99999');

            $mailer->addAttachment($path, $name . '.' . $pathParts['extension']);
        }

        $mail_filename = substr(md5(time().'xx'.rand(0, 9999)), 7, 16).'.eml';
        file_put_contents(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$mail_filename, $mailer->getMailString());
        $file = $taskInstance->task->files()->create([
            'filename' => $mail_filename,
            'original_filename' => 'odpowiedz_'.date('Y_m_d_H_i').'.eml',
            'mime' => 'message/rfc822'
        ]);

        $mailer->send();

        $taskInstance->task->replies()->create([
            'user_id' => Auth::user()->id,
            'task_file_id' => $file->id,
            'receivers' => implode(',',$receivers)
        ]);

        return Redirect::to(Input::get('referer'));
    }

    public function getForward($task_instance_id)
    {
        $taskInstance = TaskInstance::with('task.injuries')->find($task_instance_id);
        $referer = Request::server('HTTP_REFERER');

        $footers = Auth::user()->footers()->lists('name', 'id');

        $sender = null;
        if($taskInstance->task->source_type == 'TaskMailbox'){
            $senderMail = $taskInstance->task->source->mails()->first();
            $sender = $senderMail->mail;
        }

        return View::make('tasks.module.forward', compact('taskInstance', 'referer', 'footers', 'sender'));
    }

    public function postForward()
    {
        $taskInstance = TaskInstance::find(Input::get('task_instance_id'));

        $sender = Input::get('sender');
        if(! $sender || $sender == '' || !filter_var($sender, FILTER_VALIDATE_EMAIL)){
            Flash::error('Niepoprawny adres nadawcy');
            return Redirect::to(Input::get('referer'));
        }

        $mailer = new Idea\Mail\Mailer($taskInstance->task->mailbox);
        $mailer->setFrom($sender);

        $receivers = [];
        foreach(explode(',', Input::get('recipient')) as $email){
            $email = trim($email);
            if($email != '' && filter_var($email, FILTER_VALIDATE_EMAIL)){
                $mailer->addAddress($email);
                $receivers[] = $email;
            }
        }
        $mailer->setSubject('Fwd: '. $taskInstance->task->subject);

        foreach(explode(',', Input::get('bcc_emails')) as $email){
            $email = trim($email);
            if($email != '' && filter_var($email, FILTER_VALIDATE_EMAIL)){
                $mailer->addBccAddress($email);
                $receivers[] = $email;
            }
        }

        $html = Input::get('content');
        preg_match_all('/(src|background)=["\'](.*)["\']/Ui', $html, $images);
        if (array_key_exists(2, $images)) {
            foreach ($images[2] as $imgindex => $url) {
                $cid = md5($url) . '@phpmailer.0';
                $file_id = basename($url);
                $file_path = $url;
                if (str_contains($url, ['/tasks/preview-doc/'])) {
                    $file_path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/emails/" . $file_id;
                    $filename = $file_id;
                }
                if (file_exists($file_path) && $mailer->addEmbeddedImage($file_path, $cid, $filename) ) {
                    $html = preg_replace(
                        '/' . $images[1][$imgindex] . '=["\']' . preg_quote($url, '/') . '["\']/Ui',
                        $images[1][$imgindex] . '="cid:' . $cid . '"',
                        $html
                    );
                }
            }
        }

        if(Input::get('footer_id'))
        {
            $footer = UserFooter::find(Input::get('footer_id'));
            if($footer) {
                $html = str_replace('%%FOOTER%%', $footer->footer, $html);
            }
        }

        $mailer->setBody($html);

        if(Input::hasFile('attachment')) {
            foreach (Input::file('attachment') as $attachment) {
                $mailer->addAttachment($attachment->getPathName(), $attachment->getClientOriginalName());
            }
        }

        foreach($taskInstance->task->files as $file){
            if($file->original_filename != 'email.eml') {
                $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/emails/" . $file->filename;
                $mailer->addAttachment($path, $file->filename);
            }
        }

        foreach (Input::get('files', []) as $file_id)
        {
            $file = InjuryFiles::find($file_id);
            if($file->type == 2){
                $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$file->file;
            }else{
                $documentType = InjuryDocumentType::find($file->category);
                $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".$documentType->short_name."/".$file->file;
            }
            $pathParts = pathinfo($path);
            $name = rand('10000','99999');

            $mailer->addAttachment($path, $name . '.' . $pathParts['extension']);
        }

        $mail_filename = substr(md5(time().'xx'.rand(0, 9999)), 7, 16).'.eml';
        file_put_contents(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$mail_filename, $mailer->getMailString());
        $file = $taskInstance->task->files()->create([
            'filename' => $mail_filename,
            'original_filename' => 'przekazanie_'.date('Y_m_d_H_i').'.eml',
            'mime' => 'message/rfc822'
        ]);

        $mailer->send();

        $taskInstance->task->forwards()->create([
            'user_id' => Auth::user()->id,
            'task_file_id' => $file->id,
            'receivers' => implode(',',$receivers)
        ]);

        return Redirect::to(Input::get('referer'));
    }

    public function getInjuryTasks($injury_id)
    {
        $injury = Injury::with('tasks','tasks.source','tasks.comments','tasks.files','tasks.group','tasks.type', 'tasks.currentInstance', 'tasks.currentInstance.step')->find($injury_id);
        $content = true;
        return View::make('injuries.card_file.tasks', compact('injury', 'content'))->render();
    }

    public function getDetachInjury($task_id, $injury_id)
    {
        return View::make('tasks.module.detach-injury', compact('task_id', 'injury_id'));
    }

    public function postDetachInjury($task_id, $injury_id)
    {
        $task = Task::find($task_id);
        $injury = Injury::find($injury_id);

        $task->injuries()->detach($injury->id);

        $link = $task->case_nb.' <a class="task-show-details" data-task="'.$task->currentInstance->id.'" >
                        przejdź
                    </a> Powód: '.Input::get('content');
        Histories::history($injury->id, 222, Auth::user()->id, $link );

        $taskFile = $task->files()->first();
        $file = InjuryFiles::where('file', $taskFile->filename)->where('injury_id', $injury->id)->where('type' , 2)->first();
        if($file) $file->delete();

        $taskFiles = TaskFile::where('task_id', $task_id)->get();
        foreach ($taskFiles as $taskFile) {
            $file = InjuryFiles::where('file', $taskFile->filename)->where('injury_id', $injury->id)->where('type' , 2)->first();
            if($file) $file->delete();
        }

        if($task->currentInstance && $task->currentInstance->completed){
            $taskInstance = $task->currentInstance;
             $taskInstance->update([
                'date_complete' => null,
                'task_step_id' => 2
            ]);

            TaskStepHistory::create([
                'task_instance_id' => $taskInstance->id,
                'task_step_id' => 2
            ]);

            Cache::forget('task.stats');
            Cache::forget('task.user.'.\Auth::user()->id);
        }


        return json_encode(['code' => 0]);
    }
}