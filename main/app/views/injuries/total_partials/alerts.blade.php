@if($injury->checkIfWreckAlert('>') || $injury->checkIfRepairAlert('>'))
    <i class="fa fa-bell red sm-ico tips"
    title="<p>
        {{ implode("<br/>", array_merge( $injury->getWreckAlerts('>'), $injury->getRepairAlerts('>') ) ) }}
    </p>"
    ></i>
@endif
@if($injury->checkIfWreckAlert('==') || $injury->checkIfRepairAlert('=='))
    <i class="fa fa-bell-o red sm-ico tips"
    title="<p>
        {{ implode("<br/>", array_merge( $injury->getWreckAlerts('=='), $injury->getRepairAlerts('==') ) ) }}
    </p>"
    ></i>
@endif