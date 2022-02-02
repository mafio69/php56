<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Generowanie raportu <i>{{$report->desc}}</i></h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    {{ Form::open(array('url' => URL::route('reports.custom.post', array($report->name)), 'class' => 'page-form', 'id' => 'gen-report-form' )) }}
        <div class="form-group">
            @if($report->datepicker == 1)
                @include('reports.partials.datepicker')
            @endif
            <div class="row marg-btm" >
                <div class="col-sm-12 ">
                    Potwierdź wygenerowanie raportu.
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="generate-document">Wygeneruj</button>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd",
			onClose: function( selectedDate ) {
				if($(this).attr('date-opt') == 'from'){
					$( $(this).parent().parent().find('input.to') ).datepicker( "option", "minDate", selectedDate );
				}else
					$( $(this).parent().parent().find('input.from') ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	});
</script>