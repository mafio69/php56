@if($injury->checkIfWreckAlert('>') || $injury->checkIfRepairAlert('>') || $injury->checkIfTheftAlert('>'))
    <i class="fa fa-bell red sm-ico tips"
    title="<p>
        {{ implode("<br/>", array_merge( $injury->getWreckAlerts('>'), $injury->getRepairAlerts('>'), $injury->getTheftAlerts('>') ) ) }}
    </p>"
    ></i>
@endif
