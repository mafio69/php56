<div class="col-sm-12">
    <h4 class="page-header marg-top-min overflow">
        Przedmioty umowy
        <p class="pull-right btn btn-sm btn-primary add-object">
            <i class="fa fa-plus"></i> dodaj przedmiot umowy
        </p>
    </h4>
    <div class="row" id="objects-container">
    </div>
</div>

@section('headerJs')
    @parent
    <script type="text/javascript">
        function calculateGrossNet(){
            var net_value = parseFloat(0.0);
            var gross_value = parseFloat(0.0);

            $('.object-net_value').each(function(){
                if(isset($(this).val()) && $(this).val() != '')
                    net_value+=parseFloat($(this).val());
            });
            $('.object-gross_value').each(function(){
                if(isset($(this).val()) && $(this).val() != '')
                    gross_value+=parseFloat($(this).val());
            });

            $('#loan_net_value').val(net_value);
            $('#loan_gross_value').val(gross_value);
        }
        $(document).ready(function(){
            $('.add-object').on('click', function(){
                $.ajax({
                    url: "{{ URL::to('insurances/create/create-object') }}",
                    dataType: "html",
                    type: "GET",
                    success: function( data ) {
                        $('#objects-container').append(data);
                    }
                });
            });
            $('#objects-container').on('click', '.delete-object', function(){
                $(this).parents('.object-container').remove();
                calculateGrossNet();
            });
            $('#objects-container').on('change', '.object-net_value,.object-gross_value', function(){
                calculateGrossNet();
            });
        });
    </script>
@stop