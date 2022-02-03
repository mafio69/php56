
@extends('layouts.main')

@section('header')
    Edycja stopki użytkownika {{ $footer->user->name }}
@stop

@section('main')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <form action="{{ URL::to('settings/users/footer-update', [$footer->id]) }}" method="post" id="form">
    {{Form::token()}}
        <div class="row marg-btm">
            <div class="col-sm-12 col-lg-8 col-lg-offset-2">
                <div class="form-group">
                    <label for="">Nazwa wewnętrzna:</label>
                    {{ Form::text('name', $footer->name, ['class' => 'form-control required', 'required']) }}
                </div>
                <textarea id="footer-body" class="form-control" name="content" style="min-height: 300px;">
                    {{ $footer->footer }}
                </textarea>
            </div>
            <div class="col-sm-12">
                <hr>
            </div>
            <div class="col-sm-12 col-lg-8 col-lg-offset-2 text-center">
                <button type="submit" class="btn btn-primary">Zapisz </button>
            </div>
        </div>
    </form>
@endsection

@section('headerJs')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#footer-body').summernote({
                height: 300,
                callbacks: {
                    onImageUpload: function(image) {
                        uploadImage(image[0]);
                    }
                }
            });
        });

        function uploadImage(file) {
            var data = new FormData();
            data.append("file", file);
            data.append("_token", $('input[name="_token"]').val());
            $.ajax({
                data: data,
                type: "POST",
                url: "/settings/users/upload-footer-img",
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {
                    var image = $('<img>').attr('src', url);
                    $('#footer-body').summernote("insertNode", image[0]);
                }
            });
        }
    </script>
@endsection
