<div class="row">
    <div class="col-sm-12 marg-btm">
        <label>Czy powinna zostać naliczona opłata za wystawienie dokumentu:</label>
        <select name="if_fee_collection" class="form-control">
            @if($if_doc_fee_enabled)
                <option value="1"{{$if_fee_collection?' selected':''}}>tak</option>
            @endif
            <option value="0"{{!$if_fee_collection?' selected':''}} {{!$if_doc_fee_enabled?' selected
            disabled':''}}>nie</option>
        </select>
    </div>
</div>