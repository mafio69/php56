<?php

class IdeaOffices extends Eloquent
{
    protected $table = 'idea_offices';
    protected $guarded = array();

    protected $fillable = ['name', 'city', 'post', 'street', 'phone', 'active'];
}