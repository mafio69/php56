<?php
$files = json_decode(stripslashes($_POST['files']));
?>
 <div class="modal-header">
    <h4 class="modal-title" id="myModalLabel">Wybór kategorii dokumentów</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('dos.other.injuries.setDocumentSet') }}" method="post"  id="dialog-set-doc-form">
  		
        {{Form::token()}}
        @foreach($files as $file)
          <input type="hidden" name="files[]" value="{{$file}}">
        @endforeach
        <div class="form-group">
        	<div class="row">
          		<div class="col-sm-12 marg-btm">
        			<h4>Wybierz typ dokumentu:</h4>
              <div class="row">
              @foreach(Config::get('definition.fileCategory') as $k =>$v)
                <div class="col-md-4 ">
                  <div class="radio">
                    <label>
                      <input type="radio" class="required" name="fileType" value="{{ $k }}">
                      {{ $v }}
                    </label>
                  </div>
                </div> 
              @endforeach
              </div>
        		</div>
        	</div>
        	<div class="row">
        		<div class="col-sm-12 ">
              <label >Opis dokumentów:</label>
              {{ Form::text('content', '', array('class' => 'form-control ', 'id'=>'content',  'placeholder' => 'opis dokumentów')) }} 
            </div>
        	</div>
        </div>
        
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" id="cancel_docs">Anuluj</button> 
    <button type="button" class="btn btn-primary" id="set_docs">Wprowadź dokumenty</button>    
  </div>