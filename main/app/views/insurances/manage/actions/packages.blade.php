<div class="row">
    <div class="col-sm-4 control-label text-right">
        <label>Pakiety</label>
    </div>
    <div class="col-sm-8">
        <div class="row">
            @foreach($group_rate->packages as $package)
                @if($package->$percentage_col || $package->$amount_col)
                    <div class="col-sm-12 col-md-6">
                        @if($package->$percentage_col)
                            <div class="checkbox">
                                <label>
                                    <input class="package_percentage package" name="package_percentage[]" data-percentage="{{ $package->$percentage_col }}" value="{{ $package->id }}" type="checkbox" @if($insurance && $insurance->packages->contains($package->id)) checked @endif> {{ $package->name }} - {{ $package->$percentage_col }} %
                                </label>
                            </div>
                        @else
                            <div class="checkbox">
                                <label>
                                    <input class="package_amount package" name="package_amount[]" value="{{ $package->id }}" data-amount="{{ $package->$amount_col }}" type="checkbox" @if($insurance && $insurance->packages->contains($package->id)) checked @endif> {{ $package->name }} - {{ $package->$amount_col }} zł
                                </label>
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
<h4 class="inline-header"></h4>

