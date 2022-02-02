<div class="col-sm-12">
    <h4 class="page-header marg-top-min overflow">
        Przedmioty umowy (jachty)
        <p class="pull-right btn btn-sm btn-primary add-yacht">
            <i class="fa fa-plus"></i> dodaj jacht
        </p>
    </h4>
    <div class="row" id="yachts-container">
        <div class="col-sm-12 col-md-6" id="yacht-loading" style="display:none">
            <h1 class="text-center">
                <i class="fa fa-circle-o-notch fa-spin"></i>
            </h1>
        </div>
    </div>
</div>

@section('headerJs')
    @parent
    <script type="text/javascript">
        function calculateGrossNet(){
            var net_value = parseFloat(0.0);
            var gross_value = parseFloat(0.0);

            $('.yacht-net_value').each(function(){
                if(isset($(this).val()) && $(this).val() != '')
                    net_value+=parseFloat($(this).val());
            });
            $('.yacht-gross_value').each(function(){
                if(isset($(this).val()) && $(this).val() != '')
                    gross_value+=parseFloat($(this).val());
            });

            $('#loan_net_value').val(net_value);
            $('#loan_gross_value').val(gross_value);
        }
        $(document).ready(function(){
            $('.add-yacht').on('click', function(){
                $.ajax({
                    url: "{{ URL::to('insurances/create-yacht/create-yacht') }}",
                    dataType: "html",
                    type: "GET",
                    beforeSend : function (){
                        $('#yacht-loading').show()
                    },
                    success: function( data ) {
                        $('#yacht-loading').hide()
                        $(data).insertBefore('#yacht-loading');
                    }
                });
            });
            $('#yachts-container').on('click', '.delete-yacht', function(){
                $(this).parents('.yacht-container').remove();
                calculateGrossNet();
            });
            $('#yachts-container').on('change', '.yacht-net_value,.yacht-gross_value', function(){
                calculateGrossNet();
            });
        });
    </script>
@stop