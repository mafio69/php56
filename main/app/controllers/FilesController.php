<?php

class FilesController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

    public function getDownloadPhotos($injury_id)
    {
        $injury = Injury::find($injury_id);
        $photos = [];
        foreach (Request::all() as $photo_id)
        {
            $photos[] = $photo_id;
        }

        $photos = InjuryFiles::whereIn('id', $photos)->get();

        $zip = new ZipArchive();
        $filename ="zdjecia-".str_replace('/', '_', $injury->case_nr).".zip";

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
            $zip->addFile(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/images/full/' .$photo->file, Config::get('definition.imageCategory')[$photo->category].'-'.$k.'.jpg');
        }

        $zip->close();

        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/images/' .$filename);
        // remove zip file is exists in temp path
        unlink(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/images/' .$filename);
    }
}
