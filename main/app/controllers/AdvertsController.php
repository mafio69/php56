<?php

class AdvertsController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:reklamy_aplikacji_mobilnej#wejscie');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {

        $adverts = Adverts::whereActive(0)->get();

        $advertsA = array();
        foreach($adverts as $advert)
        {
            $advertsA[$advert->resolution_type_id][] = $advert;
        }

        $resolution_types = Resolution_types::whereActive(0)->get();

        return View::make('settings.adverts.index', compact('advertsA', 'resolution_types'));
    }


    public function create($resolution_type_id)
    {
        $resolution_type = Resolution_types::find($resolution_type_id);

        return View::make('settings.adverts.create', compact('resolution_type'));
    }

    public function store($resolution_type_id)
    {

        $input = Input::all();
        $rules = array(
            'file' => 'image',
        );

        $validation = Validator::make($input, $rules);

        if ($validation->fails())
        {
            return Response::json(array('status' => 'error', 'description' => 'przesłany plik nie jest zdjęciem'));
        }

        $randomKey  = sha1( time() . microtime() );

        $extension  = Input::file('file')->getClientOriginalExtension();

        $filename   = $randomKey.'.'.$extension;

        $path       = '/mobile/adverts/full';
        $path_thumb = '/mobile/adverts/thumb';
        $path_prepared   = '/mobile/adverts/prepared';

        // Move the file and determine if it was succesful or not
        $upload_success = Input::file('file')->move( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path , $filename );


        if( $upload_success ) {

            $img_container = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$filename);

            $resolution = Resolution_types::find($resolution_type_id);

            $ratio = round( $resolution->x_ax/$resolution->y_ax, 2);
            $img_ratio = round( $img_container->width / $img_container->height , 2);

            if($img_ratio == $ratio)
            {
                $image =Adverts::create(array(
                    'resolution_type_id' => $resolution_type_id,
                    'file'		=> $filename,
                ));

                $img_container->widen($resolution->x_ax)->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_prepared.'/'.$filename);

                Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$filename)->widen(150)
                        ->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_thumb.'/'.$filename);

                return Response::json('success', 200);
            }else{
                $image =Adverts::create(array(
                    'resolution_type_id' => $resolution_type_id,
                    'file'		=> $filename,
                    'active'    => 5
                ));

                $path_cut = '/mobile/adverts/cut';
                $img_container->widen(400)->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_cut.'/'.$filename);

                $html = '
                        <div class="panel panel-default pull-left adverts_panel">
                            <div class="panel-body">
                                <div class="media">
                                    <div class="bootstrap-modal-cropper">
                                        <input type="hidden" name="dataX['.$image->id.']" id="dataX_'.$image->id.'" class="dataX"/>
                                        <input type="hidden" name="dataY['.$image->id.']" id="dataY_'.$image->id.'" class="dataY"/>
                                        <input type="hidden" name="dataHeight['.$image->id.']" id="dataHeight_'.$image->id.'" class="dataHeight"/>
                                        <input type="hidden" name="dataWidth['.$image->id.']" id="dataWidth_'.$image->id.'" class="dataWidth"/>
                                        <input type="hidden" name="picturesCut[]" value="'.$image->id.'"/>
                                        '.HTML::image('/file/uploads/mobile_adverts/'.$filename.'/cut/', NULL, array('class' => 'media-object', 'idImg' => $image->id )).'
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer overflow">
                                <button class="btn btn-danger btn-sm pull-right remove-panel">Usuń zdjęcie</button>
                            </div>
                        </div>
                        ';

                return Response::json(array('error' => 'błędne proporcje pliku', 'status' => 1, 'file' => $filename, 'file_id' => $image->id, 'ratio' => $ratio, 'content' => $html), 400);
            }


        } else {
            return Response::json('Wystąpił błąd w trakcie dodawania pliku. Skontaktuj się z administratorem.', 400);
        }
    }

    public function cut()
    {
        foreach(Input::get('picturesCut') as $picture)
        {
            $img = Adverts::find($picture);

            $path           = '/mobile/adverts/full';
            $path_thumb     = '/mobile/adverts/thumb';
            $path_prepared  = '/mobile/adverts/prepared';
            $path_cut       = '/mobile/adverts/cut';

            $full_img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$img->file);
            $full_width = $full_img->width;
            $full_height = $full_img->height;

            $cut_img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_cut.'/'.$img->file);
            $cut_width = $cut_img->width;
            $cut_height = $cut_img->height;

            $ratio = $full_width/$cut_width;

            $dataX = Input::get('dataX')[$picture];
            $dataY = Input::get('dataY')[$picture];
            $dataWidth = Input::get('dataWidth')[$picture];
            $dataHeight = Input::get('dataHeight')[$picture];

            $afterCutX = round($dataX*$ratio);
            $afterCutY = round($dataY*$ratio);
            $afterCutWidth = round($dataWidth*$ratio);
            $afterCutHeight = round($dataHeight*$ratio);

            $thumb = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$img->file)->crop($afterCutWidth, $afterCutHeight, $afterCutX, $afterCutY);
            $thumb->widen(150)->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_thumb.'/'.$img->file);

            $prepared = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$img->file)->crop($afterCutWidth, $afterCutHeight, $afterCutX, $afterCutY);
            $prepared->widen(Input::get('resolution_x'))->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_prepared.'/'.$img->file);

            $img->active = 0;
            $img->save();
        }

        return Redirect::route('settings.adverts');
    }

    public function getDelete($id)
    {
        return View::make('settings.adverts.delete', compact('id'));
    }

    public function delete($id)
    {
        $file = Adverts::find($id);
        $file -> active = 9;
        $file -> save();

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getEdit($id)
    {
        $advert = Adverts::find($id);
        return View::make('settings.adverts.edit', compact('id', 'advert'));
    }

    public function update($id)
    {
        $advert = Adverts::find($id);
        $advert ->url = Input::get('url');
        $advert ->save();

        $result['code'] = 0;
        return json_encode($result);
    }

}
