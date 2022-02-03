@extends('layouts.main')

@section('header')
    Generowanie raportów zadań
@stop

@section('main')
    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport obsługi</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => url('tasks/reports/users'), 'class' => 'page-form', 'id' => 'users-report' )) }}
                    <p>Wskaż daty przydziału zadania:</p>
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'users_report_date_from', 'datepicker_id_to' => 'users_report_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('form').each(function(e){
                $(this).validate().cancelSubmit = true;
            });

            $(".page-form").submit(function(e) {
                var self = this;

                e.preventDefault();

                if($(this).valid()){
                    self.submit();
                }else{
                    $('.form_submit').button('reset');
                }

                return false; //is superfluous, but I put it here as a fallback
            });

            $('.monthdate').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm",
                onClose: function(dateText, inst) {
                    $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                }
            });
        });
    </script>
@stop
