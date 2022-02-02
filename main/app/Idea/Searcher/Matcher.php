<?php

namespace Idea\Searcher;


use Auth;
use Injury;
use InjuryLetter;
use MobileInjury;
use Vehicles;
use VmanageVehicle;

class Matcher
{
    public function letters($vehicles)
    {
        foreach($vehicles as $k => $vehicle)
        {
            $vehicles[$k]['letters'] = $this->searchLetters($vehicle);
        }

        return $vehicles;
    }

    public function unprocessed($vehicles)
    {
        foreach ($vehicles as $k => $vehicle) {
            $vehicles[$k]['unprocessed'] = $this->searchUnprocessed($vehicle);
        }

        return $vehicles;
    }

    public function dosUnprocessed($objects)
    {
        foreach ($objects as $k => $object) {
            $objects[$k]['unprocessed'] = $this->searchDosUnprocessed($object);
        }

        return $objects;
    }

    public function injuries($vehicles)
    {
        foreach ($vehicles as $k => $vehicle) {
            $vehicles[$k]['injuries'] = $this->searchInjuries($vehicle);
        }
        return $vehicles;
    }

    public function dosInjuries($objects)
    {
        foreach ($objects as $k => $object) {
            $objects[$k]['injuries'] = $this->searchDosInjuries($object);
        }
        return $objects;
    }

    public function searchLetters($vehicle)
    {
        return  InjuryLetter::whereNull('injury_file_id')->where(function($query) use ($vehicle){
            if($vehicle['nr_contract'] != '') {
                $query->orWhere(function ($subquery) use ($vehicle) {
                    $subquery->whereNotNull('nr_contract')->where('nr_contract',  'like', $vehicle['nr_contract']);
                });
            }
            if($vehicle['registration'] != '') {
                $query->orWhere(function ($subquery) use ($vehicle) {
                    $subquery->whereNotNull('registration')->where('registration', 'like', $vehicle['registration']);
                });
            }
        })->get();
    }

    public function searchUnprocessed($vehicle)
    {
        return MobileInjury::where('active', '=', '0')
            ->where(function($query) use($vehicle)
            {
                if($vehicle['registration'] != '') {
                    $query -> where('registration', 'like', $vehicle['registration']);
                }

                if($vehicle['nr_contract'] != '') {
                    $query -> orWhere('nr_contract', 'like', $vehicle['nr_contract']);
                }

                $query->where(function($query){
                    $query->where('source', 0);
                    $query->orWhereIn('injuries_type', [2,1,3]);
                });
                $query->where('source', '!=', 3);
            })
            ->with('files', 'damages', 'injuries_type')->orderBy('created_at','desc')->get();
    }

    public function searchDosUnprocessed($vehicle)
    {
        return MobileInjury::where('active', '=', '0')
            ->where(function($query) use($vehicle)
            {
                $query -> where('nr_contract', 'like', $vehicle['nr_contract']);
                $query->whereNotIn('source', [0,3]);
                $query->whereIn('injuries_type', [4,5]);
            })
            ->with('files', 'damages', 'injuries_type')->orderBy('created_at','desc')->get();
    }

    public function searchInjuries($vehicle)
    {
        $vehiclesA = [];
        $vehiclesVmanageA = VmanageVehicle::where(function($query) use($vehicle){
            if($vehicle['registration'] != '') {
                $query->whereRegistration($vehicle['registration']);
            }
            if($vehicle['nr_contract'] != '') {
                $query->orWhere('nr_contract',  'like', $vehicle['nr_contract']);
            }
        })->where(function($query){
            $query->whereHas('company', function($query){
                $query->whereHas('guardians', function($query){
                    $query->where('users.id', Auth::user()->id);
                });
            });
        })->withTrashed()->lists('id');

        $vehiclesA = Vehicles::where(function ($query) use($vehicle){
            if($vehicle['registration'] != '') {
                $query->whereRegistration($vehicle['registration']);
            }
            if($vehicle['nr_contract'] != '') {
                $query->orWhere('nr_contract', 'like', $vehicle['nr_contract']);
            }
        })->lists('id');

        return Injury::where(function($query) use($vehiclesVmanageA, $vehiclesA){
            $query->where(function($query) use($vehiclesVmanageA){
                $query->where('vehicle_type', 'VmanageVehicle')->whereIn('vehicle_id', $vehiclesVmanageA);
            })->orWhere(function($query) use($vehiclesA){
                $query->where('vehicle_type', 'Vehicles')->whereIn('vehicle_id', $vehiclesA);
            });
        })->where('active', '=', 0)->with('getInfo', 'vehicle', 'injuries_type', 'user', 'chat', 'chat.messages', 'status')->get();
    }

    public function searchDosInjuries($object)
    {
        $objectsA = \Objects::where('nr_contract', 'like', $object['nr_contract'])
                            ->lists('id');

        return \DosOtherInjury::whereIn('object_id', $objectsA)
                ->where('active', '=', 0)
                    ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident', 'status')
                    ->orderBy('created_at','desc')
                    ->get();
    }
}
