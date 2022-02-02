<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Wysyłanie dokumentów</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('dos.other.injuries.set', array('sendDocs',$injury->id)) }}" method="post"  id="dialog-form-mail">
        {{Form::token()}}
        <div class="row">
            <div class="col-sm-12 col-lg-6">
                <label>Adresaci wiadomości:</label>
                <ul class="list-group">

                    @if($client && isset($client_emails[0]) && count($client_emails[0]) > 0)
                        @foreach($client_emails[0] as $client_email)
                            <li class="list-group-item">
                                <div class="checkbox">
                                    <label>
                                        <input name="clients[]" value="{{ $client_email }}" type="checkbox">
                                        Klient - {{ $client->name }} - {{ $client_email }}
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    @endif
                    @if($notifier)
                    <li class="list-group-item @if(!$notifier || $notifier == '') list-group-item-danger @endif">
                        <div class="checkbox">
                            <label>
                                <input name="addressees['notifier']" value="{{ $notifier }}" type="checkbox" @if(!$notifier || $notifier == '') disabled @endif> Zgłaszający - {{ $injury->notifier_name }} {{ $injury->notifier_surname }} - {{ $notifier }}
                            </label>
                        </div>
                    </li>
                    @endif
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
            <div class="col-sm-12 col-lg-6">
                <div class="form-group">
                    <label>Komentarz:</label>
                    <textarea name="email_comment" class="form-control" placeholder="komentarz" style="height: 200px;"></textarea>
                </div>
            </div>
            @foreach($docsToSend as $doc)
                {{ Form::hidden('doc_ids[]', $doc->id) }}
            @endforeach
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel-btn">Anuluj</button>
    <button type="button" class="btn btn-primary" id="send-documents-mail">Wyślij</button>
</div>

<script>
    stop_temp = false;
    $('#modal-lg').on('click', '#send-documents-mail', function(){
      if(stop_temp==false){
        stop_temp = true;
        var $btn = $(this).button('loading');
        if($('#dialog-form-mail').valid()) {
            $.ajax({
                type: "POST",
                url: $('#dialog-form-mail').prop('action'),
                data: $('#dialog-form-mail').serialize(),
                assync: false,
                cache: false,
                success: function (data) {
                    if (data.code == '0') location.reload();
                    else if (data.code == '1') self.location = data.url;
                    else {
                        stop_temp = false;
                        $('#modal-lg .modal-body').html(data.error);
                        $('#modal-lg #send-documents-mail').remove();
                        $('#modal-lg #cancel-btn').html('Zamknij');
                    }
                },
                dataType: 'json'
            });
        }else {
            stop_temp = false;
            $btn.button('reset');
        }
      }
        return false;
    });


</script>
