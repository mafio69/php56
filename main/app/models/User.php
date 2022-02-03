<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class User extends Eloquent implements UserInterface, RemindableInterface {

    use SoftDeletingTrait;
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	protected $fillable = [
	    'is_external',
        'without_restrictions',
        'without_restrictions_vmanage',
        'without_restrictions_task_group',
        'login',
        'name',
        'password',
        'department_id',
        'team_id',
        'phone_number',
        'insurances_global_nr',
        'email',
        'signature',
        'active',
        'password_expired_at',
        'failed_attempts',
        'locked_at',
        'locked_manual',
        'active_to',
        'remember_token'
    ];

    protected $dates = ['password_expired_at', 'deleted_at', 'locked_at', 'active_to'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}


	public function getRememberToken()
	{
	    return $this->remember_token;
	}

	public function setRememberToken($value)
	{
	    $this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
	    return 'remember_token';
	}

    public function typ()
    {
    	//if( $this->hasRole(1) || $this->hasRole(2) )
			return '1';

		//if($this->hasRole(3))
			return '3';

		//if( $this->hasRole(999) )
			return '2';
	}

    public function custom_reports()
    {
        return $this->belongsToMany('Custom_report_type', 'user_custom_report_type', 'user_id', 'custom_report_type_id');
    }

    public function groups()
    {
        return $this->belongsToMany('UserGroup', 'user_group', 'user_id', 'user_group_id');
    }

    public function vmanage_companies()
    {
        return $this->belongsToMany('VmanageCompany', 'vmanage_company_user', 'user_id', 'vmanage_company_id');
    }

    public function vmanage_companyHistories()
    {
        return $this->hasMany('UserCompanyHistory');
    }

    public function injuries()
    {
        return $this->hasMany('Injury', 'user_id')->where('active', '=', '0');
    }

    public function documents()
    {
        return $this->hasMany('InjuryFiles', 'user_id')->where('active', '=', '0');
    }

    public function histories()
    {
        return $this->hasMany('InjuryHistory', 'user_id');
    }

    public function passwords(){
        return $this->hasMany('UserPassword');
    }

    public function logins()
    {
        return $this->hasMany('UserLogin');
    }

    public function groupHistories()
    {
        return $this->hasMany('UserGroupHistory');
    }

    public function owners()
    {
        return $this->belongsToMany('Owners', 'user_owner', 'user_id', 'owner_id');
    }

    public function ownerHistories()
    {
        return $this->hasMany('UserOwnerHistory');
    }

    public function taskGroups()
    {
        return $this->belongsToMany('TaskGroup', 'user_task_group', 'user_id', 'task_group_id');
    }

    public function taskGroupHistories()
    {
        return $this->hasMany('UserTaskGroupHistory');
    }

    public function taskInstances()
    {
        return $this->hasMany('TaskInstance', 'user_id');
    }

    public function department()
    {
        return $this->belongsTo('Department');
    }

    public function team()
    {
        return $this->belongsTo('Team');
    }

    public function taskExcludes()
    {
        return $this->hasMany('TaskExclude');
    }

    public function taskAssignments()
    {
        return $this->hasMany('TaskAssignment');
    }

    public function footers()
    {
        return $this->hasMany('UserFooter');
    }

    public function emails()
    {
        return $this->hasMany('UserEmail');
    }

    public function generatePassword(){
        $this->passwords()->create(['password'=>$this->password]);

        $new_password = str_random(8);

        $this->password = Hash::make($new_password);
        $this->password_expired_at = \Carbon\Carbon::now();
        $this->locked_at = null;
        $this->failed_attempts = null;

        $this->save();

        if(!$this->email&&$this->email==''){
            \Log::error('Próba wysłania maila osobie, która nie ma maila'.$this->id);
        }
        else{
            \Log::info('password generated', ['login' => $this->login, 'password' => $new_password, 'base_url' => Config::get('webconfig.APP_URL', 'http://localhost')]);
            $data = ['name' => $this->name, 'login' => $this->login, 'password' => $new_password, 'base_url' => Config::get('webconfig.APP_URL', 'http://localhost')];
            Mail::send('emails.password-regenerate', $data, function($msg)
            {
                $msg->to($this->email)->subject('Hasło do systemu CAS');
            });
        }
    }

    public function can($permission) {
        $permissions = Session::get('permissions');
        if ($permissions == null) $permissions = [];
        if (in_array($permission, $permissions)) {
            return true;
        }
        return false;
    }
}
