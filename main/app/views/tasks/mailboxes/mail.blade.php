<div class="list-group-item row">
    <div class="from-group form-group-sm col-sm-12 col-md-5">
        <label class="col-sm-3 control-label text-right">mail:</label>
        <div class="col-sm-9">
            <input name="mails[]" type="text" class="form-control" placeholder="mail" required>
        </div>
    </div>
    <div class="form-group form-group-sm col-sm-12 col-md-5">
        <label class="col-sm-3 control-label text-right">grupa zadań:</label>
        <div class="col-sm-9">
            <select name="mail_task_groups[]" class="form-control required" required>
                <option @if(!$task_group_id) selected="selected" @endif  value="">
                    --- wybierz ---
                </option>
                @foreach($taskGroups as $group)
                    <option value="{{ $group->id }}" @if($task_group_id == $group->id) selected @endif>
                        {{ $group->name }}
                    </option>
                    @endforeach
            </select>
        </div>
    </div>
    <div class="form-group form-group-sm col-sm-12 col-md-2">
        <div class="btn btn-danger btn-sm remove">
            <i class="fa fa-trash-o"></i>
        </div>
    </div>
</div>