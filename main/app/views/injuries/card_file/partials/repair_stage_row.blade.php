<tr data-stage="{{ $nextRepairStage->id }}">
    <td width="250px">
        <strong>{{ $nextRepairStage->stage->name }}</strong>
    </td>
    <td>
        <input type="checkbox" name="stage[{{ $nextRepairStage->stage->id }}]" data-injury="{{ $injury->id }}" data-stage="{{ $nextRepairStage->stage->id }}" class="repair-stage" data-size="small" data-off-color="danger" data-on-text="<i class='fa fa-check' aria-hidden='true'></i>" data-off-text="<i class='fa fa-minus' aria-hidden='true'></i>" data-indeterminate="true">
    </td>
    <td>
        <div class="btn btn-sm @if($nextRepairStage->comment) btn-default @else btn-default @endif show-comment" data-stage="{{ $nextRepairStage->id }}">
            <i class="fa fa-comment-o fa-fw" aria-hidden="true"></i> komentarz
        </div>
    </td>
    <td>
        @if($nextRepairStage->stage->if_datepicker)
            <input type="text" name="date_value" data-injury="{{ $injury->id }}" data-stage="{{ $nextRepairStage->stage->id }}" class="form-control stage-datepicker" placeholder="wybierz datę"  >
        @endif
    </td>
</tr>
<tr class="comment-container" data-comment="{{ $nextRepairStage->id }}" style="display: none;">
    <td></td>
    <td colspan="3">
        <div class="form-group">
            {{ Form::textarea('comment['.$nextRepairStage->id.']', $nextRepairStage->comment, ['class' => 'form-control']) }}
            <span class="btn btn-info btn-sm pull-right marg-top-min update-comment" data-stage="{{ $nextRepairStage->id }}">
                <i class="fa fa-floppy-o fa-fw"></i> zapisz
            </span>
        </div>
    </td>
</tr>