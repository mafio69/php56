<div class="tab-pane fade in " id="sms">
    @if(Auth::user()->can('zlecenia#zarzadzaj'))
              <div class="row" style="margin-top: 50px;">
                  <div class="col-xs-12 col-sm-10 col-md-8  col-xs-offset-0 col-sm-offset-1 col-md-offset-2 ">
                      <div class="panel panel-primary">
                          <div class="panel-heading">
                              <h3 class="panel-title">Tworzenie wiadomości SMS</h3>
                          </div>
                          <div class="panel-body">
                              <form action="{{ URL::to('dos/injuries/send-sms', [$injury->id]) }}" method="post" id="form"   role="form">
                                  <div class="row">
                                      <div class="col-md-10 col-md-offset-1">
                                          <div class="input-group input-group-sm form-sms marg-btm">
                                              <span class="input-group-addon ">Numer telefonu odbiorcy:</span>
                                              @if( count($phonesSMS) > 0)
                                              <select class="form-control" name="phone_number" >
                                                  @foreach($phonesSMS as $k => $phone)
                                                  <option value="{{ $phone['value'] }}"
                                                        @if($k == 0)
                                                            selected
                                                        @endif
                                                      >{{ $phone['name'] }}</option>
                                                  @endforeach
                                              </select>
                                              @else
                                              <p class="text-danger" style="margin-left: 30px;"><i>nie uzupełniono numeru telefonu kierowcy i zgłaszającego</i></p>
                                              @endif
                                          </div>

                                          <div class="input-group input-group-sm form-sms marg-btm">
                                              <span class="input-group-addon ">Szablon wiadomości:</span>
                                              <select class="form-control" id="template" >
                                                  <option value="">---</option>
                                                  @foreach($templates as $template)
                                                  <option value="{{ $template->body }}">{{ $template->name }}</option>
                                                  @endforeach
                                              </select>
                                          </div>

                                          <div class="form-group marg-btm">
                                              <textarea class="form-control required" placeholder="treść wiadomości" required autofocuse="" name="bodySMS" id="bodySMS" cols="50" rows="10"></textarea>
                                          </div>

                                          <div class="form-group marg-btm">
                                              <label>Podpis: </label> <span id="podpisSMS">Pozdrawiam {{ Auth::user()->name }}</span>
                                          </div>
                                          <div class="form-group marg-btm">
                                              <label>Ilość znaków: </label> <span id="iloscZnakow">0</span><br>
                                              <label>Liczba wiadomości: </label> <span id="iloscWiadomosci">0</span>
                                              @if( count($phonesSMS) > 0)
                                                {{ Form::submit(' wyślij wiadomość ',  array('class' => 'btn btn-primary pull-right', 'id' => 'submit'))  }}
                                              @endif
                                          </div>
                                      </div>
                                  </div>
                                  {{Form::token()}}
                               </form>
                          </div>
                      </div>
                  </div>
              </div>
        @endif
</div>    
