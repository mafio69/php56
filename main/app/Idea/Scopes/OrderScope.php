<?php


namespace Idea\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ScopeInterface;

class OrderScope implements ScopeInterface
{

    public function apply(Builder $builder)
    {
        $builder->orderBy('ord');
    }

    public function remove(Builder $builder)
    {
        // TODO: Implement remove() method.
    }
}