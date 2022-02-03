<?php

use Symfony\Component\HttpFoundation\StreamedResponse;

class InsurancesManageController extends BaseController
{

    /**
     * InsurancesManageController constructor.
     */
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:wykaz_polis#wejscie');

    }

    public function getIndex()
    {
        $query = LeasingAgreement::has('insurances', '<', 1)
            ->whereNull('withdraw');
        //czy ustawione jest filtrowanie wyszukiwaniem
        $this->passingWheres($query);

        $leasingAgreements = $query->where('has_yacht', 0)
            ->where('if_foreign', 0)
            ->with('client', 'user', 'leasingAgreementType', 'leasingAgreementPaymentWay')
            ->orderBy('id', 'asc')->paginate(Session::get('search.pagin', '10'));

        return View::make('insurances.manage.new', compact('leasingAgreements'));
    }

    public function getInprogress()
    {
        $query = LeasingAgreement::has('insurances', '>', 0)
            ->whereNull('withdraw');
        //czy ustawione jest filtrowanie wyszukiwaniem
        $this->passingWheres($query);
        $leasingAgreements = $query->where('has_yacht', 0)
            ->where('if_foreign', 0)
            ->whereNull('archive')
            ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'insurances' => function ($query) {
                $query->active();
            }), 'insurances.insuranceCompany', 'refundedInsurances')
            ->orderBy('reported_to_resume', 'desc')
            ->orderBy('id', 'asc')->paginate(Session::get('search.pagin', '10'));

        return View::make('insurances.manage.inprogress', compact('leasingAgreements'));
    }

    public function getResume()
    {
        $query = LeasingAgreement::
            select('leasing_agreements.*')
            ->distinct()
            ->whereNull('withdraw')
            ->has('insurances', '>', 0);
        //czy ustawione jest filtrowanie wyszukiwaniem
        $this->passingWheres($query);
        $leasingAgreements = $query->where('has_yacht', 0)
            ->where('if_foreign', 0)
            ->whereNull('archive')
            ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'insurances' => function ($query) {
                $query->whereActive(1);
            }), 'refundedInsurances')
            ->join('leasing_agreement_insurances', function ($join) {
                $join->on('leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')
                    ->where('leasing_agreement_insurances.active', '=', 1);

            })
            ->where(function ($query) {
                $to = \Carbon\Carbon::now()->endOfMonth();
                $from = \Carbon\Carbon::now()->startOfMonth();
                $query->whereActive('1')->whereBetween('date_to', array($from, $to));
            })
            ->orderBy('reported_to_resume', 'desc')
            ->orderBy('leasing_agreement_insurances.date_to', 'desc')
            ->orderBy('leasing_agreements.id', 'asc')
            ->paginate(Session::get('search.pagin', '10'));

        return View::make('insurances.manage.resume', compact('leasingAgreements'));
    }

    public function getResumeOutdated($submonth)
    {
        $now = \Carbon\Carbon::now();

        $query = LeasingAgreement::
            select(DB::raw('YEAR(leasing_agreement_insurances.date_to) year, MONTH(leasing_agreement_insurances.date_to) month'), DB::raw('count(*) as insurances_ct'))
            ->distinct()
            ->whereNull('withdraw')
            ->has('insurances', '>', 0);
        //czy ustawione jest filtrowanie wyszukiwaniem
        $this->passingWheres($query);
        $months_db = $query->whereNull('archive')
            ->join('leasing_agreement_insurances', function ($join) use ($now) {
                $join->on('leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')->where('leasing_agreement_insurances.active', '=', 1)->where('date_to', '<', $now);
            })
            ->groupBy('year')
            ->groupBy('month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $months = array();

        //return ($months_db);
        foreach ($months_db as $month) {
            if ($month->year > 0 && !is_null($month->year)) {
                $month_date = \Carbon\Carbon::createFromFormat('Y-n', $month->year . '-' . $month->month);
                $months_diff = $now->diffInMonths($month_date);
                $months[$months_diff] = $month->insurances_ct;
            }
        }

        $query = LeasingAgreement::
            select('leasing_agreements.*')
            ->distinct()
            ->whereNull('withdraw')
            ->has('insurances', '>', 0)
            ->where('has_yacht', 0)
            ->where('if_foreign', 0);
        //czy ustawione jest filtrowanie wyszukiwaniem
        $this->passingWheres($query);
        $leasingAgreements = $query->whereNull('archive')
            ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'insurances' => function ($query) {
                $query->whereActive(1);
            }), 'refundedInsurances')
            ->join('leasing_agreement_insurances', function ($join) {
                $join->on('leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')
                    ->where('leasing_agreement_insurances.active', '=', 1);

            })
            ->where(function ($query) use ($submonth) {
                if ($submonth == 'older') {
                    $to = \Carbon\Carbon::now()->subMonths(7)->endOfMonth();
                    $query->where('leasing_agreement_insurances.date_to', '<', $to);
                } else {
                    $to = \Carbon\Carbon::now()->subMonths($submonth)->endOfMonth();
                    $from = \Carbon\Carbon::now()->subMonths($submonth)->startOfMonth();
                    $query->whereBetween('leasing_agreement_insurances.date_to', array($from, $to));
                }
            })
            ->orderBy('reported_to_resume', 'desc')
            ->orderBy('leasing_agreement_insurances.date_to', 'desc')
            ->orderBy('leasing_agreements.id', 'asc')
            ->paginate(Session::get('search.pagin', '10'));

        return View::make('insurances.manage.resume-outdated', compact('leasingAgreements', 'months'));
    }

    public function getFutureResume()
    {
        $query = LeasingAgreement::
            select('leasing_agreements.*')
            ->distinct()
            ->whereNull('withdraw')
            ->whereHas('insurances', function ($query) {
                $query->whereActive(1);
            });
        //czy ustawione jest filtrowanie wyszukiwaniem
        $this->passingWheres($query);
        $leasingAgreements = $query->where('has_yacht', 0)
            ->where('if_foreign', 0)
            ->whereNull('archive')
            ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'insurances' => function ($query) {
                $query->whereActive(1);
            }), 'refundedInsurances')
            ->join('leasing_agreement_insurances', function ($join) {
                $join->on('leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')
                    ->where('leasing_agreement_insurances.active', '=', 1);

            })
            ->where(function ($query) {
                $to = \Carbon\Carbon::now()->addMonths(4)->endOfMonth();
                $from = \Carbon\Carbon::now()->addMonths(1)->startOfMonth();
                $query->whereActive('1')->whereBetween('date_to', array($from, $to));
            })
            ->orderBy('reported_to_resume', 'desc')
            ->orderBy('leasing_agreement_insurances.date_to', 'desc')
            ->orderBy('leasing_agreements.id', 'asc')
            ->paginate(Session::get('search.pagin', '10'));

        return View::make('insurances.manage.future-resume', compact('leasingAgreements'));
    }

    public function getArchive()
    {
        $leasingAgreements = LeasingAgreement::
            distinct()
            ->where(function ($query) {
                //czy ustawione jest filtrowanie wyszukiwaniem
                $this->passingWheres($query);
            })
            ->whereNull('withdraw')
            ->whereNotNull('archive')
            ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'insurances' => function ($query) {
                $query->active();
            }), 'insurances.insuranceCompany', 'refundedInsurances')
            ->orderBy('id', 'asc')->paginate(Session::get('search.pagin', '10'));

        return View::make('insurances.manage.archive', compact('leasingAgreements'));

    }

    public function getWithdraw()
    {
        $leasingAgreements = LeasingAgreement::
            where(function ($query) {
            //czy ustawione jest filtrowanie wyszukiwaniem
            $this->passingWheres($query);
        })
            ->distinct()
            ->whereNotNull('withdraw')
            ->with('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'refundedInsurances')
            ->orderBy('id', 'asc')->paginate(Session::get('search.pagin', '10'));
        return View::make('insurances.manage.withdraw', compact('leasingAgreements'));
    }

    public function getOther()
    {
        $to = \Carbon\Carbon::now()->addDays(60)->format('Y-m-d');
        $leasingAgreements = LeasingAgreement::
            select('leasing_agreements.*')
            ->has('insurances', '>', 0)
            ->where(function ($query) {
                //czy ustawione jest filtrowanie wyszukiwaniem
                $this->passingWheres($query);
            })
            ->whereNull('withdraw')
            ->whereNull('archive')
            ->where(function ($query) {
                $query->where("has_yacht", 1)->orWhere('if_foreign', 1);
            })
            ->whereHas('insurances', function ($query) use ($to) {
                $query->where('active', 1);
                $query->where('date_to', '>', $to);
            })
            ->with('insurances')
            ->distinct()
            ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'insurances' => function ($query) {
                $query->orderBy('date_to', 'desc');
            }))
            ->paginate(Session::get('search.pagin', '10'));

        return View::make('insurances.manage.other', compact('leasingAgreements'));
    }

    public function getOtherNew()
    {
        $leasingAgreements = LeasingAgreement::
            has('insurances', '<', 1)
            ->where(function ($query) {
                //czy ustawione jest filtrowanie wyszukiwaniem
                $this->passingWheres($query);
            })
            ->where(function ($query) {
                $query->where("has_yacht", 1)->orWhere('if_foreign', 1);
            })
            ->whereNull('archive')
            ->whereNull('withdraw')
            ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'refundedInsurances', 'insurances' => function ($query) {
                $query->orderBy('date_to', 'desc');
            }))
            ->distinct()
            ->paginate(Session::get('search.pagin', '10'));
        return View::make('insurances.manage.other-new', compact('leasingAgreements'));
    }

    public function getOtherResume()
    {
        $inputs = Input::all();
        $to = \Carbon\Carbon::now()->addDays(60)->format('Y-m-d');
        $leasingAgreements = LeasingAgreement::
            has('insurances', '>', 0)
            ->select('leasing_agreements.*', DB::raw('MAX(lai.date_to) as max_date'))
            ->join('leasing_agreement_insurances as lai', function ($join) use ($to) {
                $join->on('leasing_agreements.id', '=', 'lai.leasing_agreement_id')->where('lai.date_to', '<', $to)->whereNull('lai.deleted_at');
            })
            ->where(function ($query) {
                //czy ustawione jest filtrowanie wyszukiwaniem
                $this->passingWheres($query);
            })
            ->whereNull('archive')
            ->where(function ($query) {
                $query->where("has_yacht", 1)->orWhere('if_foreign', 1);
            })
            ->whereHas('insurances', function ($query) use ($to) {
                $query->where('date_to', '<', $to);
            })
            ->whereDoesntHave('insurances', function ($query) use ($to) {
                $query->where('date_to', '>', $to);
            })
            ->with(['insurances' => function ($query) use ($to) {
                $query->orderBy('date_to', 'desc');
            }, 'insurances.insuranceCompany'])
            ->groupBy('leasing_agreements.id')
            ->orderBy('max_date', 'asc')
            ->paginate(Session::get('search.pagin', '10'));
            
            return View::make('insurances.manage.other-resume', compact('leasingAgreements', 'finalleasingAgreements'));
    }

    public function getSearch()
    {
        $last = URL::previous();
        $url = strtok($last, '?');

        $gets = '?';

        if (Input::has('search_term')) {

            if (Input::has('nr_contract')) {
                $gets .= 'nr_contract=1&';
            }

            if (Input::has('client_name')) {
                $gets .= 'client_name=1&';
            }

            if (Input::has('client_NIP')) {
                $gets .= 'client_NIP=1&';
            }

            if (Input::has('object_name')) {
                $gets .= 'object_name=1&';
            }

            if (Input::has('policy_nb')) {
                $gets .= 'policy_nb=1&';
            }

            $gets .= 'term=' . Input::get('search_term') . '&';
        }

        if (Input::has('warnings')) {
            $gets .= 'warnings=1&';
        }

        if (Input::has('yachts')) {
            $gets .= 'yachts=1&';
        }

        if (Input::has('foreign_policy')) {
            $gets .= 'foreign_policy=1&';
        }

        if (Input::has('global')) {
            return URL::to('insurances/manage/search-global') . $gets;
        } else {
            return $url . $gets;
        }
    }

    public function postSearch()
    {
        $last = URL::previous();
        $url = strtok($last, '?');

        $gets = '?';

        if (Input::has('search_term')) {

            if (Input::has('nr_contract')) {
                $gets .= 'nr_contract=1&';
            }

            if (Input::has('client_name')) {
                $gets .= 'client_name=1&';
            }

            if (Input::has('client_NIP')) {
                $gets .= 'client_NIP=1&';
            }

            if (Input::has('object_name')) {
                $gets .= 'object_name=1&';
            }

            if (Input::has('policy_nb')) {
                $gets .= 'policy_nb=1&';
            }

            $gets .= 'term=' . Input::get('search_term') . '&';
        }

        if (Input::has('warnings')) {
            $gets .= 'warnings=1&';
        }

        if (Input::has('yachts')) {
            $gets .= 'yachts=1&';
        }

        if (Input::has('foreign_policy')) {
            $gets .= 'foreign_policy=1&';
        }

        if (Input::has('global')) {
            return URL::to('insurances/manage/search-global') . $gets;
        } else {
            return $url . $gets;
        }
    }

    public function getSearchGlobal()
    {
        $leasingAgreements = LeasingAgreement::
            where(function ($query) {
            //czy ustawione jest filtrowanie wyszukiwaniem
            $this->passingWheres($query);
        })
            ->with('insurances', 'client', 'user', 'leasingAgreementType', 'leasingAgreementPaymentWay')
            ->orderBy('id', 'asc')->paginate(Session::get('search.pagin', '10'));

        return View::make('insurances.manage.searchGlobal', compact('leasingAgreements'));
    }

    public function postGenerateExcel($step)
    {
        set_time_limit(500);
        DB::disableQueryLog();
        switch ($step) {
            case 'index':
                Excel::create('polisy-nowe', function ($excel) {
                    $excel->sheet('Export', function ($sheet) {
                        $sheet->appendRow(array('Zestawienie umów nowych na dzień ' . date('Y-m-d')));
                        $sheet->appendRow(array());
                        $sheet->appendRow($this->generateHeaders('index'));

                        $query = LeasingAgreement::has('insurances', '<', 1)
                            ->whereNull('withdraw');

                        $this->passingWheres($query, (Input::has('parameters')) ? Input::get('parameters') : null);

                        $query->where('has_yacht', 0)
                            ->where('if_foreign', 0)
                            ->with('client', 'user', 'leasingAgreementType', 'leasingAgreementPaymentWay')
                            ->orderBy('id', 'asc')->chunk(100, function ($agreements) use (&$sheet) {
                            foreach ($agreements as $leasingAgreement) {
                                $sheet->appendRow(array(
                                    $leasingAgreement->nr_contract,
                                    $leasingAgreement->nr_agreement,
                                    $leasingAgreement->client->name,
                                    $leasingAgreement->import_insurance_company,
                                    $leasingAgreement->user->name,
                                    substr($leasingAgreement->created_at, 0, -3),
                                ));
                            }
                        });
                    });
                })->export('xls');

                break;
            case 'inprogress':

                set_time_limit(0);
                $response = new StreamedResponse(function () {
                    // Open output stream
                    $handle = fopen('php://output', 'w');

                    // Add CSV headers
                    fputcsv($handle, $this->generateHeaders('inprogress'));
                    LeasingAgreement::has('insurances', '>', 0)
                        ->whereNull('withdraw')
                        ->where(function ($query) {
                            //czy ustawione jest filtrowanie wyszukiwaniem
                            $this->passingWheres($query, (Input::has('parameters')) ? Input::get('parameters') : null);
                        })
                        ->whereNull('archive')
                        ->where('has_yacht', 0)
                        ->where('if_foreign', 0)
                        ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'insurances' => function ($query) {
                            $query->active();
                        }), 'insurances.insuranceCompany')
                        ->orderBy('reported_to_resume', 'desc')
                        ->chunk(250, function ($agreements) use (&$handle) {
                            foreach ($agreements as $leasingAgreement) {
                                $row = [];
                                $row[] = ($leasingAgreement->nr_contract);
                                $row[] = (($leasingAgreement->client) ? $leasingAgreement->client->name : '--- błąd importu leasingobiorcy ---');
                                $row[] = ($leasingAgreement->insurances->first()->insuranceCompany) ? $leasingAgreement->insurances->first()->insuranceCompany->name : '---';
                                $row[] = ($leasingAgreement->insurances->first()->date_from);
                                $row[] = ($leasingAgreement->insurances->first()->date_to);
                                $row[] = ($leasingAgreement->loan_net_value);
                                $row[] = ($leasingAgreement->nr_agreement);
                                $row[] = ('="' . $leasingAgreement->insurances->first()->insurance_number .'"');
                                $row[] = ('="' . $leasingAgreement->insurances->first()->notification_number.'"');
                                fputcsv($handle, $row);
                            }
                        });

                    // Close the output stream
                    fclose($handle);
                }, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="policsy_trwajace.csv"',
                ]);

                return $response;
                break;
            case 'resume':
                Excel::create('wznowienia aktualne', function ($excel) {
                    $excel->sheet('Export', function ($sheet) {
                        $sheet->appendRow(array('Zestawienie umów wznowienia aktualne na dzień ' . date('Y-m-d')));
                        $sheet->appendRow(array());
                        $sheet->appendRow($this->generateHeaders('resume'));

                        LeasingAgreement::
                            select('leasing_agreements.*')
                            ->whereNull('withdraw')
                            ->has('insurances', '>', 0)
                            ->where(function ($query) {
                                //czy ustawione jest filtrowanie wyszukiwaniem
                                $this->passingWheres($query);
                            })
                            ->whereNull('archive')
                            ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'insurances' => function ($query) {
                                $query->whereActive(1);
                            }))
                            ->join('leasing_agreement_insurances', function ($join) {
                                $join->on('leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')
                                    ->where('leasing_agreement_insurances.active', '=', 1);

                            })
                            ->where('has_yacht', 0)
                            ->where('if_foreign', 0)
                            ->where(function ($query) {
                                $to = \Carbon\Carbon::now()->endOfMonth();
                                $from = \Carbon\Carbon::now()->startOfMonth();
                                $query->whereActive('1')->whereBetween('date_to', array($from, $to));
                            })
                            ->orderBy('reported_to_resume', 'desc')
                            ->orderBy('leasing_agreement_insurances.date_to', 'desc')
                            ->orderBy('leasing_agreements.id', 'asc')
                            ->chunk(100, function ($agreements) use (&$sheet) {
                                foreach ($agreements as $leasingAgreement) {
                                    $sheet->appendRow(array(
                                        $leasingAgreement->nr_contract,
                                        ($leasingAgreement->client) ? $leasingAgreement->client->name : '--- błąd importu leasingobiorcy ---</i>',
                                        ($leasingAgreement->insurances->first()->insuranceCompany) ? $leasingAgreement->insurances->first()->insuranceCompany->name : '---',
                                        $leasingAgreement->insurances->first()->date_from,
                                        $leasingAgreement->insurances->first()->date_to,
                                        $leasingAgreement->loan_net_value,
                                        $leasingAgreement->nr_agreement,
                                        '="' . $leasingAgreement->insurances->first()->insurance_number . '"',
                                        $leasingAgreement->insurances->first()->notification_number,
                                    ));
                                }
                            });
                    });
                })->export('xls');
                break;
            case 'resume-outdated':

                Excel::create('wznowienia minione', function ($excel) {
                    $excel->sheet('Export', function ($sheet) {
                        $submonth = Input::get('resume-outdated');

                        if ($submonth == 'older') {
                            $from = '';
                            $to = \Carbon\Carbon::now()->subMonths(7)->endOfMonth();
                        } else {
                            $to = \Carbon\Carbon::now()->subMonths($submonth)->endOfMonth();
                            $from = \Carbon\Carbon::now()->subMonths($submonth)->startOfMonth();
                        }
                        $sheet->appendRow(array('Zestawienie umów wznowienia minione na dzień ' . date('Y-m-d')));
                        $sheet->appendRow(array(($from != '') ? 'od dnia ' . $from : '', 'do dnia ' . $to));
                        $sheet->appendRow(array());
                        $sheet->appendRow($this->generateHeaders('resume-outdated'));

                        LeasingAgreement::
                            select('leasing_agreements.*')
                            ->whereNull('withdraw')
                            ->has('insurances', '>', 0)
                            ->where(function ($query) {
                                //czy ustawione jest filtrowanie wyszukiwaniem
                                $this->passingWheres($query, (Input::has('parameters')) ? Input::get('parameters') : null);
                            })
                            ->whereNull('archive')
                            ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'insurances' => function ($query) {
                                $query->whereActive(1);
                            }))
                            ->join('leasing_agreement_insurances', function ($join) {
                                $join->on('leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')
                                    ->where('leasing_agreement_insurances.active', '=', 1);

                            })
                            ->where('has_yacht', 0)
                            ->where('if_foreign', 0)
                            ->where(function ($query) use ($submonth) {
                                if ($submonth == 'older') {
                                    $to = \Carbon\Carbon::now()->subMonths(7)->endOfMonth();
                                    $query->where('leasing_agreement_insurances.date_to', '<', $to);
                                } else {
                                    $to = \Carbon\Carbon::now()->subMonths($submonth)->endOfMonth();
                                    $from = \Carbon\Carbon::now()->subMonths($submonth)->startOfMonth();
                                    $query->whereBetween('leasing_agreement_insurances.date_to', array($from, $to));
                                }
                            })
                            ->orderBy('reported_to_resume', 'desc')
                            ->orderBy('leasing_agreement_insurances.date_to', 'desc')
                            ->orderBy('leasing_agreements.id', 'asc')
                            ->chunk(100, function ($agreements) use (&$sheet) {
                                foreach ($agreements as $leasingAgreement) {
                                    $sheet->appendRow(array(
                                        $leasingAgreement->nr_contract,
                                        ($leasingAgreement->client) ? $leasingAgreement->client->name : '--- błąd importu leasingobiorcy ---</i>',
                                        ($leasingAgreement->insurances->first()->insuranceCompany) ? $leasingAgreement->insurances->first()->insuranceCompany->name : '---',
                                        $leasingAgreement->insurances->first()->date_from,
                                        $leasingAgreement->insurances->first()->date_to,
                                        $leasingAgreement->loan_net_value,
                                        $leasingAgreement->nr_agreement,
                                        '="' . $leasingAgreement->insurances->first()->insurance_number. '"',
                                        $leasingAgreement->insurances->first()->notification_number,
                                    ));
                                }
                            });
                    });
                })->export('xls');
                break;
             case 'future-resume':

                Excel::create('wznowienia przyszłe', function ($excel) {
                    $excel->sheet('Export', function ($sheet) {
                        $submonth = Input::get('resume-outdated');

                        if ($submonth == 'older') {
                            $from = '';
                            $to = \Carbon\Carbon::now()->subMonths(7)->endOfMonth();
                        } else {
                            $to = \Carbon\Carbon::now()->subMonths($submonth)->endOfMonth();
                            $from = \Carbon\Carbon::now()->subMonths($submonth)->startOfMonth();
                        }
                        $sheet->appendRow(array('Zestawienie umów wznowienia przyszłe na dzień ' . date('Y-m-d')));
                        $sheet->appendRow(array(($from != '') ? 'od dnia ' . $from : '', 'do dnia ' . $to));
                        $sheet->appendRow(array());
                        $sheet->appendRow($this->generateHeaders('future-resume'));

                        $query = LeasingAgreement::
                            select('leasing_agreements.*')
                            ->distinct()
                            ->whereNull('withdraw')
                            ->whereHas('insurances', function ($query) {
                                $query->whereActive(1);
                            });
                            //czy ustawione jest filtrowanie wyszukiwaniem
                            $this->passingWheres($query);
                            $leasingAgreements = $query->where('has_yacht', 0)
                                ->where('if_foreign', 0)
                                ->whereNull('archive')
                                ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'insurances' => function ($query) {
                                    $query->whereActive(1);
                                }), 'refundedInsurances')
                                ->join('leasing_agreement_insurances', function ($join) {
                                    $join->on('leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')
                                        ->where('leasing_agreement_insurances.active', '=', 1);

                                })
                                ->where(function ($query) {
                                    $to = \Carbon\Carbon::now()->addMonths(4)->endOfMonth();
                                    $from = \Carbon\Carbon::now()->addMonths(1)->startOfMonth();
                                    $query->whereActive('1')->whereBetween('date_to', array($from, $to));
                                })
                                ->orderBy('reported_to_resume', 'desc')
                                ->orderBy('leasing_agreement_insurances.date_to', 'desc')
                                ->orderBy('leasing_agreements.id', 'asc')
                                ->chunk(100, function ($agreements) use (&$sheet) {
                                    foreach ($agreements as $leasingAgreement) {
                                        $sheet->appendRow(array(
                                            $leasingAgreement->nr_contract,
                                            ($leasingAgreement->client) ? $leasingAgreement->client->name : '--- błąd importu leasingobiorcy ---</i>',
                                            ($leasingAgreement->insurances->first()->insuranceCompany) ? $leasingAgreement->insurances->first()->insuranceCompany->name : '---',
                                            $leasingAgreement->insurances->first()->date_from,
                                            $leasingAgreement->insurances->first()->date_to,
                                            $leasingAgreement->loan_net_value,
                                            $leasingAgreement->nr_agreement
                                        ));
                                    }
                                });
                    });
                })->export('xls');
                break;
            case 'archive':
                set_time_limit(0);
                $response = new StreamedResponse(function () {
                    // Open output stream
                    $handle = fopen('php://output', 'w');

                    // Add CSV headers
                    fputcsv($handle, $this->generateHeaders('archive'));
                    LeasingAgreement::
                        select('leasing_agreements.*')
                        ->distinct()
                        ->whereNull('withdraw')
                        ->whereNotNull('archive')
                        ->where(function ($query) {
                            //czy ustawione jest filtrowanie wyszukiwaniem
                            $this->passingWheres($query, (Input::has('parameters')) ? Input::get('parameters') : null);
                        })
                        ->with(array('client', 'insurances', 'insurances.insuranceCompany'))
                        ->orderBy('id', 'asc')
                        ->chunk(250, function ($agreements) use (&$handle) {
                            foreach ($agreements as $leasingAgreement) {
                                $row = [];
                                $row[] = $leasingAgreement->nr_contract;
                                $row[] = ($leasingAgreement->client) ? $leasingAgreement->client->name : '--- błąd importu leasingobiorcy ---';
                                $row[] = ($leasingAgreement->insurances->first()->insuranceCompany) ? $leasingAgreement->insurances->first()->insuranceCompany->name : '---';
                                $row[] = $leasingAgreement->insurances->first()->date_from;
                                $row[] = $leasingAgreement->insurances->first()->date_to;
                                $row[] = $leasingAgreement->archive;
                                $row[] = $leasingAgreement->loan_net_value;
                                $row[] = $leasingAgreement->nr_agreement;
                                fputcsv($handle, $row);
                            }
                        });

                    // Close the output stream
                    fclose($handle);
                }, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="archiwum.csv"',
                ]);

                return $response;
                break;

            case 'withdraw':
                set_time_limit(0);
                $response = new StreamedResponse(function () {
                    // Open output stream
                    $handle = fopen('php://output', 'w');

                    // Add CSV headers
                    fputcsv($handle, $this->generateHeaders('withdraw'));
                    LeasingAgreement::
                        select('leasing_agreements.*')
                        ->distinct()
                        ->whereNotNull('withdraw')
                        ->where(function ($query) {
                            //czy ustawione jest filtrowanie wyszukiwaniem
                            $this->passingWheres($query, (Input::has('parameters')) ? Input::get('parameters') : null);
                        })
                        ->with(array('client', 'insurances', 'insurances.insuranceCompany'))
                        ->orderBy('id', 'asc')
                        ->chunk(250, function ($agreements) use (&$handle) {

                            foreach ($agreements as $leasingAgreement) {
                                $row = [];
                                $row[] = $leasingAgreement->nr_contract;
                                $row[] = $leasingAgreement->nr_agreement;
                                $row[] = ($leasingAgreement->client) ? $leasingAgreement->client->name : '--- błąd importu leasingobiorcy ---';
                                $row[] = $leasingAgreement->user->name;
                                $row[] = $leasingAgreement->created_at;
                                $row[] = $leasingAgreement->withdraw;
                                $row[] = $leasingAgreement->withdraw_reason_id != 3 ? Config::get('definition.withdrawReasons.' . $leasingAgreement->withdraw_reason_id) : $leasingAgreement->withdraw_reason;
                                fputcsv($handle, $row);
                            }
                        });

                    // Close the output stream
                    fclose($handle);
                }, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="wycofane.csv"',
                ]);

                return $response;
                break;

            case 'other':
                set_time_limit(0);
                $response = new StreamedResponse(function () {
                    // Open output stream
                    $handle = fopen('php://output', 'w');

                    // Add CSV headers
                    fputcsv($handle, $this->generateHeaders('other'));
                    $to = \Carbon\Carbon::now()->addDays(60)->format('Y-m-d');
                    LeasingAgreement::
                        select('leasing_agreements.*')
                        ->has('insurances', '>', 0)
                        ->where(function ($query) {
                            //czy ustawione jest filtrowanie wyszukiwaniem
                            $this->passingWheres($query);
                        })
                        ->whereNull('withdraw')
                        ->whereNull('archive')
                        ->where(function ($query) {
                            $query->where("has_yacht", 1)->orWhere('if_foreign', 1);
                        })
                        ->whereHas('insurances', function ($query) use ($to) {
                            $query->where('active', 1);
                            $query->where('date_to', '>', $to);
                        })
                        ->with('insurances')
                        ->distinct()
                        ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'insurances' => function ($query) {
                            $query->orderBy('date_to', 'desc');
                        }))
                        ->chunk(250, function ($agreements) use (&$handle) {

                            foreach ($agreements as $leasingAgreement) {
                                $row = [];
                                $row[] = $leasingAgreement->nr_contract;
                                $row[] = $leasingAgreement->nr_agreement;
                                $row[] = ($leasingAgreement->client) ? $leasingAgreement->client->name : '--- błąd importu leasingobiorcy ---';
                                $row[] = $leasingAgreement->user->name;
                                $row[] = $leasingAgreement->created_at;
                                $row[] = ('="' . $leasingAgreement->insurances->first()->insurance_number .'"');
                                $row[] = ('="' . $leasingAgreement->insurances->first()->notification_number.'"');
                                fputcsv($handle, $row);
                            }
                        });

                    // Close the output stream
                    fclose($handle);
                }, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="obce.csv"',
                ]);

                return $response;
                break;

            case 'other-new':
                set_time_limit(0);
                $response = new StreamedResponse(function () {
                    // Open output stream
                    $handle = fopen('php://output', 'w');

                    // Add CSV headers
                    fputcsv($handle, $this->generateHeaders('other'));
                    LeasingAgreement::
                        has('insurances', '<', 1)
                        ->where(function ($query) {
                            //czy ustawione jest filtrowanie wyszukiwaniem
                            $this->passingWheres($query);
                        })
                        ->where(function ($query) {
                            $query->where("has_yacht", 1)->orWhere('if_foreign', 1);
                        })
                        ->whereNull('archive')
                        ->whereNull('withdraw')
                        ->with(array('client', 'leasingAgreementType', 'leasingAgreementPaymentWay', 'refundedInsurances', 'insurances' => function ($query) {
                            $query->orderBy('date_to', 'desc');
                        }))
                        ->distinct()
                        ->chunk(250, function ($agreements) use (&$handle) {

                            foreach ($agreements as $leasingAgreement) {
                                $row = [];
                                $row[] = $leasingAgreement->nr_contract;
                                $row[] = $leasingAgreement->nr_agreement;
                                $row[] = ($leasingAgreement->client) ? $leasingAgreement->client->name : '--- błąd importu leasingobiorcy ---';
                                $row[] = $leasingAgreement->user->name;
                                $row[] = $leasingAgreement->created_at;
                                fputcsv($handle, $row);
                            }
                        });

                    // Close the output stream
                    fclose($handle);
                }, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="obce_nowe.csv"',
                ]);

                return $response;
                break;

            case 'other-resume':
                set_time_limit(0);
                $response = new StreamedResponse(function () {
                    // Open output stream
                    $handle = fopen('php://output', 'w');
                    // Add CSV headers
                    fputcsv($handle, $this->generateHeaders('resume'));
                    $to = \Carbon\Carbon::now()->addDays(60)->format('Y-m-d');
                    LeasingAgreement::
                        has('insurances', '>', 0)
                        ->select('leasing_agreements.*', DB::raw('MAX(lai.date_to) as max_date'))
                        ->join('leasing_agreement_insurances as lai', function ($join) use ($to) {
                            $join->on('leasing_agreements.id', '=', 'lai.leasing_agreement_id')->where('lai.date_to', '<', $to)->whereNull('lai.deleted_at');
                        })
                        ->where(function ($query) {
                            //czy ustawione jest filtrowanie wyszukiwaniem
                            $this->passingWheres($query);
                        })
                        ->whereNull('archive')
                        ->where(function ($query) {
                            $query->where("has_yacht", 1)->orWhere('if_foreign', 1);
                        })
                        ->whereHas('insurances', function ($query) use ($to) {
                            $query->where('date_to', '<', $to);
                        })
                        ->whereDoesntHave('insurances', function ($query) use ($to) {
                            $query->where('date_to', '>', $to);
                        })
                        ->with(['insurances' => function ($query) use ($to) {
                            $query->orderBy('date_to', 'desc');
                        }, 'insurances.insuranceCompany'])
                        ->groupBy('leasing_agreements.id')
                        ->orderBy('max_date', 'asc')
                        ->chunk(250, function ($agreements) use (&$handle) {

                            foreach ($agreements as $leasingAgreement) {
                                $row = [];
                                $row[] = ($leasingAgreement->nr_contract);
                                $row[] = (($leasingAgreement->client) ? $leasingAgreement->client->name : '--- błąd importu leasingobiorcy ---');
                                if (count($leasingAgreement->insurances) > 0) {
                                    $row[] = ($leasingAgreement->insurances->first()->insuranceCompany) ? $leasingAgreement->insurances->first()->insuranceCompany->name : '---';
                                    $row[] = ($leasingAgreement->insurances->first()->date_from);
                                    $row[] = ($leasingAgreement->insurances->first()->date_to);
                                } else {
                                    $row[] = 'brak przypisanych polis';
                                    $row[] = '---';
                                    $row[] = '---';
                                }
                                $row[] = ($leasingAgreement->loan_net_value);
                                $row[] = ($leasingAgreement->nr_agreement);
                                fputcsv($handle, $row);
                            }
                        });

                    // Close the output stream
                    fclose($handle);
                }, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="obce-wznowienia.csv"',
                ]);

                return $response;
                break;
            default:
                return Redirect::back();
                break;
        }
    }

    public function getGenerateHestiaCertificate($policy_id)
    {
        $policy = LeasingAgreementInsurance::with('leasingAgreement.client')->find($policy_id);

        return View::make('insurances.manage.dialog.certificate-hestia', compact('policy'));
    }

    public function getGenerateHestiaCertificateNoClient($policy_id)
    {
        $policy = LeasingAgreementInsurance::with('leasingAgreement.client')->find($policy_id);

        return View::make('insurances.manage.dialog.certificate-hestia-no-client', compact('policy'));
    }

    private function passingWheres(&$query, $parameters = null)
    {
        if (is_null($parameters)) {
            $parameters = Input::all();
        }

        if (isset($parameters['term'])) {
            $query->where(function ($query2) use ($parameters) {
                if (isset($parameters['nr_contract'])) {
                    $query2->orWhere('nr_contract', 'like', $parameters['term']);
                }
                if (isset($parameters['client_name'])) {
                    $query2->orWhereHas('client', function ($query3) use ($parameters) {
                        $query3->where('name', 'like', '%' . $parameters['term'] . '%');
                    });
                }
                if (isset($parameters['client_NIP'])) {
                    $query2->orWhereHas('client', function ($query3) use ($parameters) {
                        $query3->where('NIP', 'like', '%' . $parameters['term'] . '%');
                    });
                }

                if (isset($parameters['object_name'])) {
                    $query2->orWhereHas('objects', function ($query) use ($parameters) {
                        $query->where('name', 'like', $parameters['term'] . '%');
                    });
                }
                if (isset($parameters['policy_nb'])) {
                    $query2->orWhereHas('insurances', function ($query) use ($parameters) {
                        $query->where('insurance_number', 'like', '%' . $parameters['term'] . '%');
                    });
                }
            });
        }
        if (isset($parameters['warnings'])) {
            $query->where('detect_problem', '=', '1');
        }
        if (isset($parameters['yachts']) || Session::get('search.yachts_filter', '0') != 0) {
            $query->where('has_yacht', '=', '1');
        }

        if (isset($parameters['foreign_policy']) || Session::get('search.foreign_policy', '0') != 0) {
            $query->whereHas('insurances', function ($query) {
                $query->where('if_foreign_policy', '=', '1');
            });
        }

        if (Session::get('search.insurance_company_filter', '') != '') {
            $query->where(function ($query) {
                $query->whereHas('insurances', function ($query) {
                    $query->whereActive(1)->whereHas('insuranceCompany', function ($query) {
                        $query->where('name', 'like', '%' . Session::get('search.insurance_company_filter') . '%');
                    });
                })->orWhere('import_insurance_company', 'like', '%' . Session::get('search.insurance_company_filter') . '%');
            });
        }

        return $query;
    }

    private function generateHeaders($step)
    {
        switch ($step) {
            case 'index':
                return array(
                    'nr umowy',
                    'nr zgłoszenia',
                    'leasingobiorca',
                    'Ubezpieczyciel',
                    'wprowadzający',
                    'data zgłoszenia',
                );
                break;
            case ($step == 'inprogress' || $step == 'resume' || $step == 'resume-outdated'):
                return array(
                    'nr umowy',
                    'leasingobiorca',
                    'TU',
                    'polisa od',
                    'polisa do',
                    'SU [netto]',
                    'nr zgłoszenia',
                    'nr polisy',
                    'nr zgłoszenia polisy',
                );
                break;
            case 'future-resume':
                return array(
                    'nr umowy',
                    'leasingobiorca',
                    'TU',
                    'polisa od',
                    'polisa do',
                    'SU [netto]',
                    'nr zgłoszenia',
                );
                break;
            case ($step == 'archive'):
                return array(
                    'nr umowy',
                    'leasingobiorca',
                    'TU',
                    'polisa od',
                    'polisa do',
                    'data przeniesienia do archwium',
                    'SU [netto]',
                    'nr zgłoszenia',
                );
                break;
            case ($step == 'withdraw'):
                return array(
                    'nr umowy',
                    'nr zgloszenia',
                    'leasingobiorca',
                    'wprowadzający',
                    'data zgłoszenia',
                    'data przeniesienia',
                    'przyczyna przeniesienia',
                );
                break;
            case ($step == 'other'):
                return array(
                    'nr umowy',
                    'nr zgloszenia',
                    'leasingobiorca',
                    'wprowadzający',
                    'data zgłoszenia',
                    'nr polisy',
                    'nr zgłoszenia polisy',
                );
                break;
        }
    }
}
