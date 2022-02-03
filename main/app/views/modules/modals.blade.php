<!-- xlarge modal -->
<div class="modal fade bs-example-modal-xl " id="modal-xl" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

        </div>
    </div>
</div>

<!-- large modal -->
<div class="modal fade bs-example-modal-lg " id="modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div>

<!-- normal modal -->
<div class="modal fade bs-example-modal" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">

        </div>
    </div>
</div>

<!-- small modal -->
<div class="modal fade bs-example-modal-sm" id="modal-sm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">

        </div>
    </div>
</div>

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            if($('.bs-example-modal-xl').length > 1){
                $('.bs-example-modal-xl').each(function(index){
                    if(index > 0){
                        $(this).remove();
                    }
                });
            }
            if($('.bs-example-modal-lg').length > 1){
                $('.bs-example-modal-lg').each(function(index){
                    if(index > 0){
                        $(this).remove();
                    }
                });
            }
            if($('.bs-example-modal-sm').length > 1){
                $('.bs-example-modal-sm').each(function(index){
                    if(index > 0){
                        $(this).remove();
                    }
                });
            }
            if($('.bs-example-modal').length > 1){
                $('.bs-example-modal').each(function(index){
                    if(index > 0){
                        $(this).remove();
                    }
                });
            }

            setTimeout(function(){
                if(! modals_initiated) {
                    $('body').removeClass('page-loading');
                    modals_initiated = true;
                    $('body').on('click', '.modal-open-xl', function () {
                        var hrf = $(this).attr('target');
                        $.get(hrf, function (data) {
                            $('#modal-xl .modal-content').html(data);
                        });
                    });

                    $('body').on('click', '.modal-open-lg', function () {
                        var hrf = $(this).attr('target');
                        $.get(hrf, function (data) {
                            $('#modal-lg .modal-content').html(data);
                        });
                    });

                    $('body').on('click', '.modal-open', function (event) {
                        var hrf = $(this).attr('target');
                        $.get(hrf, function (data) {
                            $('#modal .modal-content').html(data);
                        });
                    });

                    $('body').on('click', '.modal-open-sm', function () {
                        var hrf = $(this).attr('target');
                        $.get(hrf, function (data) {
                            $('#modal-sm .modal-content').html(data);
                        });
                    });

                    $('#dialog-form').validate();

                    $('#modal').on('click', '#set', function (event) {
                        var $btn = $(this).button('loading');
                        if ($('#dialog-form').valid()) {
                            $.ajax({
                                type: "POST",
                                url: $('#dialog-form').prop('action'),
                                data: $('#dialog-form').serialize(),
                                assync: false,
                                cache: false,
                                success: function (data) {
                                    if (data.code == '0') location.reload();
                                    else if (data.code == '1') {
                                        self.location = data.url;
                                    } else if (data.code == '2') {
                                        $('#modal .modal-body').html(data.error);
                                        $btn.button('reset');
                                        $('#modal .modal-footer').html('<button type="button" onclick="location.reload();" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                                    } else {
                                        $('#modal .modal-body').html(data.error);
                                        if (isset(data.url) && data.url != '') {
                                            $btn.button('reset');
                                            $('#modal').on('hidden.bs.modal', function (e) {
                                                self.location = data.url;
                                            });
                                        }
                                    }
                                },
                                dataType: 'json'
                            });
                        } else {
                            $btn.button('reset');
                        }
                        return false;
                    });

                    $('#modal-sm').on('click', '#set', function () {
                        var $btn = $(this).button('loading');
                        if ($('#dialog-form').valid()) {
                            $.ajax({
                                type: "POST",
                                url: $('#dialog-form').prop('action'),
                                data: $('#dialog-form').serialize(),
                                assync: false,
                                cache: false,
                                success: function (data) {
                                    if (data.code == '0') location.reload();
                                    else if (data.code == '1') self.location = data.url;
                                    else {
                                        $('#modal-sm .modal-body').html(data.error);
                                        if (isset(data.url) && data.url != '') {
                                            $btn.button('reset');
                                            $('#modal-sm').on('hidden.bs.modal', function (e) {
                                                self.location = data.url;
                                            });
                                        }
                                    }
                                },
                                dataType: 'json'
                            });
                        } else {
                            $btn.button('reset');
                        }
                        return false;
                    });

                    $('#modal-lg').on('click', '#set', function () {
                        var $btn = $(this).button('loading');
                        if ($('#dialog-form').valid()) {
                            $.ajax({
                                type: "POST",
                                url: $('#dialog-form').prop('action'),
                                data: $('#dialog-form').serialize(),
                                assync: false,
                                cache: false,
                                success: function (data) {
                                    if (data.code == '0') location.reload();
                                    else if (data.code == '1') self.location = data.url;
                                    else if (data.code == '2') {
                                        $('#modal-lg .modal-body').html(data.error);
                                        $('#modal-lg .modal-footer').html('<button type="button" onclick="location.reload();" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                                    } else {
                                        $('#modal-lg .modal-body').html(data.error);
                                        if (isset(data.url) && data.url != '') {
                                            $btn.button('reset');
                                            $('#modal-lg').on('hidden.bs.modal', function (e) {
                                                self.location = data.url;
                                            });
                                        }
                                    }
                                },
                                dataType: 'json'
                            });
                        } else {
                            $btn.button('reset');
                        }
                        return false;
                    });

                    $('#modal-xl').on('click', '#set', function () {
                        var $btn = $(this).button('loading');
                        if ($('#dialog-form').valid()) {
                            $.ajax({
                                type: "POST",
                                url: $('#dialog-form').prop('action'),
                                data: $('#dialog-form').serialize(),
                                assync: false,
                                cache: false,
                                success: function (data) {
                                    if (data.code == '0') location.reload();
                                    else if (data.code == '1') self.location = data.url;
                                    else {
                                        $('#modal-xl .modal-body').html(data.error);
                                        if (isset(data.url) && data.url != '') {
                                            $btn.button('reset');
                                            $('#modal-xl').on('hidden.bs.modal', function (e) {
                                                self.location = data.url;
                                            });
                                        }
                                    }
                                },
                                dataType: 'json'
                            });
                        } else {
                            $btn.button('reset');
                        }
                        return false;
                    });

                    $('#modal, #modal-sm, #modal-lg, #modal-xl').on('hidden.bs.modal', function (e) {
                        $('#modal .modal-content').html('');
                    })
                    $('#modal-sm').on('hidden.bs.modal', function (e) {
                        $('#modal-sm .modal-content').html('');
                    })
                    $('#modal-lg').on('hidden.bs.modal', function (e) {
                        $('#modal-lg .modal-content').html('');
                    })
                }
            }, 1000);
        });
    </script>
@stop