<?php

class VmanageImportController extends \BaseController
{
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

    public function getGetin()
    {
        $imports = VmanageImport::where('if_truck', 0)->orderBy('id', 'desc')->get();
        return View::make('vmanage.import.getin', compact('imports'));
    }

    public function getTrucks($filename = null, $original_filename = null)
    {
        $imports = VmanageImport::where('if_truck', 1)->orderBy('id', 'desc')->get();
        return View::make('vmanage.import.trucks', compact('imports', 'filename', 'original_filename'));
    }

    public function postUploadFile()
    {
        $file = Input::file('file');
        $extension = $file->getClientOriginalExtension();

        $filename = time().'.'.$extension;
        $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/imports/vmanage/";
        $file->move($destinationPath, $filename);

        return Response::json(['filename' => $filename]);
    }

    public function postUploadTruckFile()
    {
        $file = Input::file('file');
        $original_filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $filename = time().'.'.$extension;
        $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/imports/vmanage/";
        $file->move($destinationPath, $filename);

        return Redirect::to(url('vehicle-manage/import/trucks', [$filename, $original_filename]));
    }

    public function postUploadGetin()
    {
        $file = Input::file('file');
        $original_filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        if($extension != 'tsv'){
            Flash::error('Niepoprawny format pliku.');
            return Response::json(['status' => 'error']);
        }
        $filename = time().'.'.$extension;
        $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/imports/vmanage/";
        $file->move($destinationPath, $filename);

        $import = VmanageImport::create(
            [
                'user_id'   =>  Auth::user()->id,
                'filename'  =>  $filename,
                'original_filename' => $original_filename
            ]);

        Queue::push('Idea\Vmanage\Imports\QueueImportGetin', array('filename' => $filename, 'import_id' => $import->id));

        Flash::message('Trwa importowanie zestawnia...');
        return Response::json(['status' => 'ok']);
    }

    public function getDownload($import_id)
    {
        $import = VmanageImport::findOrFail($import_id);

        $filename = $import->filename;
        $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/imports/vmanage/";

        return Response::download($destinationPath.$filename);
    }

    public function postProceedFile()
    {
        $filename = Input::get('filename');
        $import = VmanageImport::create([
            'if_truck' => 1,
            'user_id'   =>  Auth::user()->id,
            'filename'  =>  $filename,
            'original_filename' => Input::get('original_filename'),
            'file_type' => Input::get('file_type')
        ]);
        Queue::push('Idea\Vmanage\Imports\QueueImportTrucks', array('filename' => $filename, 'import_id' => $import->id));

        return Redirect::to(url('vehicle-manage/import/trucks'));
    }

    public function postCheckParseStatus()
    {
        $if_truck = Input::get('if_truck', 0);
        $imports = VmanageImport::where('if_truck', $if_truck)->whereNotNull('parsed')->lists('parsed', 'id');
        return Response::json($imports);
    }

    public function getVipClients()
    {
        $imports = VipClientImport::with('vips')->orderBy('id', 'desc')->paginate(Session::get('search.pagin', '10'));
        return View::make('vmanage.import.vip_clients', compact('imports'));
    }

    public function postUploadVipClients()
    {
        $file = Input::file('file');
        $extension = $file->getClientOriginalExtension();
        if($extension != 'csv' && $extension != 'xls' && $extension != 'xlsx' ){
            Flash::error('Niepoprawny format pliku.');
            return Response::json(['status' => 'error']);
        }
        $filename = time().'.'.$extension;

        $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/imports/vip_clients/";
      /*  if(!file_exists($destinationPath)){
          mkdir($destinationPath);
        }*/
        $file->move($destinationPath, $filename);

        $import = VipClientImport::create(
            [
                'user_id'   =>  Auth::user()->id,
                'filename'  =>  $filename
            ]);

        $vip_clients_import = new \Idea\Vmanage\Imports\VipClientsImport($import);

        if($vip_clients_import->import()){
          Flash::message('Zaimportowano dane');
          return Response::json(['status' => 'ok']);
        }
        else{
          Flash::error('Wystąpił błąd');
          return Response::json(['status' => 'error']);
        }
    }

    public function getVipClientRegistrations($import_id)
    {
        $import = VipClientImport::find($import_id);
        $registrations = VipClient::where('vip_clients_import_id', $import_id)->paginate(Session::get('search.pagin', '10'));
        return View::make('vmanage.import.vip-client-registrations', compact('import', 'registrations'));
    }

    public function getRegistrations()
    {
        $registrations = VipClient::with('import', 'import.user')
            ->where(function($query){
                if(Request::has('registration'))
                {
                    $query->where('registration', 'like', '%'.Request::get('registration').'%');
                }
            })
            ->orderBy('registration')->paginate(Session::get('search.pagin', '10'));

        return View::make('vmanage.import.registrations', compact('registrations'));
    }

    public function getDetachRegistration($registration_id)
    {
        $registration = VipClient::find($registration_id);
        return View::make('vmanage.import.detach-registration', compact('registration'));
    }

    public function postDeleteRegistration($registration_id)
    {
        $registration = VipClient::find($registration_id);
        Log::info('registration deleted '.$registration->registration);

        $registration->delete();
        return json_encode(['code' => '0']);
    }
}
