<?php
use Illuminate\Auth\UserInterface;

class ApiUser extends \Eloquent implements UserInterface {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = ['name', 'login', 'password'];

    protected $dates = ['deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    public function apiModules()
    {
        return $this->belongsToMany('ApiModule', 'api_user_api_module');
    }

    public function apiHistories()
    {
        return $this->hasMany('ApiHistory');
    }

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
}