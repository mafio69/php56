<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie kategorii</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('dos.other.injuries.dialog.post.category') }}" method="post"  id="category-form">

            <fieldset>
                <div class="form-group">
                    <label>Nazwa:</label>
                    {{ Form::text('name', '', array('class' => 'form-control required uppercase', 'placeholder' => 'nazwa'))  }}
                </div>
                {{Form::token()}}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="add_category">Dodaj</button>
</div>