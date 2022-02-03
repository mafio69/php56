@extends('layouts.main')

@section('header')
    Odpowiadanie na email zadania
@stop

@section('main')
    <form action="{{ URL::to('tasks/reply') }}" method="post"  enctype="multipart/form-data" id="dialog-form">
        {{Form::token()}}
        {{ Form::hidden('task_instance_id', $taskInstance->id) }}
        {{ Form::hidden('referer', $referer) }}
        <div class="row marg-btm">
            <div class="col-sm-12">
                <blockquote>
                {{ $taskInstance->task->subject }}
                </blockquote>
            </div>
        </div>
        <div class="row marg-btm">
            <div class="col-sm-4">
                <label>
                    Adresat:
                </label>
                {{ $taskInstance->task->from_email }} ({{$taskInstance->task->from_name}})
                {{ Form::text('emails', null, ['class' => 'form-control', 'id' => 'emails', 'placeholder' => 'Dodatkowi adresaci (rodziel przecinkiem)']) }}

            </div>
            <div class="col-sm-4">
                <label>
                    Nadawca:
                </label>
                <input type="text" name="sender" class="form-control email required"
                       value="{{ $sender }}" required>
            </div>
            <div class="col-sm-4">
                <label>Podpis</label>
                {{ Form::select('footer_id', $footers, null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="row marg-btm">
            <div class="col-sm-12">
                <label>
                    Wyślij kopię do (rodziel przecinkiem)
                </label>
                {{ Form::text('cc_emails', null, ['class' => 'form-control', 'id' => 'cc-emails', 'placeholder' => 'Wyślij kopię do (rodziel przecinkiem)']) }}
            </div>
        </div>
        <div class="row marg-btm">
            <div class="col-sm-12">
                <label>
                    Wyślij ukrytą kopię do (rodziel przecinkiem)
                </label>
                {{ Form::text('bcc_emails', null, ['class' => 'form-control', 'id' => 'bcc-emails', 'placeholder' => 'Wyślij ukrytą kopię do (rodziel przecinkiem)']) }}
            </div>
        </div>
        <div class="row marg-btm">
            <div class="col-sm-12">
                <textarea id="email-body" class="form-control" name="content" style="min-height: 300px;">
                    <br>
                    <br>
                    %%FOOTER%%
                    <br>
                    <p>W dniu {{ $taskInstance->task->task_date->format('Y-m-d') }} o godz. {{ $taskInstance->task->task_date->format('H:i') }},
                        {{ $taskInstance->task->from_name }} pisze:</p>
                    <blockquote class="reply_quote">
                        <p>{{ closeHtmlTags($taskInstance->task->content) }}</p>
                    </blockquote>
                </textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h5>Załączniki:</h5>
            </div>
            <div class="col-sm-12">
                @foreach($taskInstance->task->files as $file)
                    @if($file->original_filename != 'email.eml')
                        <span class="label label-default marg-right"> {{ $file->original_filename }} </span>
                    @endif  
                @endforeach
            </div>
            @if($taskInstance->task->injuries->count() > 0)
                <div class="col-sm-12">
                    <hr>
                    <h6>Dokumenty szkodowe</h6>
                 </div>
                @foreach($taskInstance->task->injuries as $injury)
                    @foreach($injury->documents()->where(function($query)
                        {
                            $query->where('type', '=', 2)->orWhere('type', '=', 3);
                        })->where('category', '!=', 0)->get() as $document)
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="files[]" value="{{ $document->id }}">

                                    @if($document->type == 2)
                                        {{ $document->document->name }}<br>
                                        <i>
                                            @if($document->category == 23)
                                                {{ number_format( $document->name , 2, ',', '') }} zł
                                            @else
                                                {{ $document->name }}
                                            @endif
                                        </i>
                                    @else
                                        {{ $document->document->name }}
                                        @if($document->name != '')
                                            <br>
                                            <i>
                                                {{ $document->name }}
                                            </i>
                                        @endif
                                    @endif
                                    <span class="label label-info">
                                        {{ $injury->case_nr }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                @endforeach

            @endif
            <div class="col-sm-12">
                <hr>
                <div class="form-group">
                    <input id="attachments_input" type="file" name="attachment[]" multiple>
                    <p class="help-block">Prześlij załącznik ze swojego komputer</p>
                </div>
                <div class="form-group" id="files-preview-container">

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr>
            </div>
            <div class="col-sm-12 col-md-4 col-md-offset-4 marg-btm">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa fa-send fa-fw"></i>
                    wyślij
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function(){
            $('#email-body').wysihtml5({
                toolbar: {
                    "font-styles": false,
                    "html": true,
                    'link': false,
                    "image": false,
                    "color": true,
                    "blockquote": true
                },
                parserRules:    wysihtml5ParserRules,
                useLineBreaks:  false
            });
            let objectURL;
            $('#attachments_input').on('change', function (){
                $('#files-preview-container').html('');
                if (objectURL) {
                    URL.revokeObjectURL(objectURL);
                }

                for(var i=0;i<this.files.length;i++) {
                    const file = this.files[i];
                    console.log(file);
                    objectURL = URL.createObjectURL(file);

                    var link = document.createElement('a');
                    var linkText = document.createTextNode(file.name);
                    link.appendChild(linkText);
                    link.title = file.name;
                    link.href = objectURL;
                    link.target = '_blank';
                    link.classList.add('marg-left');
                    link.classList.add('marg-right');

                    $('#files-preview-container').append(link);
                }

            });

            let terms = [];
            $('#emails').autocomplete({
                source: function( request, response ) {
                    terms = request.term.split(' ');
                    let term = terms.pop();
                    $.ajax({
                        url: "{{ url('tasks/address-book/search') }}",
                        data: {
                            term: term,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            response( $.map( data, function( item ) {
                                return item;
                            }));
                        }
                    });
                },
                minLength: 2,
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                },
                select: function(event, ui) {
                    event.preventDefault();
                    $('#emails').val( terms.join('') + ui.item.value );
                }
            });

            $('#cc-emails').autocomplete({
                source: function( request, response ) {
                    terms = request.term.split(' ');
                    let term = terms.pop();
                    $.ajax({
                        url: "{{ url('tasks/address-book/search') }}",
                        data: {
                            term: term,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            response( $.map( data, function( item ) {
                                return item;
                            }));
                        }
                    });
                },
                minLength: 2,
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                },
                select: function(event, ui) {
                    event.preventDefault();
                    $('#cc-emails').val( terms.join('') + ui.item.value );
                }
            })

            $('#bcc-emails').autocomplete({
                source: function( request, response ) {
                    terms = request.term.split(' ');
                    let term = terms.pop();
                    $.ajax({
                        url: "{{ url('tasks/address-book/search') }}",
                        data: {
                            term: term,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            response( $.map( data, function( item ) {
                                return item;
                            }));
                        }
                    });
                },
                minLength: 2,
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                },
                select: function(event, ui) {
                    event.preventDefault();
                    $('#bcc-emails').val( terms.join('') + ui.item.value );
                }
            })
        });
    </script>
@endsection