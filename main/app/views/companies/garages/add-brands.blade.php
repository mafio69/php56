<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawnie obsługiwanych marek</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="" method="post" >
        {{Form::token()}}
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <div class="form-group">
                    <label>Wyszukiwanie marki:</label>
                    {{ Form::text('token', '', array('class' => 'form-control', 'placeholder' => 'nazwa marki'))  }}
                </div>
            </div>
            <div class="search-results col-sm-12 col-lg-8 col-lg-offset-2">
                <table class="table table-condensed table-hover">
                    <thead>
                        <th>
                            <label>
                                <input type="checkbox" id="check-all"/>
                                marka
                            </label>
                        </th>
                    </thead>
                </table>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="add-brands" data-element="{{ $type == 1 ? 'collapseBrandsO' : 'collapseBrandsC' }}">Dodaj</button>
</div>

<script>
    $('input[name="token"]').on('keyup', function(){
         brands = $.map($('input.brand_id'), function(c){return c.value; });
        $.ajax({
            url: "/company/garages/search-brand",
            data: {
                term: $(this).val(),
                typ: "{{ $type }}",
                brands: brands
            },
            beforeSend: function(){
                $('.search-results').html('<p class="text-center"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></p>');
            },
            dataType: "json",
            type: "GET",
            success: function (data) {
                $('.search-results').html('<table class="table table-condensed table-hover"><thead><th><label><input type="checkbox" id="check-all"/> marka</label></th></thead></table>');
                $.each(data, function (i, item) {
                    $('.search-results table').append('<tr><td><label><input type="checkbox" class="check" name="check-brands[]" value="'+item.id+'" data-name="'+item.name+'"/> '+item.name+'</label></td></tr>');
                });
            }
        });
    }).keyup();

    $('.search-results').on('change', '#check-all', function(){
        if($(this).is(':checked'))
        {
            $('.check').prop('checked', true);
        }else{
            $('.check').prop('checked', false);
        }
    });
    $('.search-results').on('change', '.check', function(){
        if( ! $(this).is(':checked'))
        {
            $('#check-all').prop('checked', false);
        }
    });

    $('#add-brands').on('click', function () {
        element = $(this).data('element');
        $('input.check:checked').each(function(){
            var id = $(this).val();
            var name = $(this).data('name');

            $('#'+element+' .brands-list table').append('<tr><td>'+name+'</td><td><label><input type="radio" name="authorization['+id+']" value="1"> tak</label><label class="marg-left"><input type="radio" name="authorization['+id+']" value="0"> nie</label><input type="hidden" class="brand_id" name="brand_id['+id+']" value="'+id+'"></td><td><span class="btn btn-xs btn-danger remove-brand-row"><i class="fa fa-trash"></i></span></td></tr>');
        });

        counter = parseInt($('div[data-target="#'+element+'"] .badge').text());
        counter +=  $('input.check:checked').length;
        $('div[data-target="#'+element+'"] .badge').text( counter );

        $('#modal').modal('hide');
    });
</script>