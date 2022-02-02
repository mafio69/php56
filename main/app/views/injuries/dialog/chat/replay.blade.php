 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Tworzenie odpowiedzi</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('chat.postReplay', array($id)) }}" method="post"  id="dialog-injury-form"> 
  		
      {{Form::token()}}
      <div class="form-group">
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Wiadomość:</label>
            {{ Form::textarea('content', '', array('class' => 'form-control required', 'id'=>'content',  'placeholder' => 'treść wiadomości')) }} 
          </div>
        </div>
        <h4 class="inline-header"><span>Adresaci wiadomości:</span></h4>
        <div class="row">
          <div class="col-sm-12 marg-btm">
            
            <input type="hidden" name="check_dos" value="1">
               
            @if($status[2] == 1)
              @if(get_chat_group() != 2)
              <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-success-grey btn-check
                  @if(get_chat_group() == 3 )
                    active" 
                    disabled="disabled"
                  @else
                    "
                  @endif
                ">
                  <input type="checkbox" name="check_info" value="1"><i class="fa fa-user fa-fw"></i> Infolinia
                </label>
              </div>
              @endif
            @endif

            @if($status[1] == 1)
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-info-grey btn-check
                @if(get_chat_group() == 2)
                  active" 
                  disabled="disabled"
                @else
                  "
                @endif

              ">
                <input type="checkbox" name="check_branch" value="1"><i class="fa fa-user fa-fw"></i> Warsztat
              </label>
            </div>
            @endif

          </div>
        </div>
      </div> 

  	</form>

  </div>
  <div class="modal-footer" style="margin-top:0px;">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Dodaj</button>
  </div>
 <script type="text/javascript">
     $(document).ready(function(){
         $('#content').wysihtml5({
             toolbar: {
                 "font-styles": false,
                 "html": true,
                 'link': false,
                 "image": false,
                 "color": true,
                 "blockquote": false
             },
             parserRules:    wysihtml5ParserRules,
             useLineBreaks:  false
         });
     });
 </script>
