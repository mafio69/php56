<?php

class TaskManageTypesController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('permitted:zadania#slownik_typow_spraw#wejscie');
    }

    public function getIndex()
    {
        $taskGroups = TaskGroup::with('taskTypes', 'taskSubGroups', 'taskSubGroups.taskTypes')->orderBy('ord')->get();
        return View::make('tasks.manage-types.index', compact('taskGroups'));
    }

    public function getCreate($task_group_id, $task_subgroup_id = null)
    {
        $taskGroup = TaskGroup::find($task_group_id);
        $taskSubGroup = TaskGroup::find($task_subgroup_id);

        return View::make('tasks.manage-types.create', compact('taskGroup', 'taskSubGroup'));
    }

    public function postStore($task_group_id, $task_subgroup_id = null)
    {
        TaskType::create([
            'task_group_id' => $task_group_id,
            'task_subgroup_id' => $task_subgroup_id,
            'name' => Input::get('name')
        ]);

        return Response::json(['code' => 0]);
    }

    public function getEdit($task_type_id)
    {
        $taskType = TaskType::find($task_type_id);
        return View::make('tasks.manage-types.edit', compact('taskType'));
    }

    public function postUpdate($task_type_id)
    {
        $taskType = TaskType::find($task_type_id);
        $taskType->update(Input::all());
        return Response::json(['code' => 0]);
    }

    public function getDelete($task_type_id)
    {
        $taskType = TaskType::find($task_type_id);
        return View::make('tasks.manage-types.delete', compact('taskType'));
    }

    public function postDelete($task_type_id)
    {
        $taskType = TaskType::find($task_type_id);
        $taskType->delete();

        return Response::json(['code' => 0]);
    }
}