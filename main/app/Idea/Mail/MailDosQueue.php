<?php

namespace Idea\Mail;

use Config;
use Queue;
use Mail;
use Auth;

class MailDosQueue
{

    public function fire($job, $data)
    {
        $email_comment = $data['email_comment'];
        $emails = $data['emails'];
        $injury_id = $data['injury_id'];
        $user_id = $data['user_id'];
        $inputs_doc = $data['doc_ids'];
        $injury = \DosOtherInjury::find($injury_id);
        $url = $data['url'];
        $docsToSend = \DosOtherInjuryFiles::whereIn('id', $inputs_doc )->with('document_type')->get();

        Mail::send('emails.dos-injury-docs', ['injury' => $injury, 'docs' => $docsToSend, 'logo' => public_path() . '/assets/css/images/idea-getin-logo.png', 'email_comment' => $email_comment], function ($message) use ($emails, $docsToSend, $email_comment, $injury,$user_id,$url) {
            $message->subject('Dokumenty dołączone do sprawy nr '.$injury->injury_nr.', nr umowy '.$injury->object->nr_contract);

            foreach ($emails as $email) {
                $message->to($email);
            }

            foreach ($docsToSend as $doc) {
                if ($doc->type == 2) {
                    $pathToFile = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/" . $doc->file;
                } else {
                    $documentType = \DosOtherInjuryDocumentType::find($doc->category);
                    $pathToFile = \Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . "/" . $documentType->short_name . "/" . $doc->file;
                }

                $message->attach($pathToFile);
            }
        });

        $mailer = new Mailer();
        foreach ($emails as $email) {
            $mailer->addAddress($email);
        }
        $mailer->setSubject('Dokumenty dołączone do sprawy nr '.$injury->injury_nr.', nr umowy '.$injury->object->nr_contract);
        $html = \View::make('emails.dos-injury-docs', ['injury' => $injury, 'docs' => $docsToSend, 'logo' => public_path() . '/assets/css/images/idea-getin-logo.png', 'email_comment' => $email_comment])->render();
        $mailer->setBody($html);
        foreach ($docsToSend as $doc) {
            if ($doc->type == 2) {
                $pathToFile = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/" . $doc->file;
            } else {
                $documentType = \DosOtherInjuryDocumentType::find($doc->category);
                $pathToFile = \Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . "/" . $documentType->short_name . "/" . $doc->file;
            }

            $mailer->addAttachment($pathToFile, $doc->file);
        }
        $mail_filename = substr(md5(time().'xx'.rand(0, 9999)), 7, 16).'.eml';
        file_put_contents(\Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$mail_filename, $mailer->getEml());
        \DosOtherInjuryFiles::create([
            'injury_id' => $injury_id,
            'type'		=> 4,
            'category'	=> 0,
            'user_id'	=> $user_id,
            'file'		=> $mail_filename,
        ]);


        $wpis = $email_comment;
        $wpis .= " Adresy email: ";
        $wpis .= implode(',', $emails);
        $wpis .= "; Wysłane dokumenty: ";

        foreach ($docsToSend as $doc) {
            if($doc->type == 2) {

                $wpis .= '<a href="'.$url.'/dos/injuries/download-doc/'.$doc->id.'" target="_blank">'. Config::get('definition.fileCategory.'.$doc->category).'</a>; ';
            }else {
                $wpis .= '<a href="'.$url.'/dos/injuries/download-generate-doc/'.$doc->id.'" target="_blank">'.$doc->document_type()->first()->name.'</a>; ';
            }
        }

        \Histories::dos_history($injury_id, 168, $user_id, '-1', $wpis);

        $job->delete();
    }

}
