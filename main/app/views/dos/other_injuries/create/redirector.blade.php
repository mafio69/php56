<form action="{{ $url }}" method="post" id="redirectForm">
    {{ Form::token()  }}
    @foreach($data as $field_name => $field_value)
        {{ Form::hidden($field_name, $field_value) }}
    @endforeach
</form>
<script>
    document.getElementById("redirectForm").submit();
</script>
