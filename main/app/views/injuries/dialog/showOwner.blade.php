
<div class="panel-body">
    
        
        <fieldset>
            <div class="form-group">
                <div class="row">
                    <label>Nazwa:</label>
                	<p class="form-control">{{ $owner->name }}</p>                                
                </div>
                @if($owner->old_name)
                    <div class="row">
                        <label>Dawna nazwa:</label>
                        <p class="form-control">{{ $owner->old_name }}</p>
                    </div>
                @endif
                <h4 class="inline-header"><span>Adres:</span></h4>
                <div class="row">
                    <label>Kod pocztowy:</label>
                    <p class="form-control">{{ $owner->post }}</p>                                
                </div>
                <div class="row">
                    <label>Miasto:</label>
                    <p class="form-control">{{ $owner->city }}</p>                                
                </div>
                <div class="row">
                    <label>Ulica:</label>
                    <p class="form-control">{{ $owner->street }}</p>                                
                </div>
            </div>
        </fieldset>
    
</div>
