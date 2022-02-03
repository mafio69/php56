@extends('layouts.main')


@section('main')
    <div class="row marg-btm">
        <div class="col-lg-8 col-lg-offset-2 ">
            <div class="panel panel-primary ">

                <div class="panel-body">
                    {{ Form::open(array('url' => url('debug-send-file'), 'class' => 'page-form', 'enctype' => 'multipart/form-data', 'method' => 'post' )) }}
                                <input type="file" name="file">
                                <button type="submit">send</button>
                        <span class="btn btn-primary" id="send">wyślij</span>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop

@section('headerJs')
    @parent
    <script>
        function uploadFile (filename) {
            $.ajax({
                type: "POST",
                url: "{{url('debug-get-file')}}",
                assync:false,
                cache:false,
                complete: function( data ) {
                    // define data and connections
                    var blob = new Blob([data]);
                    var url = URL.createObjectURL(blob);
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{url('debug-send-file')}}', true);

                    // define new form
                    var formData = new FormData();
                    formData.append('file', blob, filename);

                    // action after uploading happens
                    xhr.onload = function(e) {
                        console.log("File uploading completed!");
                    };

                    // do the uploading
                    console.log("File uploading started!");
                    xhr.send(formData);
                }
            });


        }

        $(document).on('ready', function (){
            $('#send').on('click', function(){
                uploadFile('3200449319750c0b050fe1542ff374864a8f6e74.eml');
            })
        })
    </script>
@endsection