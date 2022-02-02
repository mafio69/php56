<div class="panel panel-default">
    <div class="panel-heading">
        Dostępne marki

        <span class="btn btn-xs btn-primary pull-right submit">
            <i class="fa fa-floppy-o fa-fw"></i>
            zapisz
        </span>
    </div>
    <div class="panel-body">
        @foreach($brands->chunk(3) as $chunk)
            <div class="row">
                @foreach($chunk as $brand)
                    <div class="col-sm-6 col-md-4 brand-group-selector" data-brand="{{ $brand->id }}">
                        <div class="form-group">
                            <div class="input-group ">
                                <p class="form-control-static">{{ $brand->brand->name }}
                                    @if($brand->authorization)
                                        <span class="label label-info pull-right">autoryzowany</span>
                                    @endif
                                </p>
                                <span class="input-group-btn add-brand" data-brand="{{ $brand->id }}">
                                    <button class="btn btn-default" type="button">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
<div id="brands-container">
    <div class="panel panel-default">
        <table class="table table-hover table-condensed">
            <thead>
            <th>marka</th>
            <th>autoryzacja</th>
            <th>sprzedał</th>
            <th></th>
            </thead>
        </table>
    </div>
</div>