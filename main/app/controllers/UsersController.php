<?php

use Carbon\Carbon;

class UsersController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:uzytkownicy#lista_uzytkownikow#wejscie', ['only' => 'getIndex', 'getSignature', 'getShowSignature']);
        $this->beforeFilter('permitted:uzytkownicy#dodawanie_uzytkownika#wejscie', ['only' => ['getCreate', 'postStore']]);
        $this->beforeFilter('permitted:uzytkownicy#podglad_uzytkownika#wejscie', ['only' => 'getShow']);
        $this->beforeFilter('permitted:uzytkownicy#edycja_uzytkownika#grupy', ['only' => ['getAddGroups', 'postAppendGroups']]);
        $this->beforeFilter('permitted:uzytkownicy#edycja_uzytkownika#wejscie', ['only' => ['getEdit', 'postUpdate']]);
        $this->beforeFilter('permitted:uzytkownicy#podglad_uzytkownika#blokowanie_konta', ['only' => ['getLockAccount', 'postLockAccount', 'getUnlockAccount', 'postUnlockAccount']]);
        $this->beforeFilter('permitted:uzytkownicy#podglad_uzytkownika#ustawianie_hasla', ['only' => ['getResetPassword', 'postGenerate']]);
        $this->beforeFilter('permitted:uzytkownicy#lista_uzytkownikow#ustawianie_podpisu', ['only' => ['postUploadSignature', 'postSignature']]);
    }

    public function getRegeneratePassword()
    {
        $user = Auth::user();
        if (\Carbon\Carbon::now()->diffInDays($user->password_expired_at,false)<1 )
        {
            return View::make('auth.user_password_require', compact('user'));
        }

        return View::make('auth.user_password_manage', compact('user'));

    }

    public function postRegeneratePassword()
    {

        if (!Input::has('password') || !Input::has('password_confirmation')) {
            return Redirect::back()->withInput(Input::except('password'))->with('status', ['type' => 'danger', 'text' => 'Prosimy wypełnić wszystkie pola.']);
        }

        if (strlen(Input::input('password')) < 8) {
            return Redirect::back()->withInput(Input::except('password'))->with('status', ['type' => 'danger', 'text' => 'Wprowadź hasło zawierające minimum 8 znaków.']);
        }

        if (!preg_match('/^(?=.*[a-z|A-Z])(?=.*[A-Z])(?=.*\d).+$/', Input::input('password'))) {
            return Redirect::back()->withInput(Input::except('password'))->with('status', ['type' => 'danger', 'text' => 'Wprowadź hasło składające się małych i wielkich liter, liczb.']);
        }

        if (Input::input('password') != Input::input('password_confirmation')) {
            return Redirect::back()->withInput(Input::except('password'))->with('status', ['type' => 'danger', 'text' => 'Podane hasła są różne.']);
        }

        $user = Auth::user();

        if (Hash::check(Input::input('password'), $user->password)) {
            return Redirect::back()->withInput(Input::except('password'))->with('status', ['type' => 'danger', 'text' => 'Wprowadzone nowe hasło było już przez Ciebie wykorzystane. Wprowadź inną propozycję.']);
        }

        foreach ($user->passwords()->where('created_at', '>=', Carbon::now()->subMonths(3))->get() as $password) {
            if (Hash::check(Input::input('password'), $password->password)) {
                return Redirect::back()->withInput(Input::except('password'))->with('status', ['type' => 'danger', 'text' => 'Wprowadzone nowe hasło było już przez Ciebie wykorzystane. Wprowadź inną propozycję.']);
            }
        }

        $user->passwords()->create(['password' => $user->password]);

        $user->password = Hash::make(Input::input('password'));

        $user->password_expired_at = Carbon::now()->addDays(30);

        $user->save();

        Flash::success('Hasło zostało zmienione');
        return Redirect::to('/');

    }

    public function getIndex()
    {
        $query = User::with('logins', 'footers');

        if(Input::has('filter_login') && Input::get('filter_login')) {
            $query->where('login','like', Input::get('filter_login').'%');
        }
        if(Input::has('filter_name') && Input::get('filter_name')) {
            $query->where('name','like', Input::get('filter_name').'%');
        }
        if(Input::has('filter_email') && Input::get('filter_email')) {
            $query->where('email','like', Input::get('filter_email').'%');
        }

        $users = $query->orderBy('name')->paginate(Session::get('search.pagin', '10'));

        return View::make('settings.users.index', compact('users'));
    }

    public function getCreate()
    {
        $departments = ['' => '--- wybierz ---'] + Department::lists('name', 'id');
        $teams = ['' => '--- wybierz ---'] + Team::lists('name', 'id');


        return View::make('settings.users.create' , compact('departments', 'teams'));
    }

    public function postStore()
    {
        $validator = Validator::make(Input::all(), [
            'name' => 'required|max:255',
            'login' => 'required|max:255|unique:users'
        ]);

        if($validator -> fails()){
            return json_encode(['error' => $validator->errors()->all()]);
        }else{
            $inputs = Input::all();
            if(! Input::has('is_external')) $inputs['is_external'] = 0;
            if(Input::get('active_to') == '') $inputs['active_to'] = null;

            $user = User::create($inputs);
            $user->update([
                'password_expired_at' => Carbon::now()
            ]);

            if($user->active_to && $user->active_to->startOfDay()->lte(Carbon::now()) && ! $user->locked_at)
            {
                $user->locked_at = $user->active_to;
                $user->save();
            }
        }

        Flash::success('Użytkownik '.$user->name.' został utworzony.');
        return json_encode(['code' => 1, 'url' => url('settings/users/show', [$user->id])]);
    }

    public function getEdit($user_id)
    {
        $user_db = User::find( $user_id );

        $departments = ['' => '--- wybierz ---'] + Department::lists('name', 'id');
        $teams = ['' => '--- wybierz ---'] + Team::lists('name', 'id');

        return View::make('settings.users.edit', compact('user_db', 'departments', 'teams'));
    }

    public function postUpdate($user_id)
    {
        $validator = Validator::make(Input::all(), [
            'name' => 'required|max:255'
        ]);
        if($validator -> fails()){
            return json_encode(['error' => 'Wystąpił błąd.']);
        }else{
            $inputs = Input::all();
            if(! Input::has('is_external')) $inputs['is_external'] = 0;
            if(Input::get('active_to') == '') $inputs['active_to'] = null;

            $user = User::findOrFail($user_id);
            $user->update($inputs);

            if($user->active_to && $user->active_to->startOfDay()->lte(Carbon::now()) && ! $user->locked_at)
            {
                $user->locked_at = $user->active_to;
                $user->save();
            }
        }
        Flash::success('Dane użytkownika '.$user->name.' zostały zaktualizowane.');
        return json_encode(['code' => 0]);
    }

    public function getEditPassword($user_id)
    {
        $user_db = User::findOrFail($user_id);
        return View::make('settings.users.password', compact('user_db'));
    }

    public function postPassword( $user_id)
    {
        $validator = Validator::make(Input::all(), [
            'password' => 'required|confirmed|min:1',
        ]);

        if($validator -> fails()){
            return json_encode(['error' => $validator->errors()->all()]);
        }

        $user = User::findOrFail( $user_id );
        $user->password = Hash::make(Input::get('password'));
        $user->save();

        Flash::success('Hasło użytkownika '.$user->name.' zostało zmienione.');
        return json_encode(['code' => 0]);
    }

    public function getDelete($user_id)
    {
        $user_db = User::findOrFail($user_id);
        return View::make('settings.users.delete', compact('user_db'));
    }

    public function postDelete($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->delete();

        Flash::success('Użytkownik '.$user->name.' został usunięty.');
        return json_encode(['code' => 0]);
    }

    public function postUploadSignature()
    {
        $file = Input::file('file');
        $destinationPath = public_path('templates-src/');
        $original_name = $file->getClientOriginalName();
        $filename = time().md5(time().$file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
        $file->move($destinationPath, $filename);

        return json_encode(['filename'=>$filename]);
    }

    public function getSignature($user_id){
        $user_db = User::findOrFail($user_id);
        return View::make('settings.users.signature', compact('user_db'));
    }

    public function postSignature($user_id){
        $user = User::findOrFail($user_id);
        if(Input::has('filename')){
            $user->update(['signature'=>Input::input('filename')]);
        }
        return json_encode(['code' => 0]);
    }
    public function getShowSignature($filename)
    {
        $path = public_path('templates-src/') . $filename;
        $mime = mime_content_type($path);

        $response = Response::make(file_get_contents($path), 200, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);

        return $response;
    }

    public function getShow($user_id)
    {
        $user = User::with(
            'owners',
            'groupHistories.userGroup',
            'groupHistories.triggererUser',
            'ownerHistories.owner',
            'ownerHistories.triggererUser',
            'logins',
            'vmanage_companies',
            'vmanage_companyHistories.vmanage_company',
            'vmanage_companyHistories.triggererUser',
            'taskGroups',
            'taskGroupHistories.taskGroup',
            'department',
            'team'
        )->find($user_id);

        return View::make('settings.users.show', compact('user'));
    }

    public function getGenerate($id){
        $user = User::findOrFail($id);

        return View::make('settings.users.generate', compact('user'));
    }

    public function postGenerate($id){
        $user = User::findOrFail($id);

        $user->generatePassword();

        Flash::success('Wygenerowano nowe hasło');

        return json_encode(['code'=>0]);

    }

    public function getAddGroups($user_id)
    {
        $user = User::find($user_id);
        $groups = UserGroup::orderBy('name')->get();

        return View::make('settings.users.add-groups', compact('user', 'groups'));
    }

    public function postAppendGroups($user_id)
    {
        $user = User::find($user_id);

        $user_groups = $user->groups->lists('id');
        $groups = Input::get('groups', []);

        $new_groups = array_diff($groups, $user_groups);
        $del_groups = array_diff($user_groups, $groups);


        foreach($del_groups as $del_group)
        {
            UserGroupHistory::create([
                'triggerer_user_id' => Auth::user()->id,
                'user_id'   =>  $user->id,
                'user_group_id' => $del_group,
                'mode' => 'detach'
            ]);

        }

        foreach($new_groups as $module_id => $new_group)
        {
            if($new_group > 0) {
                UserGroupHistory::create([
                    'triggerer_user_id' => Auth::user()->id,
                    'user_id' => $user->id,
                    'user_group_id' => $new_group,
                    'mode' => 'attach'
                ]);

            }
        }

        $user->groups()->sync(Input::get('groups', []));

        Flash::success('Zaktualizowano przypisane grupy');
        return json_encode(['code'=>0]);
    }

    public function getResetPassword($user_id)
    {
        $user = User::find($user_id);
        return View::make('settings.users.generate', compact('user'));
    }

    public function getLockAccount($user_id)
    {
        $user = User::find($user_id);
        return View::make('settings.users.lock', compact('user'));
    }

    public function postLockAccount($user_id)
    {
        $user = User::find($user_id);
        $user->locked_at = Carbon::now();
        $user->locked_manual = 1;
        $user->save();

        Flash::success('Zablokowano konto');

        return json_encode(['code'=>0]);
    }

    public function getUnlockAccount($user_id)
    {
        $user = User::find($user_id);
        return View::make('settings.users.unlock', compact('user'));
    }

    public function postUnlockAccount($user_id)
    {
        $user = User::find($user_id);
        $user->locked_at = null;
        $user->locked_manual = null;
        $user->save();

        Flash::success('Odblokowano konto');

        return json_encode(['code'=>0]);
    }

    public function getManageContractors($user_id)
    {
        $user = User::with('owners')->find($user_id);
        $owners = Owners::orderBy('name')->get();

        return View::make('settings.users.manage-contractors', compact('user', 'owners'));
    }

    public function postAppendContractors( $user_id)
    {
        $user = User::findOrFail($user_id);

        $user_contractors = $user->owners->lists('id');
        $owners = Input::get('owners', []);

        $new_owners = array_diff($owners, $user_contractors);
        $del_owners = array_diff($user_contractors, $owners);


        foreach($del_owners as $del_owner)
        {
            UserOwnerHistory::create([
                'triggerer_user_id' => Auth::user()->id,
                'user_id'   =>  $user->id,
                'owner_id' => $del_owner,
                'mode' => 'detach'
            ]);

        }

        foreach($new_owners as $new_owner)
        {
            if($new_owner > 0) {
                UserOwnerHistory::create([
                    'triggerer_user_id' => Auth::user()->id,
                    'user_id' => $user->id,
                    'owner_id' => $new_owner,
                    'mode' => 'attach'
                ]);

            }
        }

        $user->owners()->sync(Input::get('owners', []));

        $inputs = Input::all();
        if(! Input::has('without_restrictions')) $inputs['without_restrictions'] = 0;

        $user->update($inputs);

        Flash::success('Zaktualizowano przypisanych kontrahentów');

        return json_encode(['code'=>0]);
    }

    public function getManageCompanies($user_id)
    {
        $user = User::with('vmanage_companies')->find($user_id);
        $companies = VmanageCompany::orderBy('name')->get();

        return View::make('settings.users.manage-companies', compact('user', 'companies'));
    }

    public function postAppendCompanies( $user_id)
    {
        $user = User::with('vmanage_companies')->findOrFail($user_id);

        $user_companies = $user->vmanage_companies->lists('id');
        $companies = Input::get('companies', []);

        $new_companies = array_diff($companies, $user_companies);
        $del_companies = array_diff($user_companies, $companies);


        foreach($del_companies as $del_company)
        {
            UserCompanyHistory::create([
                'triggerer_user_id' => Auth::user()->id,
                'user_id'   =>  $user->id,
                'vmanage_company_id' => $del_company,
                'mode' => 'detach'
            ]);
        }

        foreach($new_companies as $new_company)
        {
            if($new_company > 0) {
                UserCompanyHistory::create([
                    'triggerer_user_id' => Auth::user()->id,
                    'user_id' => $user->id,
                    'vmanage_company_id' => $new_company,
                    'mode' => 'attach'
                ]);
            }
        }

        $user->vmanage_companies()->sync(Input::get('companies', []));

        $inputs = Input::all();
        if(! Input::has('without_restrictions_vmanage')) $inputs['without_restrictions_vmanage'] = 0;

        $user->update($inputs);

        Flash::success('Zaktualizowano przypisanych firm');

        return json_encode(['code'=>0]);
    }

    public function getManageTasks($user_id)
    {
        $user = User::with('taskGroups')->find($user_id);

        $groups = TaskGroup::lists('name', 'id');

        return View::make('settings.users.manage-tasks', compact('user', 'groups'));
    }

    public function postAppendTasks( $user_id)
    {
        $user = User::with('taskGroups')->findOrFail($user_id);

        $user_task_groups = $user->taskGroups->lists('id');
        $task_groups = Input::get('groups', []);

        $new_task_groups = array_diff($task_groups, $user_task_groups);
        $del_task_groups = array_diff($user_task_groups, $task_groups);


        foreach($del_task_groups as $del_task_group)
        {
            UserTaskGroupHistory::create([
                'triggerer_user_id' => Auth::user()->id,
                'user_id'   =>  $user->id,
                'task_group_id' => $del_task_group,
                'mode' => 'detach'
            ]);
        }

        foreach($new_task_groups as $new_task_group)
        {
            if($new_task_group > 0) {
                UserTaskGroupHistory::create([
                    'triggerer_user_id' => Auth::user()->id,
                    'user_id' => $user->id,
                    'task_group_id' => $new_task_group,
                    'mode' => 'attach'
                ]);
            }
        }

        $user->taskGroups()->sync(Input::get('groups', []));

        $inputs = Input::all();
        if(! Input::has('without_restrictions_task_group')) $inputs['without_restrictions_task_group'] = 0;

        $user->update($inputs);

        Flash::success('Zaktualizowano przypisanych gróp zadań');

        return json_encode(['code'=>0]);
    }

    public function getFooters($user_id){
        $user_db = User::findOrFail($user_id);
        return View::make('settings.users.footers', compact('user_db'));
    }

    public function getFooterAdd($user_id){
        $user_db = User::findOrFail($user_id);
        return View::make('settings.users.footer', compact('user_db'));
    }

    public function postFooter($user_id)
    {
        $user_db = User::findOrFail($user_id);
        $html = Input::get('content');

        $dom = \Idea\Helpers\TagPrefixFixer::clean(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $images = $dom->getElementsByTagName('img');
        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            if(!preg_match('!^data\:!',$src)&& ! filter_var($src, FILTER_VALIDATE_URL)){
                $base64 = getDataURI(public_path($src));
                $image->setAttribute("src", $base64);
            }
        }
        $html = preg_replace('#^data:image/\w+;base64,#i', '', $dom->saveHTML()) ;

        $footer = $user_db->footers()->create([
            'name' => Input::get('name'),
            'footer' => $html
        ]);

        Flash::success('Dodano stopkę '.$user_db->name);

        return Redirect::to('settings/users/footers/'.$footer->user_id);
    }

    public function getFooterShow($footer_id){
        $footer = UserFooter::findOrFail($footer_id);
        return View::make('settings.users.footer-show', compact('footer'));
    }

    public function postUploadFooterImg(){
        $file = Input::file('file');
        $path_parts = pathinfo($file->getClientOriginalName());
        $extension = $path_parts['extension'];

        $filename = str_random().'.'. $extension;
        $file->move(public_path('images/footers'), $filename);

        Image::make(public_path('images/footers/'.$filename))->widen(300)->save();

        return '/images/footers/'.$filename;
    }

    public function getFooterEdit($footer_id){
        $footer = UserFooter::findOrFail($footer_id);
        return View::make('settings.users.footer-edit', compact('footer'));
    }

    public function postFooterUpdate($footer_id)
    {
        $footer = UserFooter::findOrFail($footer_id);

        $html = Input::get('content');

        $dom = \Idea\Helpers\TagPrefixFixer::clean(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            if(!preg_match('!^data\:!',$src) && ! filter_var($src, FILTER_VALIDATE_URL)){
                $base64 = getDataURI(public_path($src));
                $image->setAttribute("src", $base64);
            }
        }
        $html = preg_replace('#^data:image/\w+;base64,#i', '', $dom->saveHTML()) ;


        $footer->update([
            'name' => Input::get('name'),
            'footer' => $html
        ]);

        return Redirect::to('settings/users/footers/'.$footer->user_id);
    }

    public function getDeleteFooter($footer_id)
    {
        $footer = UserFooter::find($footer_id);

        return View::make('settings.users.footer-delete', compact('footer'));
    }

    public function postDeleteFooter($footer_id)
    {
        $footer = UserFooter::find($footer_id);
        $footer->delete();

        return json_encode(['code' => 0]);
    }

    public function getAddEmail($user_id){
        $user_db = User::findOrFail($user_id);
        return View::make('settings.users.add-email', compact('user_db'));
    }

    public function postAddEmail($user_id){
        $user_db = User::findOrFail($user_id);
        $user_db->emails()->create(['email' => Input::get('email')]);

        return json_encode(['code' => 0]);
    }

    public function getDeleteEmail($email_id)
    {
        $email = UserEmail::find($email_id);

        return View::make('settings.users.email-delete', compact('email'));
    }

    public function postDeleteEmail($email_id)
    {
        $email = UserEmail::find($email_id);
        $email->delete();

        return json_encode(['code' => 0]);
    }
}
