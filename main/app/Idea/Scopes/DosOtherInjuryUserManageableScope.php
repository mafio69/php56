<?php


namespace Idea\Scopes;


use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ScopeInterface;
use Session;

class DosOtherInjuryUserManageableScope implements ScopeInterface
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
            }elseif (Auth::user()->is_external && count(Session::get('owners', [])) == 0) {
                $builder->where('id', 0);
            } else{
                $builder->whereHas('object', function($query){
                    $query->whereIn('owner_id', Session::get('owners', []));
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