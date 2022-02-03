<?php


namespace Idea\Compressor;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Intervention\Image\ImageManagerStatic as Image;

class Compressor
{
    private $filepath;
    private $filename;
    /**
     * @var int
     */
    private $if_compressed;

    /**
     * Compressor constructor.
     */
    public function __construct($filepath, $filename, $if_compressed = 1)
    {
        $this->filepath = $filepath;
        $this->filename = $filename;
        $this->if_compressed = $if_compressed;
    }

    public function toPdf(){
        if($this->if_compressed == 0)   return $this->filename;

        $file_size = \File::size($this->filepath.$this->filename);
        if($file_size <= 1048576)   return $this->filename;

//        $mimeType = \File::mimeType($this->filepath.$this->filename);
        $mimeType = mime_content_type($this->filepath.$this->filename);
        $output_filename =  time().md5($this->filename).'.pdf';

        try {
            if (in_array($mimeType, ['application/pdf'])) {
                $this->compressPdf( $output_filename,  $this->filename);
            } elseif (in_array($mimeType, ['image/gif', 'image/jpeg', 'image/png'])) {
                $this->convertImageToPdf($output_filename,   $this->filename);

                $this->filename = $output_filename;
                $output_filename =  time().md5($this->filename).'.pdf';

                $this->compressPdf($output_filename,  $this->filename);
            }elseif(in_array($mimeType, ['image/x-ms-bmp', 'image/bmp', 'image/x-windows-bmp'])){
                Image::configure(array('driver' => 'imagick'));

                $tmp_filename = time().md5($this->filename).'.jpg';
                Image::make($this->filepath .  $this->filename)->orientate()->save($this->filepath .  $tmp_filename, 90);
                $this->deleteIfExists($this->filepath .  $this->filename , $this->filepath .  $tmp_filename);

                $this->convertImageToPdf($output_filename,   $tmp_filename);

                $this->filename = $output_filename;
                $output_filename =  time().md5($this->filename).'.pdf';

                $this->compressPdf($output_filename,  $this->filename);

            }else{
                $output_filename = $this->filename;
            }
        }catch (ProcessFailedException $e){
            \Log::error('process failed', [$e->getCode(), $e->getMessage(), $this->filepath . '/' . $this->filename]);
            $output_filename = $this->filename;
        }

        return $output_filename;
    }

    private function compressPdf($outputFilename, $inputFilename)
    {
        $command = 'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.3 -dPDFSETTINGS=/ebook -dNOPAUSE -dBATCH  -dQUIET -dDownsampleColorImages=true   -dDownsampleGrayImages=true   -dDownsampleMonoImages=true   -dColorImageResolution=235   -dGrayImageResolution=235   -dMonoImageResolution=235   -dColorImageDownsampleThreshold=1.0 -dGrayImageDownsampleThreshold=1.0   -dMonoImageDownsampleThreshold=1.0 -sOutputFile=' . $this->filepath .$outputFilename . ' ' . $this->filepath .$inputFilename;
//        $command = 'gswin64c -sDEVICE=pdfwrite -dCompatibilityLevel=1.3 -dPDFSETTINGS=/ebook -dNOPAUSE -dBATCH  -dQUIET -dDownsampleColorImages=true   -dDownsampleGrayImages=true   -dDownsampleMonoImages=true   -dColorImageResolution=235   -dGrayImageResolution=235   -dMonoImageResolution=235   -dColorImageDownsampleThreshold=1.0 -dGrayImageDownsampleThreshold=1.0   -dMonoImageDownsampleThreshold=1.0 -sOutputFile=' . $this->filepath .$outputFilename . ' ' . $this->filepath .$inputFilename;
        $process = new Process($command, null, null, null, 360);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->deleteIfExists($this->filepath .  $inputFilename , $this->filepath .  $inputFilename);
    }

    private function convertImageToPdf($outputFilename, $inputFilename)
    {
        Image::make($this->filepath .  $inputFilename)->widen(1500)->orientate()->save($this->filepath .  $inputFilename);

        $command = 'convert '.$this->filepath .$inputFilename.' '.$this->filepath .$outputFilename;

        $process = new Process($command, null, null, null, 360);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->deleteIfExists($this->filepath .  $inputFilename , $this->filepath .  $inputFilename);
    }

    private function deleteIfExists($deleting_file, $checking_file)
    {
        if (\File::exists($checking_file)) {
            \File::delete($deleting_file);
        }
    }
}