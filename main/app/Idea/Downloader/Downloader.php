<?php
/**
 * Created by PhpStorm.
 * User: przemek
 * Date: 30.12.14
 * Time: 15:34
 */

namespace Idea\Downloader;


use Config;
use Event;
use Response;
use Session;
use File;

class Downloader {


    private $path;
    private $download_filename;

    function __construct($path, $download_filename)
    {
        $this->path = $path;
        $this->download_filename = $download_filename;
    }

    public function download()
    {
        ob_start();

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        // Prepare the headers
        $headers = array(
            'Content-Description' => 'File Transfer',
            'Content-Type' => finfo_file($finfo, $this->path),
            'Content-Transfer-Encoding' => 'binary',
            'Expires' => 0,
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
            'Content-Length' => File::size($this->path),
            'Content-Disposition' => 'inline; filename="' . $this->download_filename . '"'
        );
        finfo_close($finfo);

        $response = new \Symfony\Component\HttpFoundation\Response('', 200, $headers);

        // If there's a session we should save it now
        if (Config::get('session.driver') !== '') {
            Session::save();
        }

        // Below is from http://uk1.php.net/manual/en/function.fpassthru.php comments
        session_write_close();
        if (ob_get_contents()) ob_end_clean();
        $response->sendHeaders();
        if ($file = fopen($this->path, 'rb')) {
            while (!feof($file) and (connection_status() == 0)) {
                print(fread($file, 1024 * 8));
                flush();
            }
            fclose($file);
        }

        // Finish off, like Laravel would
        Event::fire('laravel.done', array($response));

        exit;
    }

    public function download_on_disk()
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $headers = array(
            'Content-Type' => finfo_file($finfo, $this->path),
        );

        finfo_close($finfo);

        $ext = pathinfo($this->path, PATHINFO_EXTENSION);

        return Response::download($this->path, $this->download_filename.'.'.$ext, $headers);
    }
}