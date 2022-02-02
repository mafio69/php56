<select name="user_id" class="selectpicker">
    <option value="">--- wybierz ---</option>
    @foreach($allUsers as $user_id => $user)
        <option value="{{ $user_id }}" @if(!isset($users[$user_id])) disabled="disabled" @endif>{{ $user }}</option>
    @endforeach
</select>

