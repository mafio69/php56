<div id="content">    
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" default>Przypisz opiekuna serwisu</h4>
    </div>
    <div id="modal-body"class="modal-body">
        <nav class="navbar navbar-default navbar-sm marg-top-min" >
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
                    <form class="navbar-form navbar-left allow-confirm flex" style="display: flex;" id="search-form">                      
                        <div>
                            <div class="row">
                                <div class="col-sm-12">
                                        <div class="form-group form-group-sm">
                                            <label>Login:</label><br>
                                            <input class="form-control" autocomplete="off" name="filter_login" type="text" value="{{ Request::has('filter_login')?Request::get('filter_login'):'' }}">
                                        </div>
                                        <div class="form-group form-group-sm">
                                            <label>Nazwisko:</label><br>
                                            <input class="form-control" autocomplete="off" name="filter_name" type="text" value="{{ Request::has('filter_name')?Request::get('filter_name'):'' }}">
                                        </div>
                                        <div class="form-group form-group-sm">
                                            <label>Email:</label><br>
                                            <input class="form-control" autocomplete="off" name="filter_email" type="text" value="{{ Request::has('filter_email')?Request::get('filter_registration'):'' }}">
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm text-center" style="border-left: 1px solid #000; padding-left: 10px; margin-right:10px; width: 130px;">
                            <label>Wyszukaj użytkownika</label><br/>
                            <button id='search' class="btn btn-xs btn-primary">
                                <i class="fa fa-search fa-fw"></i> Wyszukaj <span class="badge">{{ $users->getTotal() }}</span>
                            </button><br />
                           
                        </div>
                    </form>
                </div>
        </nav>
            <table class="table table-hover table-fixed" style="display: block;">
                    <thead>
                        <th >lp.</th>
                        <th >Login</th>
                        <th >Nazwisko</th>
                        <th>Email</th>
                        <th>Status</th>
                    </thead>
                    <?php $lp = (($users->getCurrentPage()-1)*$users->getPerPage()) + 1;?>
                    <form method="post" role="form" id="page-form">
                        {{Form::token()}}
                    @foreach ($users as $k => $user)
                        <tr class="@if($user->locked_at) bg-danger @elseif(! $user->logins->first() || ($user->password_expired_at && \Carbon\Carbon::now()->diffInDays($user->password_expired_at,false)<1)) bg-warning @endif ">
                            <td>{{$lp++}}.</td>
                            <Td>{{$user->login}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                    <a onclick="postAssign({{$user->id}}); return false;" class="btn btn-success btn-xs">
                                        <i class="fa fa-pencil fa-fw"></i>Przypisz jako opiekuna
                                    </a>
                            </td>                           
                        </tr>
                    @endforeach
                    </form>
                </table>
            <div class="center" style="clear:both;">{{  $users->links()  }}</div>
            <button class="btn btn-warning btn-block" id='modal-hide'>Anuluj</button>
    </div>
    
    <div class="content-loader-modal center" disabled style="display: none;" >
            <div class="container-fluid" style="margin: 10px">
            <i class="fa fa-circle-o-notch fa-spin fa-4x fa-fw"></i>
            </div>
    </div>
</div>
<script>
    $('#search').on('click', function(e) {
        e.preventDefault();
        $.ajax({
                type: "GET",
                url: "{{ URL::to('companies/assign-guardian', array($company->id)) }}",
                data: $('#search-form').serialize(),
                success: function (data) {
                    $('#content').html(data);  
                } 
            });
            return false;
        });

        $('.pagination a').on('click', function(e){
            e.preventDefault();
            var url = $(this).attr('href');
            $.ajax({
                type: "GET",
                url: url,
                data: $('#search-form').serialize(), 
                success: function(data){
                    $('#content').html(data);
                    }
                });
            return false;
    });
    
    $("#modal-hide").on('click', function() {
        $('#modal').modal("hide");
    });

    function postAssign (user_id) {
        $(this).button('loading');
        $.ajax({
                beforeSend: function(){
                    $('.content-loader-modal').show();
                    $('#modal-body').hide();
                },
                type: "POST",
                url: "{{ URL::route('company-guardian-set') }}",
                data: {
                    company_id : '{{ $company->id }}',
                    user_id : user_id,
                    _token: $('input[name="_token"]').val(),
                },
                success: function(data){
                    location.reload();
                }
                });
    }

</script>