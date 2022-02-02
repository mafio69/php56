<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana statusu składka opłacona</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/info-insurances/mark-contribution-as-paid-insurance', [$insurance->id]) }}"
          method="post" id="dialog-form">
        <fieldset>
            <label>Potwierdź zmianę statusu składka opłacona.</label>
        </fieldset>
        {{Form::token()}}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary " data-loading-text="trwa zmiana statusu..." id="set">Potwierdź
    </button>
</div>