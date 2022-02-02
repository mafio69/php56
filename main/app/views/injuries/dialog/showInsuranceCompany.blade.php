<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dane ubezpieczyciela</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
<div class="panel-body">
        <fieldset>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 ">
                        <label>Nazwa:</label>
                	   <p class="form-control">{{ $insurance_company->name }}</p>                                
                    </div>
                </div>
                <h4 class="inline-header"><span>Adres:</span></h4>
                <div class="row">
                    <div class="col-md-6 ">
                        <label>Kod pocztowy:</label>
                        <p class="form-control">{{ $insurance_company->post }}</p>                                
                    </div>
                    <div class="col-md-6 ">
                        <label>Miasto:</label>
                        <p class="form-control">{{ $insurance_company->city }}</p>  
                    </div>                              
                </div>
                <div class="row">
                    <div class="col-md-6 ">
                        <label>Ulica:</label>
                        <p class="form-control">{{ $insurance_company->street }}</p>                                
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 ">
                        <label>Telefon:</label>
                        <p class="form-control">{{ $insurance_company->phone }}</p>   
                    </div> 
                    <div class="col-md-6 ">
                        <label>Email:</label>
                        <p class="form-control">{{ $insurance_company->email }}</p>  
                    </div>                              
                </div>
            </div>
        </fieldset>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>