<?php

namespace Idea\VoivodeshipMatcher;


class SingleMatching
{
    private $matcher;
    private $voivodeships;

    /**
     * GroupMatching constructor.
     */
    public function __construct()
    {
        $this->matcher = new VoivodeshipMatcher();
        $this->voivodeships = \Voivodeship::lists('id', 'name');
    }

    public function match($code)
    {
        $code = trim($code);
        $post_code = \PostCode::where('name', $code)->first();

        if($post_code){
            return $post_code->voivodeship_id;
        }

        $match = $this->matcher->match($code);
        if(count($match) > 0)
        {
            $voivodeship_id = $this->matchVoivodeship($match[0]);

            return $voivodeship_id;
        }

        return null;
    }

    private function matchVoivodeship($match)
    {
        $voivodeship_name = $match['wojewodztwo'];

        if(!isset($this->voivodeships[$voivodeship_name]))
            return null;

        $voivodeship_id = $this->voivodeships[$voivodeship_name];

        \PostCode::create(['name' => $match['kod'], 'voivodeship_id' => $voivodeship_id]);

        return $voivodeship_id;
    }
}