<?php


namespace Idea\Observers;
use Cache;

class TaskInstanceObserver
{
    public function updated($taskInstance)
    {
        Cache::forget('task.stats');
        Cache::forget('task.user.'.$taskInstance->user_id);
    }

    public function created($taskInstance)
    {
        Cache::forget('task.stats');
        Cache::forget('task.user.'.$taskInstance->user_id);
    }
}