<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Wygeneruj tekst do V-Desk</h4>
</div>

<div class="modal-body" style="overflow:hidden;">
    <div id="amount-alert" class="alert alert-danger">Proszę wybrać kwotę do zapłaty</div>
    <form id="generate-text">

        {{Form::token()}}

        {{ Form::number('amount', '', array('class' => 'form-control currency', 'id' => 'amount','name' => 'amount', 'placeholder' => 'Do zapłaty', 'style' => 'margin-left: 5px', 'required')) }}

        {{ Form::textarea('description', '', array('class' => 'form-control textarea', 'id' => 'description', 'name' => 'description','placeholder' => 'Uwagi', 'style' => 'margin-left: 5px', 'disabled')) }}
        <div class="pull-right">
            <button type="button" class="btn btn-sm btn-default" onclick="copyDescription('#description')">
                <i class="fa fa-copy"></i><span> Kopiuj</span>
            </button>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Wyjście</button>
    <button class="btn btn-primary" onclick="generateText()">Wygeneruj tekst</button>
</div>
<style>
    .currency {
            margin: 5px;
        }
    .textarea {
        margin: 5px;
    }
    .modal-open {
        overflow-y: auto;
    }
    .modal
    {
        overflow: hidden;
        bottom: auto;
        right: auto;
    }
    .modal-dialog{
        margin-right: 0;
        margin-left: 0;
    }
    #modal {
        left:65%;
    }

</style>
<script>

$(document).ready(function(){
    setAmountAlert(false);
});

function generateText() {
    invoiceId = "<?php echo $id ?>";
    amount = $("#amount").val();
    if(amount) {
        $.ajax({
                url: '/injuries/docs/get-generate-v-desk-text/' + invoiceId + "/" + amount,
                type:"GET",
                dataType:"json",

                success:function(data) {
                    $('#description').val(data.data);
                    setAmountAlert(false)
                }
            });
    } else {
        setAmountAlert(true)
    }

}

function setAmountAlert(show) {
    if(show) {
        $('#amount-alert').show();
        $('#amount').addClass('alert-warning');
    }
    else {
        $('#amount-alert').hide();
        $('#amount').removeClass('alert-warning');
    }
}

function copyDescription(id){
    var $temp = $("<textarea>");
    $("#generate-text").append($temp);
    $temp.val($(id).val()).select();
    document.execCommand("copy");
    $temp.remove();
}

$('.currency').blur(function() {
    let val = $(this).val();
    $(this).val(parseFloat(val).toFixed(2));
});

$("#modal").draggable({
      handle: ".modal-content"
  });

</script>
    