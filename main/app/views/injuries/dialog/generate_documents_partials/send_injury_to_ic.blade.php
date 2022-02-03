  <div class="row">
    <div class="col-sm-6">
      <div class="col-sm-12 col-lg-12 marg-btm">
            @include('injuries.dialog.generate_documents_partials.branch_options')
           <label>Adresaci wiadomości:</label>
           <ul class="list-group">
                    @if($insuranceCompany && isset($insuranceCompany_emails[0]) && count($insuranceCompany_emails[0]) > 0)
                        @foreach($insuranceCompany_emails[0] as $insuranceCompany_email)
                            <li class="list-group-item">
                                <div class="checkbox">
                                    <label>
                                        <input name="insuranceCompanies[]" value="{{ $insuranceCompany_email }}" type="checkbox">
                                        Ubezpieczyciel - {{ $insuranceCompany->name }} - {{ $insuranceCompany_email }}
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    @endif
                    @if(Auth::user()->email && Auth::user()->email != '')
                        <li class="list-group-item">
                            <div class="checkbox">
                                <label>
                                    <input name="special_emails[]"  value="{{Auth::user()->email}}" type="checkbox">
                                    {{Auth::user()->name }} {{Auth::user()->email}}
                                </label>
                            </div>
                        </li>
                    @endif
            </ul>
            <div class="form-group">
                <label>Dodatkowi adresaci (wprowadź adresy email oddzielone przecinkiem):</label>
                <textarea name="custom_emails" class="form-control" placeholder="wprowadź adresy email oddzielone przecinkiem"></textarea>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12">
            <div class="form-group">
                <label>Komentarz:</label>
                <textarea name="email_comment" class="form-control" placeholder="komentarz" style="height: 200px;"></textarea>
            </div>
        </div>
      </div>
      <div class="col-sm-6">
        <?php  $owner = $injury->vehicle->owner()->first(); ?>
          @include('injuries.docs_templates.send_injury_to_ic_content')
      </div>
  </div>
