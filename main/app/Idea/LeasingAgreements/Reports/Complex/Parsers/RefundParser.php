<?php

namespace Idea\LeasingAgreements\Reports\Complex\Parsers;


use Auth;
use Carbon\Carbon;
use Histories;
use LeasingAgreementInsurance;

class RefundParser {


    private $agreement;
    private $notification_number;
    private $row;

    public function __construct($agreement, $notification_number, $row)
    {
        $this->agreement = $agreement;
        $this->notification_number = $notification_number;
        $this->row = $row;
    }

    public function parse()
    {
        $agreement = $this->agreement;
        $last_insurance = $agreement->insurances->last();

        $last_insurance->active = 0;
        $last_insurance->save();

        $new_insurance = $last_insurance;
        $new_insurance->active = 1;
        $new_insurance->date_from = $this->parseRefundDate();
        $new_insurance->refund = $this->row['P'];
        $new_insurance->if_refund_contribution = 1;
        $new_insurance->user_id = Auth::id();

        $new_insurance->notification_number = $this->notification_number;
        $new_insurance->refunded_insurance_id = $last_insurance->id;

        LeasingAgreementInsurance::create($new_insurance->toArray());

        $agreement->archive = $this->parseRefundDate();
        $agreement->save();

        Histories::leasingAgreementHistory($agreement->id, 10);
    }

    private function parseRefundDate()
    {
        $datesRefund = $this->row['N'];
        $datesRefund = explode('DO', $datesRefund);

        return trim($datesRefund[0]);
    }


}