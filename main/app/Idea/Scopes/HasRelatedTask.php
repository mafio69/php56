<?php


namespace Idea\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ScopeInterface;

class HasRelatedTask implements ScopeInterface
{
    public function apply(Builder $builder)
    {
        $builder->whereRaw("(select count(*) from tasks where task_instances.task_id = tasks.id and tasks.deleted_at is null) >= 1");
    }

    public function remove(Builder $builder)
    {
        // TODO: Implement remove() method.
    }
}