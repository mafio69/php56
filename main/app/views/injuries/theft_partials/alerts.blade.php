@if($injury->checkIfTheftAlert('>') )
    <i class="fa fa-bell red sm-ico tips"
    title="<p>
        {{ implode("<br/>", $injury->getTheftAlerts('>')  ) }}
    </p>"
    ></i>
@endif
@if($injury->checkIfTheftAlert('==') )
    <i class="fa fa-bell-o red sm-ico tips"
    title="<p>
        {{ implode("<br/>", $injury->getTheftAlerts('==') ) }}
    </p>"
    ></i>
@endif