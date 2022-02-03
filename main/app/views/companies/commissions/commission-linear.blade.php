<div class="row">
    <div class="col-sm-12">
        <hr>
    </div>
    <div class="col-sm-12 col-md-6 col-md-offset-3 text-center marg-btm">
        @if($readonly)
            <label>Wartość prowizji:</label>
            <p class="form-control-static">
                @if( isset($company) && $company->commissions->first()) {{ $company->commissions->first()->commission }} % @endif
            </p>
        @else
        <label>Wartość prowizji</label>
        <div class="input-group">
                <input type="text" class="form-control required number" name="commission" placeholder="wartość prowizji" aria-describedby="sizing-addon2"
                       @if( isset($company) && $company->commissions->first()) value="{{ $company->commissions->first()->commission }}" @endif
                >
                <span class="input-group-addon" id="sizing-addon2">%</span>
        </div>
        @endif

    </div>
</div>
