<div class="row">
    <div class="col-sm-12">
        <hr>
    </div>
    @if($readonly)
        <div class="col-sm-12 col-md-6 col-md-offset-3 text-center">
            <dl class="dl-horizontal">
                @foreach($company->commissions as $commission)
                    <dt>
                        @if($commission->brand_id == 0)
                            pozostałe marki
                        @else
                            {{ $commission->brand->name }}
                        @endif
                        :
                    </dt>
                    <dd>{{ $commission->commission }} %</dd>
                @endforeach
            </dl>
        </div>
    @else
    <div class="col-sm-12 col-md-6 col-md-offset-3 marg-btm">
        <label>Wybierz markę</label>
        <div class="input-group">
            {{ Form::select('brand', $brands, null, ['class' => 'form-control']) }}
            <span class="input-group-btn">
                <button class="btn btn-default add-brand" type="button">
                    <i class="fa fa-plus"></i>
                </button>
            </span>
        </div>
    </div>
    <div class="brands-container">
        @if(isset($company))
            @foreach($company->commissions as $commission)
                <div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4 marg-btm brand-container" data-brand="{{ $commission->brand_id }}">
                    <div class="input-group">
                        <span class="input-group-addon brand-name" id="sizing-addon2" data-brand="{{ $commission->brand_id }}">
                            @if($commission->brand_id == 0)
                                pozostałe marki
                            @else
                                {{ $commission->brand->name }}
                            @endif
                        </span>
                        <input type="hidden" class="form-control" name="brands[]" value="{{ $commission->brand_id }}">
                        <input type="text" class="form-control required number" name="commission[]" placeholder="wartość prowizji dla marki" aria-describedby="sizing-addon2" value="{{ $commission->commission }}">
                        <span class="input-group-addon" id="sizing-addon2">%</span>
                        <span class="input-group-btn">
                             <button class="btn btn-danger del-brand" data-brand="{{ $commission->brand_id }}" type="button">
                                 <i class="fa fa-minus"></i>
                             </button>
                        </span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    @endif
</div>
@if(isset($company))
    @section('headerJs')
        @parent
@endif
<script>
    $('.add-brand').on('click', function(){
        var brand_id = $('select[name="brand"] option:selected').val();
        var brand_name = $('select[name="brand"] option:selected').text();

        if(brand_id !== undefined) {
            $('.brands-container').append('<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4 marg-btm brand-container" data-brand="' + brand_id + '">' +
                '<div class="input-group">\n' +
                '  <span class="input-group-addon brand-name" id="sizing-addon2" data-brand="' + brand_id + '">' + brand_name + '</span>\n' +
                '  <input type="hidden" class="form-control" name="brands[]" value="' + brand_id + '">\n' +
                '  <input type="text" class="form-control required number" name="commission[]" placeholder="wartość prowizji dla marki" aria-describedby="sizing-addon2">\n' +
                '  <span class="input-group-addon" id="sizing-addon2">%</span>\n' +
                '  <span class="input-group-btn">\n' +
                '   <button class="btn btn-danger del-brand" data-brand="' + brand_id + '" type="button">\n' +
                '       <i class="fa fa-minus"></i>\n' +
                '   </button>\n' +
                '  </span>' +
                '</div>' +
                '</div>');

            $('select[name="brand"] option:selected').remove();
        }
    });

    $('.brands-container').on('click', '.del-brand', function () {
        var brand_id = $(this).data('brand');
        console.log(brand_id);

        $('select[name="brand"]').append($('<option>', {value:brand_id, text: $('.brand-name[data-brand="' + brand_id + '"').text() }) );
        $('.brand-container[data-brand="' + brand_id + '"]').remove();
    });
</script>
@if(isset($company))
    @stop
@endif