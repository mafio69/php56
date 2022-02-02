<?php


namespace Idea\Tasker;


use Config;
use Swift_Events_SimpleEventDispatcher;
use Swift_Image;
use Swift_Mailer;
use Swift_Message;

class TaskMailer
{

    private $mailer;

    /**
     * @param mixed $mailer
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }
    private $html;

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param mixed $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }

    /**
     * @return mixed
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    public function decodePersonAddressData($data)
    {
        if ($data != '') $data = mb_decode_mimeheader($data);

        return $data;
    }

}