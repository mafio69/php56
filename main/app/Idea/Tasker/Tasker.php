<?php


namespace Idea\Tasker;


use Cache;
use Carbon\Carbon;
use TaskInstance;
use TaskStepHistory;

class Tasker
{
    public static function globalStats()
    {
        return Cache::remember('task.stats', 60, function()
        {
            $stats = [];
            $stats['unassigned'] = \Task::has('currentInstance', '<', 1)->count();
            $stats['new'] = \Task::whereHas('currentInstance', function($query){ $query->where('task_step_id', 1);})->count();
            $stats['inprogress'] = \Task::whereHas('currentInstance', function($query){ $query->where('task_step_id', 2);})->count();
            $stats['complete'] = \Task::whereHas('currentInstance', function($query){ $query->where('task_step_id', 4);})->count();
            $stats['complete-without-action'] = \Task::whereHas('currentInstance', function($query){ $query->where('task_step_id', 5);})->count();
            return $stats;
        });
    }

    public static function stats()
    {
        return Cache::remember('task.user.'.\Auth::user()->id, 60, function()
        {
            $stats = [];
            $stats['new'] = TaskInstance::where('task_step_id', 1)->where('user_id', \Auth::user()->id)->count();
            $stats['inprogress'] = TaskInstance::where('task_step_id', 2)->where('user_id', \Auth::user()->id)->count();
            $stats['complete'] = TaskInstance::whereIn('task_step_id', [4,5,3])->where('user_id', \Auth::user()->id)->count();
            return $stats;
        });
    }

    public static function newTask()
    {
        return TaskInstance::where('user_id', \Auth::user()->id)->where('task_step_id', 1)->first();
    }

    public static function newTasks()
    {
        return TaskInstance::with('task', 'task.type', 'task.sourceType')
            ->join('tasks', function($join)
            {
                $join->on('tasks.id', '=', 'task_instances.task_id');
            })
            ->where('task_instances.user_id', \Auth::user()->id)
            ->where('task_instances.task_step_id', 1)
            ->orderByRaw("FIELD(tasks.task_source_id , '2') DESC")
            ->orderBy('task_instances.task_id', 'asc')
            ->select('task_instances.*')->get();
    }

    public static function inprogressTasks()
    {
        return TaskInstance::with('task', 'task.type', 'task.sourceType')
                ->join('tasks', function($join)
                {
                    $join->on('tasks.id', '=', 'task_instances.task_id');
                })
                ->where('task_instances.user_id', \Auth::user()->id)
                ->where('task_instances.task_step_id', 2)
                ->orderByRaw("FIELD(tasks.task_source_id , '2') DESC")
                ->orderBy('task_instances.date_collect','asc')->select('task_instances.*')->get();
    }

    public static function completeTasks()
    {
        return TaskInstance::with('task', 'task.type', 'task.sourceType')
            ->join('tasks', function($join)
            {
                $join->on('tasks.id', '=', 'task_instances.task_id');
            })
            ->where('task_instances.user_id', \Auth::user()->id)
            ->whereIn('task_instances.task_step_id', [3,4,5])
            ->orderByRaw("FIELD(tasks.task_source_id , '2') DESC")
            ->orderBy('task_instances.date_complete','desc')
            ->select('task_instances.*')->get();
    }

    public static function taskInstance($task_instance_id)
    {
        return TaskInstance::findOrFail($task_instance_id);
    }

    public static function assign($task, $excluded = [])
    {
        $assignments = \TaskAssignment::where('email_from', 'like', '%'.$task->from_email)->has('user')->get();

        if($assignments->count() > 0)
        {
            $query = \User::with(
                [
                    'taskInstances' => function($query) use($task){
                        $query->where('created_at', '>=', date('Y-m-d').' 00:00:00');
                    }
                ]
            )->whereHas('taskInstances', function($query) use($task){
                $query->where('task_id', $task->id);
            }, '<', 1);

            $query->whereIn('id', $assignments->lists('user_id'));

            $user = $query->get()->sortBy(function($user)
            {
                return $user->taskInstances->count();
            })->first();

            if($user) {
                return self::attachToUser($task, $user);
            }
        }

        $query = self::getTaskUsersQuery($task, $excluded);

        $user = $query->get()->sortBy(function($user)
                    {
                        return $user->taskInstances->count();
                    })->first();

        if($user) {
            return self::attachToUser($task, $user);
        }

        \Log::info('unattachable', [$task->id, $assignments->toArray(), $excluded]);
        return ['status' => 'error'];
    }

    public static function attachToUser($task, $user)
    {
        $instance = TaskInstance::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'task_step_id' => 1
        ]);

        TaskStepHistory::create([
            'task_instance_id' => $instance->id,
            'task_step_id' => 1
        ]);

        $task->update([
            'current_task_instance_id' => $instance->id
        ]);

        return ['status' => 'success', 'task_instance_id' => $instance->id];
    }

    /**
     * @param $task
     * @param $excluded
     * @return \Illuminate\Database\Eloquent\Builder|\User
     */
    public static function getTaskUsersQuery($task, $excluded)
    {
        $query = \User::has('taskAssignments', '<', 1)
            ->where(function ($query) use ($task) {
                $query->whereHas('taskGroups', function ($query) use ($task) {
                    $query->where('id', $task->task_group_id);
                })->orWhere('without_restrictions_task_group', 1);
            })->whereHas('taskExcludes', function ($query) {
                $query->whereDate('absence', '=', Carbon::now()->startOfDay());
            }, '<', 1)
            ->with(
                [
                    'taskInstances' => function ($query) use ($task) {
                        $query->where('created_at', '>=', date('Y-m-d') . ' 00:00:00');
                    }
                ]
            )->whereHas('taskInstances', function ($query) use ($task) {
                $query->where('task_id', $task->id);
            }, '<', 1);

        if (count($excluded) > 0) {
            $query->whereNotIn('id', $excluded);
        }
        return $query;
    }
}