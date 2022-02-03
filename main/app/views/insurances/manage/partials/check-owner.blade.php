<script>
    $(document).ready(function(){
        var leasing_agreement_id  = $('input[name="leasing_agreement_id"]').val();
        $.ajax({
            url: "{{ URL::to('insurances/manage-actions/check-owner') }}",
            data: {
                leasing_agreement_id: leasing_agreement_id,
                _token: $('input[name="_token"]').val()
            },
            dataType: "json",
            type: "POST",
            success: function( data ) {
                if(data.status == 9)
                {
                    $('#modal-lg .modal-content').html('');
                    $('#modal-sm .modal-content').html('');

                    var hrf= "{{ URL::to('insurances/manage-dialog/change-owner') }}/"+leasing_agreement_id;
                    $.get( hrf, function( data ) {
                        $('#modal .modal-content').html(data);
                    });
                    $('#modal').modal('show');
                }
            }
        });
    });
</script>
