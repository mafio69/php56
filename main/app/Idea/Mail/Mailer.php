<?php

namespace Idea\Mail;


use Config;
use PHPMailer;
use phpmailerException;

class Mailer
{
    private $mail;
    private $body;

    /**
     * Mailer constructor.
     */
    public function __construct($mailbox = null)
    {
        $this->mail = new PHPMailer(true);
        $this->mail->CharSet = "UTF-8";
        $this->mail->isSMTP();                                      // Set mailer to use SMTP
        if($mailbox){
            $this->mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
            $this->mail->Host = $mailbox->server;
            $this->mail->Port = 143;
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $mailbox->login;
            $this->mail->Password = \Crypt::decrypt($mailbox->password);

        }else {
            $this->mail->Host = Config::get('webconfig.MAIL_HOST');  // Specify main and backup server
            $this->mail->SMTPAuth = Config::get('webconfig.MAIL_SMTP_AUTH');                         // Enable SMTP authentication
            $this->mail->Username = Config::get('webconfig.MAIL_USERNAME');                            // SMTP username
            $this->mail->Password = Config::get('webconfig.MAIL_PASSWORD');                    // SMTP password
            $this->mail->SetFrom(Config::get('webconfig.MAIL_FROM'), Config::get('webconfig.MAIL_NAME'));
            $this->mail->SMTPSecure = Config::get('webconfig.MAIL_ENCRYPTION');                            // Enable TLS encryption, `ssl` also accepted
            $this->mail->Port = Config::get('webconfig.MAIL_PORT');
        }

    }

    public function setTimeout($value)
    {
        $this->mail->Timeout = $value;
    }

    public function debug($level)
    {
        $this->mail->SMTPDebug = $level;
    }

    public function setSubject($subject)
    {
        $this->mail->Subject = $subject;
    }

    public function setBody($body)
    {
        $this->body = $body;
        $this->mail->msgHTML($body);
    }

    public function setPureBody($body)
    {
        $this->body = $body;
        $this->mail->isHTML(true);
        $this->mail->Body = $body;
    }
    
    public function from($mailAddress, $mailName)
    {
        $this->mail->SetFrom($mailAddress, $mailName);
    }

    public function addAddress($mailAddress, $addressee = '')
    {
        $this->mail->addAddress($mailAddress, $addressee);
    }
    public function addAddresses($addresses)
    {
        foreach($addresses as $address) {
            $this->mail->addAddress($address);
        }
    }

    public function addCcAddress($mailAddress, $addressee = '')
    {
        $this->mail->addCC($mailAddress, $addressee);
    }

    public function addBccAddress($mailAddress, $addressee = '')
    {
        $this->mail->addBCC($mailAddress, $addressee);
    }

    public function addReplyTo($mailAddress, $name = '')
    {
        $this->mail->addReplyTo($mailAddress, $name);
    }

    public function send()
    {
        if(Config::get('webconfig.MAIL_DRIVER') == 'log'){
            \Log::info($this->body);
            return 'success';
        }
        if($this->mail->send())
            return 'success';


        return $this->mail->ErrorInfo;
    }

    public function addAttachment($filepath, $filename) {
       return  $this->mail->AddAttachment($filepath, $filename);
    }
    public function addStringAttachment($filepath, $filename) {
        $this->mail->AddStringAttachment($filepath, $filename);
    }
    public function addEmbeddedImage($filepath, $cid, $filename) {
        return $this->mail->addEmbeddedImage($filepath, $cid, $filename);
    }

    public function setFrom($address, $name = '')
    {
        $this->mail->SetFrom($address, $name);
    }

    public function getMailString()
    {
        $this->mail->preSend();
        return $this->mail->getSentMIMEMessage();
    }

    public function getEml()
    {
        $this->mail->preSend();
        return $this->mail->getSentMIMEMessage();
    }
}
