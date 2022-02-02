<?php
 
class Idea_data extends Eloquent
{
    protected $table = 'idea_data';
    protected $guarded = array();

    public function user()
    {
        return $this->belongsTo('User', 'last_user_edit');
    }

    public function parameter()
    {
        return $this->belongsTo('Idea_parameters', 'parameter_id');
    }

    public function owner()
    {
        return $this->belongsTo('Owners');
    }
}