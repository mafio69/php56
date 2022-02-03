<tr class="brand-row">
    <td>{{ $branchBrand->brand->name }}</td>
    <td>{{ $branchBrand->if_multibrand ? 'tak' : 'nie' }}</td>
    <td>{{ $branchBrand->authorization ? 'tak' : 'nie' }}</td>
    <td>
        <label>
            <input type="checkbox" name="sold_yes[{{ $id }}]" value="1" > tak
        </label>
        <label class="marg-left">
            <input type="checkbox" name="sold_no[{{ $id }}]" value="1"  {{$branchBrand->if_multibrand?'checked':''}}> nie
        </label>
        <input type="hidden" name="branch_brand_id[{{ $id }}]" value="{{ $branchBrand->id }}">
    </td>
    <td>
        <span class="btn btn-xs btn-danger remove-branch-brand-row" data-brand="{{ $branchBrand->id }}" data-if-multibrand="{{$branchBrand->if_multibrand}}">
            <i class="fa fa-trash"></i>
        </span>
    </td>
</tr>