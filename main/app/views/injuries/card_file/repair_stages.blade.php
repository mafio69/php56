<div class="tab-pane fade in" id="repair_stages">
    <div class="row col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
        <table class="table table-condensed table-hover" id="repair-stages-table">
            @foreach($repairStages as $repairStage)
                <tr data-stage="{{ $repairStage->id }}"
                @if( isset($injuryRepairStages[$repairStage->id]) && $injury->current_injury_repair_stage_id == $injuryRepairStages[$repairStage->id]->id)
                    class="success"
                @endif
                >
                    <td width="250px">
                        <strong>{{ $repairStage->name }}</strong><br/>
                        <small class="repair-description">
                            @if( isset($injuryRepairStages[$repairStage->id]) && $injuryRepairStages[$repairStage->id]->value == 1)
                                {{ $repairStage->checked_description }}
                            @else
                                {{ $repairStage->unchecked_description }}
                            @endif
                        </small>
                    </td>
                    <td>
                        <input type="checkbox" name="stage[{{ $repairStage->id }}]" data-injury="{{ $injury->id }}" data-stage="{{ $repairStage->id }}" class="repair-stage" data-size="small" data-off-color="danger" data-on-text="<i class='fa fa-check' aria-hidden='true'></i>" data-off-text="<i class='fa fa-minus' aria-hidden='true'></i>"
                        @if( isset($injuryRepairStages[$repairStage->id]) && $injuryRepairStages[$repairStage->id]->value == 1 )
                            checked
                        @endif
                        >
                    </td>
                    <td>
                        <div class="btn btn-sm @if( isset($injuryRepairStages[$repairStage->id]) && $injuryRepairStages[$repairStage->id]->comment) btn-primary @else btn-default @endif show-comment" data-stage="{{ $repairStage->id }}">
                            <i class="fa fa-comment-o fa-fw" aria-hidden="true"></i> komentarz
                        </div>

                    </td>
                    <td>
                        @if($repairStage->if_datepicker)
                            <input type="text" name="date_value" data-injury="{{ $injury->id }}" data-stage="{{ $repairStage->id }}" class="form-control stage-datepicker" placeholder="wybierz datę" value="{{ (isset($injuryRepairStages[$repairStage->id]) && $injuryRepairStages[$repairStage->id]->date_value) ? $injuryRepairStages[$repairStage->id]->date_value->format('Y-m-d') : null }}" >
                        @endif
                    </td>
                </tr>
                <tr class="comment-container" data-comment="{{ $repairStage->id }}" style="display: none;">
                    <td></td>
                    <td colspan="3">
                        <div class="form-group">
                            {{ Form::textarea('comment['.$repairStage->id.']', (isset($injuryRepairStages[$repairStage->id]) ) ? $injuryRepairStages[$repairStage->id]->comment : '', ['class' => 'form-control']) }}
                            <span class="btn btn-info btn-sm pull-right marg-top-min update-comment" data-injury="{{ $injury->id }}" data-stage="{{ $repairStage->id }}">
                                <i class="fa fa-floppy-o fa-fw"></i> zapisz
                            </span>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>

    </div>

</div>

@section('headerJs')
    @parent
    <script>
        $(".repair-stage").bootstrapSwitch().on('switchChange.bootstrapSwitch', function(event, state) {
            var stage_id = $(event.target).data('stage');
            var injury_id = $(event.target).data('injury');
            $.ajax({
                data: { stage_id: stage_id, injury_id: injury_id, _token: $('input[name="_token"]').val() },
                type: 'POST',
                assync: false,
                cache: false,
                dataType: 'json',
                url: '/injuries/manage/toggle-repair-stage',
                success: function (data) {
                    $('#repair-stages-table tr').removeClass('success');
                    $('#repair-stages-table tr[data-stage="'+data.current+'"]').addClass('success');
                    if(state) {
                        $('#repair-stages-table tr[data-stage="'+data.current+'"] small.repair-description').html(data.checked_description);
                    }else{
                        $('#repair-stages-table tr[data-stage="'+data.current+'"] small.repair-description').html(data.unchecked_description);
                    }
                    $('.currentRepairStage').html(data.current_checked_description);


                    $.notify({
                        message: 'Zmiany zapisano pomyślnie.'
                    },{
                        type: 'info',
                        delay: 1500,
                        placement: {
                            from: 'bottom',
                            align: 'right'
                        }
                    });
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $.notify({
                        message: 'Wystąpił błąd w trakcie aktualizacji.'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'bottom',
                            align: 'right'
                        }
                    });
                },
                dataType: 'json'
            });
        });

        $('.stage-datepicker').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,dateFormat: "yy-mm-dd",
            onClose: function( selectedDate, instance ) {
                var stage_id = instance.input.data('stage');
                var injury_id = instance.input.data('injury');

                $.ajax({
                    data: { stage_id: stage_id, injury_id: injury_id, date_value: selectedDate, _token: $('input[name="_token"]').val() },
                    type: 'POST',
                    assync: false,
                    cache: false,
                    dataType: 'json',
                    url: '/injuries/manage/update-repair-stage-date',
                    success: function (data) {
                        $.notify({
                            message: 'Zmiany zapisano pomyślnie.'
                        },{
                            type: 'info',
                            delay: 1500,
                            placement: {
                                from: 'bottom',
                                align: 'right'
                            }
                        });
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $.notify({
                            message: 'Wystąpił błąd w trakcie aktualizacji.'
                        },{
                            type: 'danger',
                            placement: {
                                from: 'bottom',
                                align: 'right'
                            }
                        });
                    },
                    dataType: 'json'
                });
            }
        });

        $('#repair-stages-table').on('click', '.show-comment', function(){
             var stage = $(this).data('stage');

             $('.comment-container[data-comment="'+stage+'"').toggle();
        });

        $('#repair-stages-table').on('click', '.update-comment', function(){
            var stage_id = $(this).data('stage');
            var injury_id = $(this).data('injury');
            var comment = $('textarea[name="comment['+stage_id+']"]').val();

            $.ajax({
                data: { stage_id: stage_id, injury_id: injury_id, comment: comment, _token: $('input[name="_token"]').val() },
                type: 'POST',
                assync: false,
                cache: false,
                dataType: 'json',
                url: '/injuries/manage/update-repair-stage-comment',
                success: function (data) {
                    $.notify({
                        message: 'Zmiany zapisano pomyślnie.'
                    },{
                        type: 'info',
                        delay: 1500,
                        placement: {
                            from: 'bottom',
                            align: 'right'
                        }
                    });

                    $('.show-comment[data-stage="'+stage_id+'"]').removeClass('btn-default').addClass('btn-primary');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $.notify({
                        message: 'Wystąpił błąd w trakcie aktualizacji.'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'bottom',
                            align: 'right'
                        }
                    });
                },
                dataType: 'json'
            });
        });
    </script>
@endsection