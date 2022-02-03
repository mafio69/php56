<?php

namespace Idea\VoivodeshipMatcher;


class GroupMatching
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

    public function matchClientsRegistry($rows = 10)
    {
        $parsed = 0;
        $clients = \Clients::where('registry_invalid_post', 0)->where('registry_post', '!=', '')->whereNull('registry_voivodeship_id')->take($rows)->get();

        foreach ($clients as $client)
        {
            $registry_post_code = trim($client->registry_post);
            $post_code = \PostCode::where('name', $registry_post_code)->first();

            if($post_code){
                $client->registry_voivodeship_id = $post_code->voivodeship_id;
                $client->save();
            }else{
                $match = $this->matcher->match($registry_post_code);
                if(count($match) > 0)
                {
                    $registry_voivodeship_id = $this->matchVoivodeship($match[0]);

                    $client->registry_voivodeship_id = $registry_voivodeship_id;
                    $client->save();

                }else{
                    $client->registry_invalid_post = 1;
                    $client->save();
                }
            }
            $parsed++;
        }

        return $parsed;
    }

    public function matchClientsCorrespond($rows = 10)
    {
        $parsed = 0;
        $clients = \Clients::where('correspond_invalid_post', 0)->where('correspond_post', '!=', '')->whereNull('correspond_voivodeship_id')->take($rows)->get();

        foreach ($clients as $client)
        {
            $correspond_post_code = trim($client->correspond_post);
            $post_code = \PostCode::where('name', $correspond_post_code)->first();

            if($post_code){
                $client->correspond_voivodeship_id = $post_code->voivodeship_id;
                $client->save();
            }else{
                $match = $this->matcher->match($correspond_post_code);
                if(count($match) > 0)
                {
                    $correspond_voivodeship_id = $this->matchVoivodeship($match[0]);

                    $client->correspond_voivodeship_id = $correspond_voivodeship_id;
                    $client->save();

                }else{
                    $client->correspond_invalid_post = 1;
                    $client->save();
                }
            }
            $parsed++;
        }

        return $parsed;
    }

    public function matchBranch($rows = 10)
    {
        $parsed = 0;
        $branches = \Branch::where('invalid_post', 0)->where('code', '!=', '')->whereNull('voivodeship_id')->take($rows)->get();

        foreach ($branches as $branch)
        {
            $code = trim($branch->code);
            $post_code = \PostCode::where('name', $code)->first();

            if($post_code){
                $branch->voivodeship_id = $post_code->voivodeship_id;
                $branch->save();
            }else{
                $match = $this->matcher->match($code);
                if(count($match) > 0)
                {
                    $voivodeship_id = $this->matchVoivodeship($match[0]);

                    $branch->voivodeship_id = $voivodeship_id;
                    $branch->save();

                }else{
                    $branch->invalid_post = 1;
                    $branch->save();
                }
            }
            $parsed++;
        }

        return $parsed;
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