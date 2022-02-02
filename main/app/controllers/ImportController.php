<?php

class ImportController extends \BaseController {

    public function companies()
    {
        $companiesToInsert = json_decode( Input::get('companiesToInsert'), true );
        if(count($companiesToInsert) > 0)
        {
            foreach ($companiesToInsert as $companyToInsert)
            {
                $company = Company::create($companyToInsert);
                foreach ($companiesToInsert['groups'] as $group)
                {
                    $company->groups()->attach($group['id']);
                }
            }
        }

        $companiesToUpdate = json_decode( Input::get('companiesToUpdate'), true );
        if(count($companiesToUpdate) > 0)
        {
            foreach ($companiesToUpdate as $companyToUpdate)
            {
                $company = Company::find($companyToUpdate['id']);
                $company->update($companyToUpdate);

                $company->groups()->detach();
                foreach ($companyToUpdate['groups'] as $group)
                {
                    $company->groups()->attach($group['id']);
                }
            }
        }

        return Response::json(['status' => 'success']);
    }

    public function branches()
    {
        $branchesToInsert = json_decode(Input::get('branchesToInsert'), true);

        Log::info('branchesToInsert', $branchesToInsert);

        if (count($branchesToInsert) > 0) {
            foreach ($branchesToInsert as $branchToInsert) {
                $branch = Branch::create($branchToInsert);
                foreach ($branchToInsert['typevehicles'] as $typevehicle) {
                    DB::table('branches_typevehicles')->insert(
                        array('branch_id' => $branch->id, 'typevehicles_id' => $typevehicle['typevehicles_id'], 'value' => $typevehicle['value'])
                    );
                }
                foreach ($branchToInsert['typegarages'] as $typegarages)
                {
                    DB::table('branches_typegarages')->insert(
                        array('branch_id' => $branch->id, 'typegarages_id' => $typegarages['typegarages_id'])
                    );
                }
                foreach ($branchToInsert['brands'] as $brand) {
                    $branch->brands()->attach($brand['id']);
                }
            }
        }

        $branchesToUpdate = json_decode(Input::get('branchesToUpdate'), true);

        Log::info('branchesToUpdate', $branchesToUpdate);

        if (count($branchesToUpdate) > 0) {
            foreach ($branchesToUpdate as $branchToUpdate) {
                $branch = Branch::find($branchToUpdate['id']);
                $branch->update($branchToUpdate);

                Branches_typevehicles::where('branch_id', $branch->id)->delete();
                foreach ($branchToUpdate['typevehicles'] as $typevehicle)
                {
                    DB::table('branches_typevehicles')->insert(
                        array('branch_id' => $branch->id, 'typevehicles_id' => $typevehicle['typevehicles_id'], 'value' => $typevehicle['value'])
                    );
                }

                Branches_typegarages::where('branch_id', $branch->id)->delete();
                foreach ($branchToUpdate['typegarages'] as $typegarages)
                {
                    DB::table('branches_typegarages')->insert(
                        array('branch_id' => $branch->id, 'typegarages_id' => $typegarages['typegarages_id'])
                    );
                }

                $branch->brands()->detach();
                foreach ($branchToUpdate['brands'] as $brand)
                {
                    $branch->brands()->attach($brand['id']);
                }
            }
        }

        return Response::json(['status' => 'success']);
    }

    public function adverts()
    {
        $advertsToInsert = Input::get('advertsToInsert');
        if(count($advertsToInsert) > 0)
        {
            foreach ($advertsToInsert as $advertToInsert)
            {
                Adverts::create($advertToInsert);
            }
        }

        $advertsToUpdate = Input::get('advertsToUpdate');
        if(count($advertsToUpdate) > 0)
        {
            foreach ($advertsToUpdate as $advertToUpdate)
            {
                $advert = Adverts::find($advertToUpdate['id']);
                $advert->update($advertToUpdate);
            }
        }

        return Response::json(['status' => 'success']);
    }

    public function injuries()
    {
        $path       = '/mobile/images/full';
        $path_min       = '/mobile/images/min';
        $path_thumb       = '/mobile/images/thumb';

        $injuriesToInsert = [];

        $injuriesNewDb = MobileInjury::where('active', '=', '0')->where('new', 1)->with('damages', 'files')->get();

        foreach($injuriesNewDb as $k => $injury)
        {
            $injury->new = 0;
            $injury->save();

            $injuriesToInsert[$k] = $injury->toArray();
            foreach($injury->files as $file)
            {
                $injuriesToInsert[$k]['images'][$file->id] = [
                    'file_id' => $file->id,
                    'image_full' => base64_encode( file_get_contents( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path.'/'.$file->file ) ),
                    'image_min' => base64_encode( file_get_contents( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_min.'/'.$file->file ) ),
                    'image_thumb' => base64_encode( file_get_contents( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_thumb.'/'.$file->file ) )
                ];
            }
        }

        return Response::json(['injuries' => $injuriesToInsert]);
    }

    public function testInjury()
    {
        $path       = '/mobile/images/full';
        $path_min       = '/mobile/images/min';
        $path_thumb       = '/mobile/images/thumb';

        $injury = MobileInjury::with('damages', 'files')->find(5357);
        $injuryToInsert = $injury->toArray();
        foreach($injury->files as $file)
        {
            $injuryToInsert['images'][$file->id] = [
                'file_id' => $file->id,
                'image_full' => base64_encode( file_get_contents( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path.'/'.$file->file ) ),
                'image_min' => base64_encode( file_get_contents( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_min.'/'.$file->file ) ),
                'image_thumb' => base64_encode( file_get_contents( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_thumb.'/'.$file->file ) )
            ];
        }

        return Response::json(['injury' => $injuryToInsert]);
    }
}