<?php

use iio\libmergepdf\Merger;

class InsurancesDocumentsController extends \BaseController {


    function __construct()
    {
        $this->beforeFilter('permitted:kartoteka_polisy#zarzadzaj', ['only' => ['postGenerateDoc']]);
        $this->beforeFilter('permitted:kartoteka_polisy#certyfikat', ['only' => ['postGenerateHestiaCertificate']]);
    }

    public function getInfo($id, $document_id)
    {
      $documentType = LeasingAgreementDocumentType::find($document_id);
      $agreement = LeasingAgreement::find($id);
      $annex_refers = LeasingAgreementAnnexRefer::lists('name','id');
      return View::make('insurances.manage.dialog.generateDocument', compact('id', 'document_id', 'agreement', 'documentType','annex_refers'));
    }

    public function getAnnexCalculate($id, $type_id){

      $annex_refer =  LeasingAgreementAnnexRefer::find($type_id);

      $agreement = LeasingAgreement::find($id);

      $value = null;


      if($annex_refer->id == 12){
        $insurance = $agreement->insurances->last();
        if($insurance){
          $date_to = $insurance->date_to;
          $date_form_years = $insurance->date_from;

          if(Input::has('start_date')){
            $date_now = Carbon\Carbon::createFromFormat('Y-m-d', Input::get('start_date'));
          }
          else{
            $date_now = Carbon\Carbon::now();
          }

          if(Input::has('end_date')){
            $date_to_years = Carbon\Carbon::createFromFormat('Y-m-d', Input::get('end_date'));
          }
          else{
            $date_to_years = Carbon\Carbon::createFromFormat('Y-m-d', $date_to);
          }

          $endInsuranceDate = Date::createFromFormat('Y-m-d', $date_to);
          $daysToRefund = $date_now->diffInDays($endInsuranceDate);

          $date_form_years = Carbon\Carbon::createFromFormat('Y-m-d', $date_form_years);
          $years = $date_to_years->year-$date_form_years->year;

          if($years>0){
            $init_val = $insurance->contribution/(360*$years);
          }
          else{
            $init_val = $insurance->contribution/360;
          }
        //  $init_val = round($init_val,2);

          $value = number_format(round($init_val*($daysToRefund+1),2),2,".","");
          //$value = $daysToRefund;
        }
      }

      return json_encode(['value'=>$value]);
    }

    public function postGenerateDoc($id, $document_id)
    {
        ob_start();

        $inputs = Input::all();

        $annex_refer =  LeasingAgreementAnnexRefer::find($inputs['refer']);

        $doc = new Idea\DocGenerator\DocGeneratorInsurance($id, $document_id, $inputs);
        $filename = $doc->generateDoc();

        if($document_id == 1) {
//             $documentType = LeasingAgreementDocumentType::find($document_id);
// //            $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . "/" . $documentType->short_name . "/" . $filename;
//             $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . "/" . $documentType->short_name . "/";
//             $fullPath = $path . $filename;
//             $merger = new Merger;
// //            $merger->addFile($path);
//             $merger->addFile($fullPath);
//             $merger->addFile(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/templates/hestia_klauzula.pdf");
//             $createdPdf = $merger->merge();
// //            file_put_contents($path, $createdPdf);
//             file_put_contents($fullPath, $createdPdf);

//             $compressor = new \Idea\Compressor\Compressor($path, $filename);
//             $filename = $compressor->toPdf();

            $compressor = new \Idea\Compressor\Compressor(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . "/" . LeasingAgreementDocumentType::find($document_id)->short_name . "/", $filename);
            $filename = $compressor->toPdf();
        }

        $file = LeasingAgreementFile::create(array(
            'leasing_agreement_id' => $id,
            'type'		=> 3,
            'category'	=> $document_id,
            'user_id'	=> Auth::user()->id,
            'file'		=> $filename,
            'name' 		=> '',
        ));

        Histories::leasingAgreementHistory($id, 28, Auth::user()->id, 'Kategoria '.$doc->getDocumentType()->name.' - <a target="_blank" href="'.URL::to('insurances/documents/downloadGenerateDoc', [$file->id]).'">pobierz</a>');

        if($doc->getDocumentType()->id==1&&$annex_refer->id==12){
      		$agreement = LeasingAgreement::find($id);
      		$last_insurance = $agreement->insurances->last();

      		$last_insurance->active = 0;
      		$last_insurance->save();

      		$new_insurance = $last_insurance;
      		$new_insurance->active = 1;
      		$new_insurance->date_from = Input::get('date_form');
      		$new_insurance->refund = Input::get('annex_value');
      		$new_insurance->if_refund_contribution = 1;
      		$new_insurance->user_id = Auth::user()->id;

      		$new_insurance->notification_number = Auth::user()->insurances_global_nr;
      		$new_insurance->refunded_insurance_id = $last_insurance->id;

      		LeasingAgreementInsurance::create($new_insurance->toArray());

      		$agreement->archive = \Carbon\Carbon::now()->toDateTimeString();
      		$agreement->save();

      		Histories::leasingAgreementHistory($id, 10);

      		Flash::success("Wykonano zwrot składki do umowy nr ".$agreement->nr_contract);
        }

        return URL::to('insurances/documents/download-generate-doc', [$file->id]);
    }

    public function getDownloadGenerateDoc($id) {
        ob_start();
        $file = LeasingAgreementFile::find($id);

        $documentType = LeasingAgreementDocumentType::find($file->category);

        $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".$documentType->short_name."/".$file->file;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $pathParts = pathinfo($path);

        $name = rand('10000','99999');
        // Prepare the headers
        $headers = array(
            'Content-Description' => 'File Transfer',
            'Content-Type' => finfo_file($finfo, $path),
            'Content-Transfer-Encoding' => 'binary',
            'Expires' => 0,
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
            'Content-Length' => File::size($path),
            'Content-Disposition' => 'inline; filename="' . $name . '.' . $pathParts['extension'] . '"'
        );
        finfo_close($finfo);

        $response = new Symfony\Component\HttpFoundation\Response('', 200, $headers);

        // If there's a session we should save it now
        if (Config::get('session.driver') !== '') {
            Session::save();
        }

        // Below is from http://uk1.php.net/manual/en/function.fpassthru.php comments
        session_write_close();
        if (ob_get_contents()) ob_end_clean();
        $response->sendHeaders();
        if ($file = fopen($path, 'rb')) {
            while (!feof($file) and (connection_status() == 0)) {
                print(fread($file, 1024 * 8));
                flush();
            }
            fclose($file);
        }

        // Finish off, like Laravel would
        Event::fire('laravel.done', array($response));
        //$response->foundation->finish();

        exit;
    }

    public function postGenerateHestiaCertificate($policy_id)
    {
        set_time_limit(500);
        DB::disableQueryLog();

        $policy = LeasingAgreementInsurance::with( 'leasingAgreement.client')->find($policy_id);

        $insuranceCompaniesPolicy = $policy->leasingAgreement->insurances()->where('insurance_company_id', $policy->insurance_company_id)->count();
        $insurances = $policy->leasingAgreement->insurances->count();
        $request = Input::all();
        $request['insuranceCompaniesPolicy'] = $insuranceCompaniesPolicy;
        $request['insurances'] = $insurances;

        $document_type_id = 2;
        if($policy->insurance_company_id == 320) $document_type_id = 4;

        $doc = new Idea\DocGenerator\DocGeneratorPolicy($policy, $document_type_id, $request);
        $filename = $doc->generateDoc();

        $documentType = LeasingAgreementDocumentType::find($document_type_id);
        $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".$documentType->short_name."/".$filename;
        $merger = new Merger;
        $merger->addFile($path);
        $merger->addFile(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/templates/hestia_klauzula.pdf");
        $createdPdf = $merger->merge();
        file_put_contents($path, $createdPdf);

        $file = LeasingAgreementFile::create(array(
            'leasing_agreement_id' => $policy->leasingAgreement->id,
            'type'		=> 3,
            'category'	=> 2,
            'user_id'	=> Auth::user()->id,
            'file'		=> $filename,
            'name' 		=> '',
        ));

        Histories::leasingAgreementHistory($policy->leasingAgreement->id, 28, Auth::user()->id, 'Kategoria '.$doc->getDocumentType()->name.' - <a target="_blank" href="'.URL::to('insurances/documents/downloadGenerateDoc', [$file->id]).'">pobierz</a>');

        Session::put('download.in.the.next.request', URL::to('insurances/documents/download-generate-doc', [$file->id]));

        return Response::json([
            'code'  => 0
        ]);
    }

    public function postGenerateHestiaCertificateNoClient($policy_id)
    {
        set_time_limit(500);
        DB::disableQueryLog();

        $policy = LeasingAgreementInsurance::with( 'leasingAgreement.client')->find($policy_id);

        $insuranceCompaniesPolicy = $policy->leasingAgreement->insurances()->where('insurance_company_id', $policy->insurance_company_id)->count();
        $insurances = $policy->leasingAgreement->insurances->count();
        $request = Input::all();
        $request['insuranceCompaniesPolicy'] = $insuranceCompaniesPolicy;
        $request['insurances'] = $insurances;

        $document_type_id = 3;
        if($policy->insurance_company_id == 320) $document_type_id = 5;

        $doc = new Idea\DocGenerator\DocGeneratorPolicy($policy, $document_type_id, $request);
        $filename = $doc->generateDoc();

        $documentType = LeasingAgreementDocumentType::find($document_type_id);
        $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".$documentType->short_name."/".$filename;
        $merger = new Merger;
        $merger->addFile($path);
        $merger->addFile(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/templates/hestia_klauzula.pdf");
        $createdPdf = $merger->merge();
        file_put_contents($path, $createdPdf);

        $file = LeasingAgreementFile::create(array(
            'leasing_agreement_id' => $policy->leasingAgreement->id,
            'type'		=> 3,
            'category'	=> 3,
            'user_id'	=> Auth::user()->id,
            'file'		=> $filename,
            'name' 		=> '',
        ));

        Histories::leasingAgreementHistory($policy->leasingAgreement->id, 28, Auth::user()->id, 'Kategoria '.$doc->getDocumentType()->name.' - <a target="_blank" href="'.URL::to('insurances/documents/downloadGenerateDoc', [$file->id]).'">pobierz</a>');

        Session::put('download.in.the.next.request', URL::to('insurances/documents/download-generate-doc', [$file->id]));

        return Response::json([
            'code'  => 0
        ]);
    }
}
