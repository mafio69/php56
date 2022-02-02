<?php

namespace Idea\Logging\LeasingAgreements\Translations;


class ObjectTranslator extends BaseTranslator{

    protected $translations = [
        'name' => 'Nazwa przedmiotu',
        'object_assetType_id' => 'Kategoria przedmiotu',
        'net_value' => 'Wartość netto przedmiotu',
        'gross_value' => 'Wartość brutto przedmiotu'
    ];

    protected function object_assetType_id($key, $values)
    {
        if(isset($values['old_value']) && $values['old_value'] != '' && !is_null($values['old_value']))
            $old_value = \ObjectAssetType::find($values['old_value'])->name;
        else
            $old_value = '';

        $new_value = \ObjectAssetType::find($values['new_value'])->name;

        return [$this->translations[$key] =>
                    ['old_value' => $old_value, 'new_value' => $new_value]
                ] ;
    }
}