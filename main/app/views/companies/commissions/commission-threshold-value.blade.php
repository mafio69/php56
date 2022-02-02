<div class="row">
    <div class="col-sm-12">
        <hr>
    </div>
    @if($readonly)
        <div class="col-sm-12 col-md-6 col-md-offset-3 text-center">
            <dl class="dl-horizontal">
                @foreach($company->commissions as $commission)
                    <dt>Od {{ $commission->min_value  }} PLN: </dt>
                    <dd>{{ $commission->commission }} %</dd>
                @endforeach
            </dl>
        </div>
    @else
    <div class="col-sm-12 col-md-6 col-md-offset-3 text-center marg-btm">
        <div class="btn btn-success add-threshold">
            <i class="fa fa-plus fa-fw"></i> dodaj próg
        </div>
    </div>
    <div class="thresholds-container">
        @if(isset($company))
            @foreach($company->commissions as $commission)
                <div class="threshold-container marg-btm" style="display: inline-block;">
                    <div class="col-sm-12 col-md-3 col-md-offset-3">
                        <div class="input-group">
                            <span class="input-group-addon">Od</span>
                            @if($commission->min_value == 0)
                                <input type="text" class="form-control required number" readonly disabled placeholder="próg" aria-describedby="sizing-addon2" value="0">
                                <input type="hidden" name="value[]" value="0">
                            @else
                                <input type="text" class="form-control required number" name="value[]" placeholder="próg" aria-describedby="sizing-addon2" value="{{ $commission->min_value }}">
                            @endif
                            <span class="input-group-addon">PLN</span>
                            </div>
                        </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control required number" name="commission[]" placeholder="wartość prowizji dla progu" aria-describedby="sizing-addon2" value="{{ $commission->commission }}">
                            <span class="input-group-addon" id="sizing-addon2">%</span>
                            @if($commission->min_value > 0)
                                <span class="input-group-btn">
                                   <button class="btn btn-danger del-threshold" type="button">
                                       <i class="fa fa-minus"></i>
                                   </button>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="threshold-container marg-btm" style="display: inline-block;">
                <div class="col-sm-12 col-md-3 col-md-offset-3">
                    <div class="input-group">
                        <span class="input-group-addon">Od</span>
                        <input type="text" class="form-control required number" readonly disabled placeholder="próg" aria-describedby="sizing-addon2" value="0">
                        <input type="hidden" name="value[]" value="0">
                        <span class="input-group-addon" >PLN</span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control required number" name="commission[]" placeholder="wartość prowizji dla progu" aria-describedby="sizing-addon2" value="">
                        <span class="input-group-addon" id="sizing-addon2">%</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @endif
</div>
@if(isset($company))
    @section('headerJs')
        @parent
@endif
<script>
    $('.add-threshold').on('click', function(){
            $('.thresholds-container').append('<div class="threshold-container marg-btm" style="display: inline-block;">' +
                '<div class="col-sm-12 col-md-3 col-md-offset-3">' +
                    '<div class="input-group">\n' +
                    '  <span class="input-group-addon">Od</span>\n' +
                    '  <input type="text" class="form-control required number" name="value[]" placeholder="próg" aria-describedby="sizing-addon2">\n' +
                    '  <span class="input-group-addon" id="sizing-addon2">PLN</span>\n' +
                    '</div>' +
                '</div>' +
                '<div class="col-sm-12 col-md-3">' +
                    '<div class="input-group">\n' +
                    '  <input type="text" class="form-control required number" name="commission[]" placeholder="wartość prowizji dla progu" aria-describedby="sizing-addon2">\n' +
                    '  <span class="input-group-addon" id="sizing-addon2">%</span>\n' +
                    '  <span class="input-group-btn">\n' +
                    '   <button class="btn btn-danger del-threshold" type="button">\n' +
                    '       <i class="fa fa-minus"></i>\n' +
                    '   </button>\n' +
                    '  </span>' +
                    '</div>' +
                '</div>' +
            '</div>');
    });

    $('.thresholds-container').on('click', '.del-threshold', function () {
        $(this).closest('.threshold-container').remove();
    });
</script>
@if(isset($company))
    @stop
@endif
