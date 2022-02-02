<?php
namespace Idea\Reports;

use DateTime;

class BaseReport {

    protected $groups;

    protected function parseDate($date, $modify_days)
    {
        if($date != '') {
            $date_from = new DateTime($date);
            $date_from->modify($modify_days.' day');

            return $date_from->format('Y-m-d H:i:s');
        }
        return 0;
    }

    protected function serviceType($injury)
    {
        $branch = $injury->branch;

        if($branch && $branch->id != 0 && $branch->id != '-1' && $branch->id > 0 && $branch->company && $branch->company->groups && $branch->company->groups->count() > 0) {
            return 'Serwis w grupie: '.implode(',', $branch->company->groups->lists('name'));
        }elseif($branch && $branch->id != 0 && $branch->id != '-1' && $branch->id > 0 ) {
            return 'Serwis inny - poza grupą';
        }

        return  '---';
    }

    protected function getLastActionDate($injury)
    {
        $update_date = $injury->updated_at;
        $last_message = null;
        if($injury->chat->count() > 0){
            $injury->chat->each(function ($chat) use(&$last_message) {
                if($chat->messages->count() > 0) {
                    $messages = $chat->messages;
                    $message_date = $messages->last()->created_at;
                    if (!$last_message || $message_date->gte($last_message)) {
                        $last_message = $message_date;
                    }
                }
            });
        }
        if(! $last_message)
            return $update_date->format('Y-m-d H:i');

        if( $last_message->gte($update_date) )
            return $last_message->format('Y-m-d H:i');

        return $update_date->format('Y-m-d H:i');
    }
}