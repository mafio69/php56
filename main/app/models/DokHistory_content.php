
<?php
 
 
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
 
class DokHistory_content extends Eloquent
{
    protected $table = 'dok_history_content';

    protected $guarded = array();

    public $timestamps = false;
}