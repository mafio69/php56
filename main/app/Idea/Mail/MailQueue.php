<?php

namespace Idea\Mail;

use Queue;
use Mail;
use Auth;

class MailQueue
{

    public function fire($job, $data)
    {
        $email_comment = $data['email_comment'];
        $emails = $data['emails'];
        $injury_id = $data['injury_id'];
        $user_id = $data['user_id'];
        $inputs_doc = $data['doc_ids'];
        $template_name = $data['template_name'];
        $injury = \Injury::find($injury_id);
        $url = $data['url'];
        $docsToSend = \InjuryFiles::whereIn('id', $inputs_doc )->with('document_type')->get();


        $mailer = new Mailer();
        foreach ($emails as $email) {
            $mailer->addAddress($email);
        }
         if($template_name == 'billing-department'){
             $mailer->from('rozliczenia.szkody@cas-auto.pl', \Config::get('webconfig.MAIL_NAME'));
        }
        $mailer->setSubject('Dokumenty dołączone do sprawy nr '.$injury->injury_nr.', nr rej. '.$injury->vehicle->registration);
        $html = \View::make('emails.'.$template_name, ['injury' => $injury, 'docs' => $docsToSend, 'logo' => public_path() . '/assets/css/images/idea-getin-logo.png', 'email_comment' => $email_comment])->render();
        $mailer->setBody($html);
        foreach ($docsToSend as $doc) {
            if ($doc->type == 3) {
                $documentType = \InjuryDocumentType::find($doc->category);
                $pathToFile = \Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . "/" . $documentType->short_name . "/" . $doc->file;
            } else {
                $pathToFile = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/" . $doc->file;
            }

            $path_parts = pathinfo($pathToFile);
            if(isset($path_parts['extension']) && in_array($path_parts['extension'], ['eml', 'msg'])){
                $zip = new \ZipArchive;

                $zipname = \Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/zip/'.$path_parts['filename'].'.zip';
                if(!file_exists(\Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/zip')){
                    mkdir(\Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/zip');
                }

                $zip->open($zipname, \ZipArchive::CREATE);
                $zip->addFile($pathToFile,$doc->file);
                $zip->close();
                $pathToFile = $zipname;
                $doc->file = $path_parts['filename'].'.zip';
            }

            $mailer->addAttachment($pathToFile, $doc->file);
            \Log::info($doc->file, [filesize($pathToFile)]);
        }

        $mailer->send();

        $mail_filename = substr(md5(time().'xx'.rand(0, 9999)), 7, 16).'.eml';
        file_put_contents(\Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$mail_filename, $mailer->getEml());

        \InjuryFiles::create(array(
            'injury_id' => $injury_id,
            'type' => 4,
            'category' => 16,
            'document_type' => 'InjuryUploadedDocumentType',
            'document_id' => 16,
            'user_id' => $user_id,
            'file' => $mail_filename,
            'name' => 'wysłane dokumenty'
        ));

        $wpis = $email_comment;
        $wpis .= " Adresy email: ";
        $wpis .= implode(',', $emails);
        $wpis .= "; Wysłane dokumenty: ";

        foreach ($docsToSend as $doc) {
            if($doc->type == 3) {
                $wpis .= '<a href="'.$url.'/download-generate-doc/'.$doc->id.'" target="_blank">'.$doc->document->name.'</a>; ';
            }else {
                $wpis .= '<a href="'.$url.'/download-doc/'.$doc->id.'" target="_blank">'. $doc->document->name.'</a>; ';
            }
        }

        \Histories::history($injury_id, 168, $user_id, '-1', $wpis);

        if($job) $job->delete();
    }

}
