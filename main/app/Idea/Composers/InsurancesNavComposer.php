<?php

namespace Idea\Composers;


class InsurancesNavComposer
{
    public function compose($view)
    {
        $companies = \Insurance_companies::lists('name', 'id');

        $companies[0] = " --- wszystkie --- ";
        asort($companies);

        $view->with('insurance_companies_nav_list', $companies);
    }
}