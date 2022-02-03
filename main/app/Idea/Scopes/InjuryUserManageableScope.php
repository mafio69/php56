<?php
namespace Idea\Scopes;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ScopeInterface;
use Session;

class InjuryUserManageableScope implements ScopeInterface
{

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return void
     */
    public function apply(Builder $builder)
    {
        if(Auth::user()) {
            if(Auth::user()->without_restrictions) {
            }elseif (Auth::user()->is_external && count(Session::get('owners', [])) == 0 && count(Session::get('companies', [])) == 0) {
                $builder->where('id', 0);
            } else{
                $builder->vehicleOwner(Session::get('owners', []));
            }

            if(Auth::user()->without_restrictions_vmanage) {
            }elseif (Auth::user()->is_external && count(Session::get('companies', [])) == 0 && count(Session::get('owners', [])) == 0) {
                $builder->where('id', 0);
            } else{
                $builder->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                    ->orWhere(function($query){
                        $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                            $query->whereIn('vmanage_company_id', Session::get('companies', []));
                        });
                    });
                });
            }
        }
    }

    /**
     * Remove the scope from the given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return void
     */
    public function remove(Builder $builder)
    {
        // TODO: Implement remove() method.
    }
}