<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Potwierdź wycofanie raportu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ url('commissions/rollback-report', [$report->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <h3>Potwierdź wycofanie raportu.</h3>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wykonywanie..." id="set">Potwierdź</button>
</div>

