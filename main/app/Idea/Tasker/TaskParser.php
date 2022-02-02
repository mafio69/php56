<?php

namespace Idea\Tasker;

use Carbon\Carbon;
use Config;
use SplFileInfo;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use ZBateson\MailMimeParser\MailMimeParser;

class TaskParser
{
    private $from_name;
    private $from_address;
    private $to_name = [];
    private $to_address = [];
    private $cc_name = [];
    private $cc_address = [];

    private $content;
    private $attachments = [];
    private $message;

    /**
     * @return mixed
     */
    public function getFromName()
    {
        return $this->from_name;
    }

    /**
     * @return mixed
     */
    public function getFromAddress()
    {
        return $this->from_address;
    }

    /**
     * @return array
     */
    public function getCcName()
    {
        return $this->cc_name;
    }

    /**
     * @return array
     */
    public function getCcAddress()
    {
        return $this->cc_address;
    }

    /**
     * @return array
     */
    public function getToName()
    {
        return $this->to_name;
    }

    /**
     * @return array
     */
    public function getToAddress()
    {
        return $this->to_address;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param $path
     */
    public function __construct($path)
    {
        $mailParser = new MailMimeParser();
        $this->message = $mailParser->parse(fopen($path, 'r'), true);
        $this->processAddresses();
        $this->processBody();
        $this->processAttachments();
    }


    private function processAddresses()
    {
        $from = $this->message->getHeader('From');
        if(! $from) {
            \Log::error('missing from header ', [$this->message->getAllHeaders()]);
        }else {
            $this->from_address = $from->getEmail();
            $this->from_name = $from->getName() == 'From' ? $from->getEmail() : $from->getName();
        }

        $to = $this->message->getHeader('To');
        if(! $to){
            \Log::error('missing to header ', [$this->message->getAllHeaders()]);
        }else {
            foreach ($to->getAddresses() as $addr) {
                $this->to_name[] = $addr->getName();
                $this->to_address[] = $addr->getEmail();
            }
        }

        $cc = $this->message->getHeader('Cc');
        if(! $cc){
            \Log::error('missing cc header ', [$this->message->getAllHeaders()]);
        }else {
            foreach ($cc->getAddresses() as $addr) {
                $this->cc_name[] = $addr->getName();
                $this->cc_address[] = $addr->getEmail();
            }
        }
    }

    private function processBody()
    {
        $content = '';
        if($this->message->getHtmlPartCount() > 0){
            for($i = 0; $i < $this->message->getHtmlPartCount(); $i++){
                $content .= $this->message->getHtmlContent($i);
            }
        }else{
            for($i = 0; $i < $this->message->getTextPartCount(); $i++){
                $content .= $this->message->getTextContent($i);
            }
        }

        $this->content = $content;
    }

    private function processAttachments()
    {
        $atts = $this->message->getAllAttachmentParts();
        \Log::info('msg', [$this->message->getHeaderValue('Subject'), count($atts)]);
        foreach ($atts as $ind => $part) {


            if ($part->getContentId() != '' && strpos($this->content, '"cid:'.$part->getContentId().'"') !== false && $this->getBase64ImageSize(base64_encode( $part->getContent() )) < 1024) {
                $this->content = str_replace(
                    '"cid:'.$part->getContentId().'"',
                    '"'.$this->getEmbeddedData($part).'"',
                    $this->content
                );
            }else {
                $original_filename = $part->getFilename();

                $ext = (new SplFileInfo($original_filename))->getExtension();
                $filename = substr(md5(time() . 'xx' . rand(0, 9999)), 7, 16) . '.' . $ext;

                try {
                    $part->saveContent((Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/emails/") . $filename);

                    $this->attachments[] = [
                        'filename' => $filename,
                        'original_filename' => $original_filename,
                        'mime' => $part->getContentType(),
                    ];
                }catch (\Throwable $e){
                    \Log::info('invalid attachemnt', [$part->getFilename()]);
                }catch (\Exception $e){
                    \Log::info('invalid attachemnt', [$part->getFilename()]);
                }

            }
        }
    }

    public function getSubject()
    {
        return $this->message->getHeaderValue('Subject');
    }

    public function getDate()
    {
        if(! $this->message->getHeader('Date')) return null;
        return new Carbon( $this->message->getHeader('Date')->getValue() );
    }

    private function getEmbeddedData(\ZBateson\MailMimeParser\Message\IMessagePart $part)
    {
        $embeddedData = 'data:';
        $embeddedData .= $part->getContentType();
        $embeddedData .= ';'.$part->getContentTransferEncoding();
        $embeddedData .= ','.base64_encode( $part->getContent() );
        return $embeddedData;
    }

    function check_base64_image($data) {
        return @imagecreatefromstring(base64_decode($data));
    }

    public function getBase64ImageSize($base64Image){
        $size_in_bytes = (int) (strlen(rtrim($base64Image, '=')) * 3 / 4);
        $size_in_kb    = $size_in_bytes / 1024;

        return $size_in_kb;
    }
}