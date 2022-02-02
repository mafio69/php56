<?php

class InjuriesUnprocessedController extends BaseController {


    public function __construct(){
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

    public function generate($id)
    {
        $injury = MobileInjury::find($id);

        if($injury->source == 1)
            $template = 'mobile.info_template_web';
        else
            $template = 'mobile.info_template_phone';

        /*
         * aby zdebugować html dokumentu odkomentować linijkę poniżej
         *
         */
        // return View::make($template, compact('injury'));

        $html = View::make($template, compact('injury'));


        $pdf = PDF::loadHTML($html)->setPaper('a4')->setOrientation('portrait')->setWarnings(false);

        return $pdf->stream();

    }


    public function downloadUnprocessedImages($injury_id)
    {
        $injury = MobileInjury::find($injury_id);
        $photos = MobileInjuryFile::whereMobile_injury_id($injury_id)->get();

        $zip = new ZipArchive();
        $filename ="zdjecia-".str_replace('/', '_', $injury->nr_contract.'/'.$injury->registration).".zip";

        if ( !file_exists(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/images') )
        {
            mkdir(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/images');
        }

        if(file_exists( Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/images/' .$filename ) ) {
            unlink (Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/images/' .$filename );
        }
        if ($zip->open(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/images/' .$filename , ZIPARCHIVE::CREATE) != TRUE) {
            throw new Exception();
        }
        foreach($photos as $k => $photo) {
            $zip->addFile(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/mobile/images/full/' .$photo->file, 'zdjecie-'.$k.'.jpg');
        }

        $zip->close();

        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/images/' .$filename);
        // remove zip file is exists in temp path
        unlink(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/images/' .$filename);
    }

}