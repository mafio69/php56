@if(Auth::user() && Auth::user()->signature)
    <img src="templates-src/{{Auth::user()->signature }}" alt="Podpis" style="height: 73px;"/>
@endif
