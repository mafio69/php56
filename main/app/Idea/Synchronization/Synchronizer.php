<?php

namespace Idea\Synchronization;


use Config;
use MobileInjury;
use MobileInjuryDamage;
use MobileInjuryFile;

class Synchronizer
{
    private $curl_base_url = 'https://ideaw.activemotors.pl/';

    /**
     * Synchronizer constructor.
     */
    public function __construct()
    {
        \DB::disableQueryLog();
        \Debugbar::disable();
        \Session::set('avoid_query_logging', 1);
    }


    public function companies()
    {
        $companiesToInsert = [];
        $companiesToUpdate = [];

        $companiesNewDb = \Company::where('new', 1)->with('groups')->get();
        foreach($companiesNewDb as $company)
        {
            $company->new = 0;
            $company->save();
            $companiesToInsert[] = $company->toArray();
        }

        $companiesDirtyDb = \Company::where('dirty', 1)->with('groups')->get();
        foreach ($companiesDirtyDb as $company)
        {
            $company->dirty = 0;
            $company->save();
            $companiesToUpdate[] = $company->toArray();
        }

        $curl = new Curl($this->curl_base_url.'import/companies', array(
            CURLOPT_POSTFIELDS => json_encode(array(
                'companiesToInsert' => $companiesToInsert,
                'companiesToUpdate' => $companiesToUpdate
            )),
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_HEADER => 1
        ));

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('Error in curl companies synchronize',
                            ['error' => $ex->getMessage(), 'code' => $ex->getCode()]
                        );
        }
    }

    public function branches()
    {
        $branchesToInsert = [];
        $branchesToUpdate = [];

        $branchesNewDb = \Branch::where('new', 1)->with('typevehicles', 'brands', 'typegarages')->get();
        foreach($branchesNewDb as $branch)
        {
            $branch->new = 0;
            $branch->save();
            $branchesToInsert[] = $branch->toArray();
        }

        $branchesDirtyDb = \Branch::where('dirty', 1)->with('typevehicles', 'brands', 'typegarages')->get();
        foreach ($branchesDirtyDb as $branch)
        {
            $branch->dirty = 0;
            $branch->save();
            $branchesToUpdate[] = $branch->toArray();
        }

        $curl = new Curl($this->curl_base_url.'import/branches', array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode(array(
                'branchesToInsert' => $branchesToInsert,
                'branchesToUpdate' => $branchesToUpdate
            )),
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_HEADER => 1
        ));

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('Error in curl branches synchronize',
                ['error' => $ex->getMessage(), 'code' => $ex->getCode()]
            );
        }
    }

    public function adverts()
    {
        $advertsToInsert = [];
        $advertsToUpdate = [];

        $advertsNewDb = \Adverts::where('new', 1)->get();
        foreach($advertsNewDb as $advert)
        {
            $advert->new = 0;
            $advert->save();
            $advertsToInsert[] = $advert->toArray();
        }

        $advertsDirtyDb = \Adverts::where('dirty', 1)->get();
        foreach ($advertsDirtyDb as $advert)
        {
            $advert->dirty = 0;
            $advert->save();
            $advertsToUpdate[] = $advert->toArray();
        }

        $curl = new Curl($this->curl_base_url.'import/adverts', array(
            CURLOPT_POSTFIELDS => array(
                'advertsToInsert' => $advertsToInsert,
                'advertsToUpdate' => $advertsToUpdate
            ),
            CURLOPT_POST => 1
        ));

        try {
            return $curl->getResponse();
        } catch (\RuntimeException $ex) {
            \Log::error('Error in curl adverts synchronize',
                ['error' => $ex->getMessage(), 'code' => $ex->getCode()]
            );
        }
    }

    public function injuries()
    {
        $curl = new Curl($this->curl_base_url.'import/injuries', array(
            CURLOPT_POST => 1
        ));
        try {
            $response = json_decode( $curl->getResponse() , true);
            $injuries = $response['injuries'];
            return $this->importInjuries($injuries);
        } catch (\RuntimeException $ex) {
            \Log::error('Error in curl adverts synchronize',
                ['error' => $ex->getMessage(), 'code' => $ex->getCode()]
            );
        }
    }

    public function test()
    {
        $curl = new Curl($this->curl_base_url.'import/test-injury', array(
            CURLOPT_POST => 1
        ));
        try {
            $response = json_decode( $curl->getResponse() , true);
            $injury = $response['injury'];

            foreach($injury['files'] as $file)
            {
                $path       = '/temp/full/';
                $path_min       = '/temp/min/';
                $path_thumb       = '/temp/thumb/';

                $images = $injury['images'][$file['id']];

                $this->base64_to_jpeg($images['image_thumb'], Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_thumb. $file['file']);
            }
            \Log::info($injury);
        } catch (\RuntimeException $ex) {
            \Log::error('Error in curl adverts synchronize',
                ['error' => $ex->getMessage(), 'code' => $ex->getCode()]
            );
        }
    }

    private function importInjuries($injuries)
    {
        $imported = 0;
        $path       = '/mobile/images/full/';
        $path_min       = '/mobile/images/min/';
        $path_thumb       = '/mobile/images/thumb/';

        foreach($injuries as $injury)
        {
            $mobileInjury = MobileInjury::create($injury);

            foreach($injury['damages'] as $damage)
            {
                MobileInjuryDamage::create(array(
                    'mobile_injury_id'      => $mobileInjury->id,
                    'mobile_damage_type_id' => $damage['mobile_damage_type_id']
                ));
            }

            foreach($injury['files'] as $file)
            {
                MobileInjuryFile::create(array(
                    'mobile_injury_id' => $mobileInjury->id,
                    'file'		=> $file['file'],
                ));

                $images = $injury['images'][$file['id']];
                $this->base64_to_jpeg($images['image_full'], Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path. $file['file']);
                $this->base64_to_jpeg($images['image_min'], Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_min. $file['file']);
                $this->base64_to_jpeg($images['image_thumb'], Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_thumb. $file['file']);
            }

            $group_name = '';
            if( ($mobileInjury->source == 0 || $mobileInjury->source == 3)  && $mobileInjury->injuries_type()->first()) {
                $group_name = $mobileInjury->injuries_type()->first()->name;
            }else {
                if ($mobileInjury->injuries_type == 2)
                    $group_name = 'komunikacyjna OC';
                elseif($mobileInjury->injuries_type == 1)
                    $group_name = 'komunikacyjna AC';
                elseif($mobileInjury->injuries_type == 3)
                    $group_name = 'komunikacyjna kradzież';
                elseif($mobileInjury->injuries_type == 4)
                    $group_name = 'majątkowa';
                elseif($mobileInjury->injuries_type == 5)
                    $group_name = 'majątkowa kradzież';
                elseif($mobileInjury->injuries_type == 6)
                    $group_name = 'komunikacyjna AC - Regres';
            }

            if (strpos($group_name, 'kradzież') !== false) {
                $task_group_id = 3;
            }else{
                $task_group_id = 1;
            }

            $task = \Task::create([
                'task_source_id' => 2, //druk online
                'from_email' => $mobileInjury->notifier_email,
                'from_name' => $mobileInjury->notifier_name.' '.$mobileInjury->notifier_surname,
                'subject' => $mobileInjury->nr_contract.' # '.$mobileInjury->registration,
                'content' => $mobileInjury->description(),
                'task_group_id' => $task_group_id,
                'task_date' => $mobileInjury->created_at
            ]);

            $mobileInjury->tasks()->save($task);

            if($mobileInjury->source == 1)
                $template = 'mobile.info_template_web';
            else
                $template = 'mobile.info_template_phone';
            $html = \View::make($template, ['injury' => $mobileInjury]);
            $name= str_random(32).'.pdf';

            \PDF::loadHTML($html)->setPaper('a4')->setOrientation('portrait')->setWarnings(false)->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$name);

            $task->files()->create([
                'filename' => $name,
                'original_filename' => 'zgłoszenie.pdf',
                'mime' => 'application/pdf'
            ]);

            \Idea\Tasker\Tasker::assign($task);

            $imported++;
        }

        return $imported;
    }

    private function base64_to_jpeg($base64_string, $output_file) {
        $ifp = fopen($output_file, "wb");
        fwrite($ifp, base64_decode($base64_string) );
        fclose($ifp);
    }
}