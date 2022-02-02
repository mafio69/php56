<?php
namespace Idea\Observers;

use Cache;

class CompanyVatCheckObserver
{
    public function updated($company)
    {
        Cache::forget('non_vat_companies');
        Cache::forget('changes_vat_companies');
    }

    public function created($company)
    {
        Cache::forget('non_vat_companies');
        Cache::forget('changes_vat_companies');
    }
}