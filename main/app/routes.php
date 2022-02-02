<?php


use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Router;

Route::group(array('prefix' => 'cron'), function() {
   Route::get('update/statuses', 'CronController@updateStatuses');

});

Route::group(array('prefix' => 'import'), function () {
    Route::post('companies', 'ImportController@companies');
    Route::post('branches', 'ImportController@branches');
    Route::post('adverts', 'ImportController@adverts');
    Route::post('injuries', 'ImportController@injuries');
});

Route::group(array('prefix' => 'mobile'), function () {
    Route::post('find_branches', 'MobileController@findBranch');

    Route::post('find_branches/in_city', 'MobileController@findBranchInCity');

    Route::post('cities', 'MobileController@getCities');

    Route::get('advert/draw/{id_res?}', 'MobileController@drawAdvert');

    Route::get('advert/{id_res?}/{id?}', 'MobileController@generateAdvert');

    Route::post('register_injury', 'MobileController@registerInjury');

    Route::post('injury_attach_img', 'MobileController@injuryAttachImg');

    Route::get('confirm_injury/{token}', array(
            'as' => 'mobile.confirm_injury',
            'uses' => 'MobileController@confirmInjury'
        )
    );
    Route::get('last_log', 'MobileController@lastLog');
});

Route::group(['prefix' => 'api'], function() {
    Route::group(['prefix' => 'syjon', 'before' => 'apikey:2'], function(){
        Route::post('sync-programs', 'ApiSyjonController@syncPrograms');
    });

    Route::group(['prefix' => 'ea', 'before' => 'apikey:1'], function () {
        Route::post('login', 'AuthenticateController@authenticate');

        Route::group(array('before' => ['jwt-auth']), function () {
            Route::post('refresh-token', 'AuthenticateController@refresh');
            Route::post('vehicle', 'ApiEaController@vehicle');
            Route::post('car-workshops', 'ApiEaController@carWorkshops');
            Route::post('register-injury', 'ApiEaController@registerInjury');
            Route::post('type-incident-list', 'ApiEaController@typeIncidentList');
            Route::post('update-injury', 'ApiEaController@updateInjury');
        });
    });
});

//z autoryzacją
Route::group(array('before' => 'auth'), function () {

    Route::post('password', 'UsersController@postRegeneratePassword');
    Route::get('password', 'UsersController@getRegeneratePassword');
    Route::get('locked', 'HomeController@locked');

    Route::get('/logout', array(
            'as' => 'logout',
            'uses' => 'HomeController@logout'
        )
    );

    Route::group(array('before' => ['password.check', 'user.session_update']), function () {
        Route::controller('debug', 'DebugController');
        Route::get('/', array(
                'as' => 'home',
                'uses' => 'HomeController@index'
            )
        );

        Route::get('/debug/doc-gen-view/{injury_id}/{doc_type_id}', array(
                'as' => 'debug-getDocGenView',
                'uses' => 'DebugController@getDocGenView'
            )
        );

        Route::post('/debug-get-file', function()
        {
            $pathToFile = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/3200449319750c0b050fe1542ff374864a8f6e74.eml";
            return file_get_contents($pathToFile);
        });

        Route::get('/debug-send-file', function()
        {
            $pathToFile = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/3200449319750c0b050fe1542ff374864a8f6e74.eml";
            return View::make('debug.send', compact('pathToFile'));
        });


        Route::post('/debug-send-file', function()
        {
            $mailer = new Idea\Mail\Mailer();
            $mailer->setBody('test');
            $mailer->setSubject('Dokumenty dołączone do sprawy nr');
            $mailer->addAddress('przemek@webwizards.pl');
            $attachment = Input::file('file');

            $mailer->addAttachment($attachment->getPathName(), $attachment->getClientOriginalName());

            $path = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/3200449319750c0b050fe1542ff374864a8f6e74.eml";
            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            $pathParts = pathinfo($path);

            $name = rand('10000','99999');
            // Prepare the headers

            $content_type = finfo_file($finfo, $path);
//            dd($content_type, Input::file('file')->getExtension(), Input::file('file')->getMimeType(),$attachment->getPathName(), $attachment->getClientOriginalName());

            $mailer->addAttachment($path, 'test1.eml', 'base64', $content_type);
            $mailer->addAttachment($path, 'test2.eml', 'base64', 'message/rfc822');

            Input::file('file')->move(\Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files", 'upload_mail.eml');
            $mailer->addAttachment(\Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/upload_mail.eml", 'test3.eml');

            var_dump($mailer->send());

            var_dump(Mail::send('emails.welcome', [], function($message) use($path)
            {
                $message->to('przemek@webwizards.pl')->subject('[IdeaLeasing] Dokumenty dołączone do sprawy.');
                $message->attach($path);
            }));

            var_dump(Mail::send('emails.welcome', [], function($message)
            {
                $message->to('przemek@webwizards.pl')->subject('[IdeaLeasing] Dokumenty dołączone do sprawy 2.');
                $message->attach(\Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/upload_mail.eml");
            }));
        });

        Route::controller('vehicle-manage/companies', 'VmanageCompaniesController');
        Route::controller('vehicle-manage/company/vehicles', 'VmanageVehiclesController');
        Route::controller('vehicle-manage/company/vehicle/users', 'VmanageUsersController');
        Route::controller('vehicle-manage/company/vehicle/owners', 'VmanageOwnersController');
        Route::controller('vehicle-manage/company/vehicle/info', 'VmanageVehicleInfoController');
        Route::controller('vehicle-manage/company/guardians', 'VmanageCompanyGuardiansController');
        Route::controller('vehicle-manage/import', 'VmanageImportController');

        Route::controller('companies', 'CompaniesController');
        Route::controller('company/garages', 'CompanyGaragesController');
        Route::controller('company/account-numbers', 'CompanyAccountNumbersController');
        Route::controller('plans', 'PlansController');
        Route::controller('plan/groups', 'PlanGroupsController');
        Route::controller('main', 'HomeController');

        Route::controller('dos/other/reports', 'DosOtherReportsController');

        Route::controller('injuries/buyers', 'InjuriesBuyersController');
        Route::controller('injuries/manage', 'InjuriesManageController');
        Route::controller('files', 'FilesController');

        Route::controller('commissions', 'CommissionsController');

        Route::controller('settings/api/modules', 'ApiModulesController');
        Route::controller('settings/api/users', 'ApiUsersController');

        Route::controller('settings/document-templates', 'SettingsDocumentTemplatesController');
        Route::controller('settings/departments', 'SettingsDepartmentsController');
        Route::controller('settings/teams', 'SettingsTeamsController');
        Route::controller('company-guardians', 'CompanyGuardiansController');

        Route::controller('dos/other/injuries/make', 'DosOtherInjuriesMakeController');

        Route::group(['prefix' => 'tasks'], function(){
            Route::controller('list', 'TasksListController');
            Route::controller('reports', 'TaskReportsController');
            Route::controller('mailboxes', 'TaskMailboxesController');
            Route::controller('types', 'TaskTypesController');
            Route::controller('assignments', 'TaskAssignmentsController');
            Route::controller('excludes', 'TaskExcludesController');
            Route::controller('black-list', 'TaskBlackListController');
            Route::controller('address-book', 'TaskAddressBookController');
            Route::controller('manage-types', 'TaskManageTypesController');
            Route::controller('/', 'TasksController');
        });

        //wyświetlanie obrazków
        Route::get('/file/uploads/{folder}/{file}/{type}', function ($folder, $file, $type = '') {
            $folderA = explode('_', $folder);
            $folder = '';
            foreach ($folderA as $f) {
                $folder .= $f . '/';
            }
            if (isset($type) && $type != '')
                $image = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/' . $folder . $type . '/' . $file);
            else
                $image = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/' . $folder . $file);
            return $image->response();
        });


        //settings

        //Models
        Route::get('/settings/brand/{id}/models', array(
            'as' => 'settings.brand.models',
            'uses' => 'Brands_modelController@index'
        ));

        Route::get('/settings/brand/{id}/models/create', array(
            'uses' => 'Brands_modelController@create'
        ));

        Route::post('/settings/brand/{id}/models/store', array(
            'uses' => 'Brands_modelController@store'
        ));

        Route::get('/settings/brand/{id}/models/edit', array(
            'uses' => 'Brands_modelController@edit'
        ));

        Route::post('/settings/brand/{id}/models/update', array(
            'uses' => 'Brands_modelController@update'
        ));

        Route::get('/settings/brand/{id}/models/delete', array(
            'uses' => 'Brands_modelController@delete'
        ));

        Route::post('/settings/brand/{id}/models/destroy', array(
            'uses' => 'Brands_modelController@destroy'
        ));

        //Models generations
        Route::get('/settings/brand/model/{id}/generations', array(
            'as' => 'settings.brand.model.generations',
            'uses' => 'Brands_models_generationController@index'
        ));

        //injuries
        Route::post('/injuries/task/', array(
                'as' => 'injuries-setTask',
                'uses' => 'InjuriesController@setTask'
            )
        );


        Route::get('/injuries/accept/{id}/', array(
                'as' => 'injuries-getAccept',
                'uses' => 'InjuriesController@getAccept'
            )
        );

        Route::get('/injuries/assign/{id?}/{owner_id?}', array(
                'as' => 'injuries-assignCompany',
                'uses' => 'DialogsInjuriesController@getAssign'
            )
        );

        Route::get('/injuries/without-company/{id}/', array(
                'as' => 'injuries-withoutCompany',
                'uses' => 'DialogsInjuriesController@getWithoutCompany'
            )
        );
        Route::post('/injuries/without-company/{id}/save', array(
                'as' => 'injuries-setWithoutCompany',
                'uses' => (Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1) ? 'InjuriesController@setWithoutCompany' : 'NonasInjuriesController@setWithoutCompany'
            )
        );

        Route::post('/injuries/assign/branchesList/{id?}/{owner_id?}', array(
                'as' => 'injuries-assignBranchesList',
                'uses' => 'InjuriesController@getBranchesList'
            )
        );
        Route::post('/injuries/assign/branchesNameList/{id?}/{spec?}/{is_active_vat?}/{invoice_id?}', array(
                'as' => 'injuries-assignBranchesNameList',
                'uses' => 'InjuriesController@getBranchesNameList'
            )
        );
        Route::post('/injuries/assign/set/{id}/', array(
                'as' => 'injuries-assignBranch',
                'uses' => (Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1) ? 'InjuriesController@setAssignBranch' : 'NonasInjuriesController@setAssignBranch'
            )
        );

        Route::get('/injuries/cancel/{id}/', array(
                'as' => 'injuries-getCancel',
                'uses' => 'DialogsInjuriesController@getCancel'
            )
        );

        Route::get('/injuries/change-injury-status/{id}/', array(
                'as' => 'injuries-getChangeInjuryStatus',
                'uses' => 'DialogsInjuriesController@getChangeInjuryStatus'
            )
        );

        Route::get('/injuries/change-injury-step/{id}/', array(
                'as' => 'injuries-getChangeInjuryStep',
                'uses' => 'DialogsInjuriesController@getChangeInjuryStep'
            )
        );

        Route::get('/injuries/delete/{id}/', array(
                'as' => 'injuries-getDelete',
                'uses' => 'DialogsInjuriesController@getDelete'
            )
        );

        Route::post('/injuries/delete/{id}/', array(
                'as' => 'injuries-setDelete',
                'uses' => 'InjuriesController@setDelete'
            )
        );

        Route::get('/injuries/delete-ea/{id}/', array(
                'uses' => 'DialogsInjuriesController@getDeleteEa'
            )
        );

        Route::post('/injuries/delete-ea/{id}/', array(
                'uses' => 'InjuriesController@setDeleteEa'
            )
        );

        Route::post('/injuries/cancel/{id}/', array(
                'as' => 'injuries-setCancel',
                'uses' => 'InjuriesController@setCancel'
            )
        );

        Route::post('/injuries/set-change-status/{id}/', array(
                'as' => 'injuries-setChangeStatus',
                'uses' => 'InjuriesController@setChangeStatus'
            )
        );

        Route::post('/injuries/set-change-injury-step/{id}/', array(
                'as' => 'injuries-setChangeInjuryStep',
                'uses' => 'InjuriesController@setChangeInjuryStep'
            )
        );

        Route::get('/injuries/restore-canceled/{id}/', array(
                'as' => 'injuries-getRestoreCanceled',
                'uses' => 'DialogsInjuriesController@getRestoreCanceled'
            )
        );


        Route::post('/injuries/restore-canceled/{id}/', array(
                'as' => 'injuries-setRestoreCanceled',
                'uses' => 'InjuriesController@setRestoreCanceled'
            )
        );

        Route::get('/injuries/restore-deleted/{id}/', array(
                'as' => 'injuries-getRestoreDeleted',
                'uses' => 'DialogsInjuriesController@getRestoreDeleted'
            )
        );


        Route::post('/injuries/restore-deleted/{id}/', array(
                'as' => 'injuries-setRestoreDeleted',
                'uses' => 'InjuriesController@setRestoreDeleted'
            )
        );

        Route::get('/injuries/restore-total/{id}/', array(
                'as' => 'injuries-getRestoreTotal',
                'uses' => 'DialogsInjuriesController@getRestoreTotal'
            )
        );
        Route::post('/injuries/restore-total/{id}/', array(
                'as' => 'injuries-setRestoreTotal',
                'uses' => 'InjuriesController@setRestoreTotal'
            )
        );

        Route::get('/injuries/restore-completed/{id}/', array(
                'as' => 'injuries-getRestoreCompleted',
                'uses' => 'DialogsInjuriesController@getRestoreCompleted'
            )
        );

        Route::post('/injuries/restore-completed/{id}/', array(
                'as' => 'injuries-setRestoreCompleted',
                'uses' => 'InjuriesController@setRestoreCompleted'
            )
        );

        Route::get('/injuries/invoice/forward/{invoice_id}', array(
                'uses' => 'DialogsInjuriesController@getInvoiceForward'
            )
        );

        Route::post('/injuries/invoice/forward/{invoice_id}', array(
                'uses' => 'InjuriesController@setInvoiceForward'
            )
        );
        Route::get('/injuries/invoice/return/{invoice_id}', array(
                'uses' => 'DialogsInjuriesController@getInvoiceReturn'
            )
        );

        Route::post('/injuries/invoice/return/{invoice_id}', array(
                'uses' => 'InjuriesController@setInvoiceReturn'
            )
        );
        Route::get('/injuries/invoice/forward-again/{invoice_id}', array(
                'uses' => 'DialogsInjuriesController@getInvoiceForwardAgain'
            )
        );

        Route::post('/injuries/invoice/forward-again/{invoice_id}', array(
                'uses' => 'InjuriesController@setInvoiceForwardAgain'
            )
        );

        Route::get('/injuries/restore/{id}/', array(
                'as' => 'injuries-getRestore',
                'uses' => 'DialogsInjuriesController@getRestore'
            )
        );

        Route::get('/injuries/agreement-settled/{id}/', array(
                'as' => 'injuries-getAgreementSettled',
                'uses' => 'DialogsInjuriesController@getAgreementSettled'
            )
        );

        Route::get('/injuries/discontinuation-investigation/{id}/', array(
                'as' => 'injuries-getDiscontinuationInvestigation',
                'uses' => 'DialogsInjuriesController@getDiscontinuationInvestigation'
            )
        );

        Route::get('/injuries/deregistration-vehicle/{id}/', array(
                'as' => 'injuries-getDeregistrationVehicle',
                'uses' => 'DialogsInjuriesController@getDeregistrationVehicle'
            )
        );

        Route::get('/injuries/transferred-dok/{id}/', array(
                'as' => 'injuries-getTransferredDok',
                'uses' => 'DialogsInjuriesController@getTransferredDok'
            )
        );

        Route::get('/injuries/no-signs-punishment/{id}/', array(
                'as' => 'injuries-getNoSignsPunishment',
                'uses' => 'DialogsInjuriesController@getNoSignsPunishment'
            )
        );

        Route::get('/injuries/usurpation/{id}/', array(
                'as' => 'injuries-getUsurpation',
                'uses' => 'DialogsInjuriesController@getUsurpation'
            )
        );

        Route::get('/injuries/resignation-claims/{id}/', array(
                'as' => 'injuries-getResignationClaims',
                'uses' => 'DialogsInjuriesController@getResignationClaims'
            )
        );

        Route::get('/injuries/total-injuries/{id}/', array(
                'as' => 'injuries-getTotalInjuries',
                'uses' => 'DialogsInjuriesController@getTotalInjuries'
            )
        );

        Route::post('/injuries/restore-claims/{id}/', array(
                'as' => 'injuries-setResignationClaims',
                'uses' => 'InjuriesController@setResignationClaims'
            )
        );

        Route::post('/injuries/total-injuries/{id}/', array(
                'as' => 'injuries-setTotalInjuries',
                'uses' => 'InjuriesController@setTotalInjuries'
            )
        );

        Route::get('/injuries/contract-settled/{id}/', array(
                'as' => 'injuries-getContractSettled',
                'uses' => 'DialogsInjuriesController@getContractSettled'
            )
        );

        Route::post('/injuries/restore-settled/{id}/', array(
                'as' => 'injuries-setContractSettled',
                'uses' => 'InjuriesController@setContractSettled'
            )
        );

        Route::post('/injuries/restore/{id}/', array(
                'as' => 'injuries-setRestore',
                'uses' => 'InjuriesController@setRestore'
            )
        );

        Route::get('/injuries/total/{id}/', array(
                'as' => 'injuries-getTotal',
                'uses' => 'DialogsInjuriesController@getTotal'
            )
        );

        Route::get('/injuries/total/finished/{id}', array(
            'as' => 'injuries-getTotalFinished',
            'uses' => 'DialogsInjuriesController@getTotalFinished'
        ));

        Route::get('/injuries/theft/{id}/', array(
                'as' => 'injuries-getTheft',
                'uses' => 'DialogsInjuriesController@getTheft'
            )
        );

        Route::post('/injuries/total/{id}/', array(
                'as' => 'injuries-setTotal',
                'uses' => (Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1) ? 'InjuriesController@setTotal' : 'NonasInjuriesController@setTotal'
            )
        );

        Route::post('/injuries/discontinuation-investigation/{id}/', array(
                'as' => 'injuries-setDiscontinuationInvestigation',
                'uses' => 'InjuriesController@setDiscontinuationInvestigation'
            )
        );

        Route::post('/injuries/agreement-settled/{id}/', array(
                'as' => 'injuries-setAgreementSettled',
                'uses' => 'InjuriesController@setAgreementSettled'
            )
        );

        Route::post('/injuries/deregistration-vehicle/{id}/', array(
                'as' => 'injuries-setDeregistrationVehicle',
                'uses' => 'InjuriesController@setDeregistrationVehicle'
            )
        );

        Route::post('/injuries/transferred-dok/{id}/', array(
                'as' => 'injuries-setTransferredDok',
                'uses' => 'InjuriesController@setTransferredDok'
            )
        );

        Route::post('/injuries/no-signs-punishment/{id}/', array(
                'as' => 'injuries-setNoSignsPunishment',
                'uses' => 'InjuriesController@setNoSignsPunishment'
            )
        );

        Route::post('/injuries/usurpation/{id}/', array(
                'as' => 'injuries-setUsurpation',
                'uses' => 'InjuriesController@setUsurpation'
            )
        );

        Route::post('/injuries/total/finished/{id}/', array(
                'as' => 'injuries-setTotalFinished',
                'uses' => 'InjuriesController@setTotalFinished'
            )
        );

        Route::post('/injuries/theft/{id}/', array(
                'as' => 'injuries-setTheft',
                'uses' => (Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1) ? 'InjuriesController@setTheft' : 'NonasInjuriesController@setTheft'
            )
        );

        Route::get('/injuries/inprogress/{id}/', array(
                'as' => 'injuries-getInprogress',
                'uses' => 'DialogsInjuriesController@getInprogress'
            )
        );

        Route::post('/injuries/inprogress/{id}/', array(
                'as' => 'injuries-setInprogress',
                'uses' => 'InjuriesController@setInprogress'
            )
        );

        Route::get('/injuries/complete/{id}/get', array(
                'as' => 'injuries-getComplete',
                'uses' => 'DialogsInjuriesController@getComplete'
            )
        );

        Route::get('/injuries/complete-refused/{id}/get', array(
                'as' => 'injuries-getCompleteRefused',
                'uses' => 'DialogsInjuriesController@getCompleteRefused'
            )
        );

        Route::get('/injuries/complete-l/{id}/', array(
                'as' => 'injuries-getComplete-l',
                'uses' => 'DialogsInjuriesController@getCompleteL'
            )
        );

        Route::get('/injuries/complete-n/{id}/', array(
                'as' => 'injuries-getComplete-n',
                'uses' => 'DialogsInjuriesController@getCompleteN'
            )
        );

        Route::get('/injuries/refusal/{id}/get', array(
                'as' => 'injuries-getRefusal',
                'uses' => 'DialogsInjuriesController@getRefusal'
            )
        );

        Route::post('/injuries/refusal/{id}/set', array(
                'as' => 'injuries-setRefusal',
                'uses' => (Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1) ? 'InjuriesController@setRefusal' : 'NonasInjuriesController@setRefusal'
            )
        );

        Route::post('/injuries/complete/{id}/set', array(
                'as' => 'injuries-setComplete',
                'uses' => (Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1) ? 'InjuriesController@setComplete' : 'NonasInjuriesController@setComplete'
            )
        );
        Route::post('/injuries/complete-refused/{id}/set', array(
                'as' => 'injuries-setCompleteRefused',
                'uses' => (Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1) ? 'InjuriesController@setCompleteRefused' : 'NonasInjuriesController@setCompleteRefused'
            )
        );
        Route::post('/injuries/complete-n/{id}/set', array(
                'as' => 'injuries-setComplete-n',
                'uses' => (Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1) ? 'InjuriesController@setCompleteN' : 'NonasInjuriesController@setCompleteN'
            )
        );
        Route::post('/injuries/complete-l/{id}/', array(
                'as' => 'injuries-setComplete-l',
                'uses' => (Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 1) ? 'InjuriesController@setCompleteL' : 'NonasInjuriesController@setCompleteL'
            )
        );

        Route::get('/injuries/upload-unprocessed', 'InjuriesController@uploadUnprocessed');
        Route::post('/injuries/proceed-unprocessed', 'InjuriesController@proceedUnprocessed');

        Route::get('/injuries/generate-docs-info/{id}/{key}', array(
                'as' => 'injuries-generate-docs-info',
                'uses' => 'InjuriesDocsController@getGenerateDocsInfo'
            )
        );

        Route::post('/docs/generate/{id}/{document_type_id}', array(
                'as' => 'get-doc-generate',
                'uses' => 'InjuriesDocsController@generateDoc'
            )
        );


        Route::get('/injuries/{id}/chat/create/', array(
                'as' => 'chat.create',
                'uses' => 'ChatController@create'
            )
        );

        Route::post('/injuries/{id}/chat/post/', array(
                'as' => 'chat.post',
                'uses' => 'ChatController@post'
            )
        );

        Route::get('/injuries/chat/{id}/replay/', array(
                'as' => 'chat.replay',
                'uses' => 'ChatController@replay'
            )
        );

        Route::post('/injuries/chat/{id}/replay/', array(
                'as' => 'chat.postReplay',
                'uses' => 'ChatController@postReplay'
            )
        );

        Route::get('/injuries/chat/{id}/deadline/', array(
                'as' => 'chat.deadline',
                'uses' => 'ChatController@deadline'
            )
        );

        Route::post('/injuries/chat/{id}/deadline/', array(
                'as' => 'chat.postDeadline',
                'uses' => 'ChatController@postDeadline'
            )
        );

        Route::get('/injuries/chat/{id}/close/', array(
                'as' => 'chat.close',
                'uses' => 'ChatController@close'
            )
        );

        Route::post('/injuries/chat/{id}/close/', array(
                'as' => 'chat.postClose',
                'uses' => 'ChatController@postClose'
            )
        );

        Route::get('/injuries/chat/{id}/accept/', array(
                'as' => 'chat.accept',
                'uses' => 'ChatController@accept'
            )
        );

        Route::post('/injuries/chat/{id}/accept/', array(
                'as' => 'chat.postAccept',
                'uses' => 'ChatController@postAccept'
            )
        );

        Route::get('/injuries/chat/{id}/removeDeadline/', array(
                'as' => 'chat.removeDeadline',
                'uses' => 'ChatController@removeDeadline'
            )
        );

        Route::post('/injuries/chat/{id}/removeDeadline/', array(
                'as' => 'chat.postRemoveDeadline',
                'uses' => 'ChatController@postRemoveDeadline'
            )
        );
        Route::get('/injuries/chat/{id}/deleteMessage/', array(
                'as' => 'chat.deleteMessage',
                'uses' => 'ChatController@deleteMessage'
            )
        );
        Route::post('/injuries/chat/{id}/removeMessage/', array(
                'as' => 'chat.removeMessage',
                'uses' => 'ChatController@removeMessage'
            )
        );

        Route::post('/injuries/chat/{id}/send-to-sap/', array(
                'as' => 'chat.sendToSap',
                'uses' => 'ChatController@sendToSap'
            )
        );


        Route::post('/injuries/isdl-getList', array(
                'as' => 'vehicle-registration-isdl-getList',
                'uses' => 'InjuriesController@getVehicleRegistrationIsdlList'
            )
        );
        Route::post('/injuries/vin-getContract', array(
                'as' => 'vehicle-contract-getList',
                'uses' => 'InjuriesController@getVehicleContractList'
            )
        );
        Route::post('/injuries/vehicle-check-injuries', array(
                'as' => 'vehicle-check-injuries',
                'uses' => 'InjuriesController@getVehicleCheckInjuries'
            )
        );
        Route::post('/injuries/vehicle-check-liquidationCard', array(
                'as' => 'vehicle-check-liquidationCard',
                'uses' => 'InjuriesController@getLiquidationCard'
            )
        );

        Route::post('/injuries/vin-getDrivers', array(
                'as' => 'drivers-getList',
                'uses' => 'InjuriesController@getDriversList'
            )
        );
        Route::post('/injuries/add', array(
                'as' => 'injuries-post-create',
                'uses' => 'InjuriesController@postCreate'
            )
        );

        Route::post('/injuries/add-mobile/{id}/', array(
                'as' => 'injuries-post-create-mobile',
                'uses' => 'InjuriesController@postCreateMobile'
            )
        );

        Route::get('/injuries/owner/show/{id}/', array(
                'as' => 'owner-show',
                'uses' => 'DialogsInjuriesController@showOwner'
            )
        );
        Route::get('/injuries/client/show/{id}/', array(
                'as' => 'owner-client',
                'uses' => 'DialogsInjuriesController@showClient'
            )
        );
        Route::get('/injuries/insurance_company/show/{id}/', array(
                'uses' => 'DialogsInjuriesController@showInsuranceCompany'
            )
        );

        Route::get('/injuries/client/edit/{id}/', array(
                'as' => 'client-edit',
                'uses' => 'DialogsInjuriesController@editClient'
            )
        );
        Route::get('/injuries/insurance_company/edit/{id}/', array(
                'as' => 'insurance_company-edit',
                'uses' => 'DialogsInjuriesController@editInsuranceCompany'
            )
        );

        Route::get('/injuries/branch-edit/{id}/get', array(
                'as' => 'injuries-getEditInjuryBranch',
                'uses' => 'DialogsInjuriesController@getEditInjuryBranch'
            )
        );

        Route::get('/injuries/branch-edit-original/{id}/get', array(
                'as' => 'injuries-getEditInjuryBranchOriginal',
                'uses' => 'DialogsInjuriesController@getEditInjuryBranchOriginal'
            )
        );

        Route::get('/injuries/branch-delete/{id}/get', array(
                'as' => 'injuries-getDeleteInjuryBranch',
                'uses' => 'DialogsInjuriesController@getDeleteInjuryBranch'
            )
        );

        Route::get('/injuries/branch-return/{id}/get', array(
                'as' => 'injuries-getReturnInjuryBranch',
                'uses' => 'DialogsInjuriesController@getReturnInjuryBranch'
            )
        );

        Route::post('/injuries/branch-edit/{id}/set', array(
                'as' => 'injuries-setBranch',
                'uses' => 'InjuriesController@setBranch'
            )
        );

        Route::post('/injuries/branch-edit-original/{id}/set', array(
                'as' => 'injuries-setBranchOriginal',
                'uses' => 'InjuriesController@setBranchOriginal'
            )
        );

        Route::post('/injuries/branch-return/{id}/set', array(
                'as' => 'injuries-returnBranch',
                'uses' => 'InjuriesController@returnBranch'
            )
        );

        Route::post('/injuries/branch-delete/{id}/set', array(
                'as' => 'injuries-deleteBranch',
                'uses' => 'InjuriesController@deleteBranch'
            )
        );


        Route::post('/injuries/damage/{id}/', array(
                'as' => 'injuries-setDamage',
                'uses' => 'InjuriesController@setDamage'
            )
        );

        Route::post('/injuries/images/{id}/{key}/', array(
                'as' => 'injuries-post-image',
                'uses' => 'InjuriesDocsController@postImage'
            )
        );


        Route::get('/injuries/history/add/{id}/', array(
                'as' => 'injuries-add-history',
                'uses' => 'DialogsInjuriesController@getAddHistory'
            )
        );
        Route::post('/injuries/history/create/{id}/', array(
                'as' => 'injuries-create-history',
                'uses' => 'InjuriesCardController@postStoreNote'
            )
        );

        Route::get('/injuries/note/add/{id}/', array(
                'as' => 'injuries-add-note',
                'uses' => 'DialogsInjuriesController@getAddNote'
            )
        );
        Route::post('/injuries/note/create/{id}/', array(
                'as' => 'injuries-create-note',
                'uses' => 'InjuriesController@createNote'
            )
        );

        Route::get('/injuries/info/edit/{id}/', array(
                'as' => 'injuries-getEditInjuryInfo',
                'uses' => 'DialogsInjuriesController@getEditInjuryInfo'
            )
        );
        Route::post('/injuries/info/post/{id}/', array(
                'as' => 'injuries-postEditInjuryInfo',
                'uses' => 'InjuriesController@postEditInjuryInfo'
            )
        );

        Route::get('/injuries/info/vehicle/edit/{id}/', array(
                'as' => 'injuries-getEditVehicle',
                'uses' => 'DialogsInjuriesController@getEditVehicle'
            )
        );
        Route::post('/injuries/info/vehicle/post/{id}/', array(
                'as' => 'injuries-postEditVehicle',
                'uses' => 'InjuriesController@postEditVehicle'
            )
        );
        Route::get('/injuries/info/vehicle-owner/edit/{id}/', array(
                'as' => 'injuries-getEditVehicleOwner',
                'uses' => 'DialogsInjuriesController@getEditVehicleOwner'
            )
        );
        Route::post('/injuries/info/vehicle-owner/post/{id}/', array(
                'as' => 'injuries-postEditVehicleOwner',
                'uses' => 'InjuriesController@postEditVehicleOwner'
            )
        );

        Route::get('/injuries/remarks-damage/edit/{id}/', array(
                'as' => 'injuries-getEditInjuryRemarks-damage',
                'uses' => 'DialogsInjuriesController@getEditInjuryRemarks_damage'
            )
        );
        Route::post('/injuries/remarks-damage/post/{id}/', array(
                'as' => 'injuries-postEditInjuryRemarks-damage',
                'uses' => 'InjuriesController@postEditInjuryRemarks_damage'
            )
        );

        Route::get('/injuries/branches-history/{id}', array(
                'uses' => 'DialogsInjuriesController@getInjuryBranchesHistory'
            )
        );

        Route::get('/injuries/add-branches-history/{id}', array(
                'uses' => 'DialogsInjuriesController@getAddInjuryBranchesHistory'
            )
        );
        Route::post('/injuries/store-branches-history/{id}/', array(
                'uses' => 'InjuriesController@postInjuryBranchesHistory'
            )
        );


        Route::post('/injuries/info/input/setAlert/{name}/{id}/{model}/{desc}', array(
            'as' => 'injuries.info.setAlert',
            'uses' => 'InjuriesCardController@setAlert'
        ));

        Route::post('/injuries/info/input/set_value/{id}/{element}/{model}/{desc}/{desc_array?}', array(
            'as' => 'injuries.info.setValue',
            'uses' => 'InjuriesCardController@setValue'
        ));

        Route::post('/injuries/info/search-buyer', array(
            'as' => 'injuries.info.search-buyer',
            'uses' => 'InjuriesCardController@searchBuyer'
        ));
        Route::post('/injuries/info/buyer-info', array(
            'as' => 'injuries.info.buyer-info',
            'uses' => 'InjuriesCardController@buyerInfo'
        ));
        Route::post('/injuries/info/set-buyer', array(
            'as' => 'injuries.info.set-buyer',
            'uses' => 'InjuriesCardController@setBuyer'
        ));

        //kartoteka wreck

        Route::post('/injuries/info/wreck/alert_repurchase_confirm/{id}', array(
            'as' => 'injuries.info.setAlert_repurchase_confirm',
            'uses' => 'InjuriesCardController@setAlert_repurchase_confirm'
        ));
        Route::post('/injuries/info/wreck/expire_tenderer_confirm/{id}', array(
            'as' => 'injuries.info.setAlert_expire_tenderer_confirm',
            'uses' => 'InjuriesCardController@setAlert_expire_tenderer_confirm'
        ));
        Route::post('/injuries/info/wreck/if_tenderer_confirm/{id}', array(
            'as' => 'injuries.info.setIf_tenderer_confirm',
            'uses' => 'InjuriesCardController@setIf_tenderer_confirm'
        ));
        Route::post('/injuries/info/wreck/alert_buyer_confirm/{id}', array(
            'as' => 'injuries.info.setAlert_buyer_confirm',
            'uses' => 'InjuriesCardController@setAlert_buyer_confirm'
        ));
        Route::post('/injuries/info/wreck/pro_forma_request_confirm/{id}', array(
            'as' => 'injuries.info.setPro_forma_request_confirm',
            'uses' => 'InjuriesCardController@setPro_forma_request_confirm'
        ));
        Route::post('/injuries/info/wreck/setPayment_confirm/{id}', array(
            'as' => 'injuries.info.setPayment_confirm',
            'uses' => 'InjuriesCardController@setPayment_confirm'
        ));
        Route::post('/injuries/info/wreck/setInvoice_request_confirm/{id}', array(
            'as' => 'injuries.info.setInvoice_request_confirm',
            'uses' => 'InjuriesCardController@setInvoice_request_confirm'
        ));
        Route::post('/injuries/info/wreck/cassation_receipt_confirm/{id}', array(
            'as' => 'injuries.info.setCassation_receipt_confirm',
            'uses' => 'InjuriesCardController@setCassation_receipt_confirm'
        ));
        Route::post('/injuries/info/wreck/off-register-vehicle-confirm/{id}', array(
            'as' => 'injuries.info.setOff_register_vehicle_confirm',
            'uses' => 'InjuriesCardController@setOff_register_vehicle_confirm'
        ));

        Route::post('/injuries/info/wreck/set-not-applicable/{id}', array(
            'as' => 'injuries.info.setNotApplicable',
            'uses' => 'InjuriesCardController@setNotApplicable'
        ));
        Route::post('/injuries/info/wreck/set-scrapped/{id}', array(
            'as' => 'injuries.info.setScrapped',
            'uses' => 'InjuriesCardController@setScrapped'
        ));
        Route::get('/injuries/info/wreck/new-offer/{id}', array(
            'as' => 'injuries.info.getNewOffer',
            'uses' => 'InjuriesCardController@getNewOffer'
        ));
        Route::post('/injuries/info/wreck/new-offer/{id}', array(
            'as' => 'injuries.info.postNewOffer',
            'uses' => 'InjuriesCardController@postNewOffer'
        ));

        Route::get('/injuries/info/wreck/pro_forma_request/{id}', array(
            'as' => 'injuries.info.getProFormaRequest',
            'uses' => 'InjuriesCardController@getProFormaRequest'
        ));
        Route::post('/injuries/info/wreck/pro_forma_request/{id}', array(
            'as' => 'injuries.info.postProFormaRequest',
            'uses' => 'InjuriesCardController@postProFormaRequest'
        ));
        Route::get('/injuries/info/wreck/invoice_request/{id}', array(
            'as' => 'injuries.info.getInvoiceRequest',
            'uses' => 'InjuriesCardController@getInvoiceRequest'
        ));
        Route::post('/injuries/info/wreck/invoice_request/{id}', array(
            'as' => 'injuries.info.postInvoiceRequest',
            'uses' => 'InjuriesCardController@postInvoiceRequest'
        ));
        Route::post('/injuries/info/wreck/dok_transfer/{id}', array(
            'as' => 'injuries.info.wreck.dok_transfer',
            'uses' => 'InjuriesCardController@wreckDokTransfer'
        ));


        //naprawa całkowita
        Route::post('/injuries/info/repair/setAlert_receive_confirm/{id}', array(
            'as' => 'injuries.info.setAlert_receive_confirm',
            'uses' => 'InjuriesCardTotalRepairController@setAlert_receive_confirm'
        ));

        Route::post('/injuries/info/repair/setAcceptation/{id}/{id_acceptation}', array(
            'as' => 'injuries.info.repair.setAcceptation',
            'uses' => 'InjuriesCardTotalRepairController@setAcceptation'
        ));

        Route::post('/injuries/info/repair/sendToDok/{id}', array(
            'as' => 'injuries.info.repair.sendToDok',
            'uses' => 'InjuriesCardTotalRepairController@sendToDok'
        ));

        //kartoteka kradzież

        Route::post('/injuries/info/theft/start_processing/{id}', array(
            'as' => 'injuries.info.theft.startProcessing',
            'uses' => 'InjuriesTheftController@startProcessing'
        ));
        Route::post('/injuries/info/theft/send_zu_confirm/{id}', array(
            'as' => 'injuries.info.setSend_zu_confirm',
            'uses' => 'InjuriesTheftController@setSend_zu_confirm'
        ));
        Route::post('/injuries/info/theft/police_memo_confirm/{id}', array(
            'as' => 'injuries.info.setPolice_memo_confirm',
            'uses' => 'InjuriesTheftController@setPolice_memo_confirm'
        ));
        Route::post('/injuries/info/theft/docs_receive_confirm/{id}', array(
            'as' => 'injuries.info.setDocs_receive_confirm',
            'uses' => 'InjuriesTheftController@setDocs_receive_confirm'
        ));
        Route::post('/injuries/info/theft/setAcceptation/{id}/{id_acceptation}', array(
            'as' => 'injuries.info.theft.setAcceptation',
            'uses' => 'InjuriesTheftController@setAcceptation'
        ));
        Route::post('/injuries/info/theft/setAcceptationParam/{id}/{id_acceptation}/{param}', array(
            'as' => 'injuries.info.theft.setAcceptationParam',
            'uses' => 'InjuriesTheftController@setAcceptationParam'
        ));
        Route::post('/injuries/info/theft/rollbackAcceptation/{id}/{id_acceptation}', array(
            'as' => 'injuries.info.theft.rollbackAcceptation',
            'uses' => 'InjuriesTheftController@rollbackAcceptation'
        ));
        Route::post('/injuries/info/theft/redemption_investigation_confirm/{id}', array(
            'as' => 'injuries.info.setRedemption_investigation_confirm',
            'uses' => 'InjuriesTheftController@setRedemption_investigation_confirm'
        ));
        Route::post('/injuries/info/theft/deregistration_vehicle_confirm/{id}', array(
            'as' => 'injuries.info.setDeregistration_vehicle_confirm',
            'uses' => 'InjuriesTheftController@setDeregistration_vehicle_confirm'
        ));
        Route::post('/injuries/info/theft/compensation_payment_confirm/{id}', array(
            'as' => 'injuries.info.setCompensation_payment_confirm',
            'uses' => 'InjuriesTheftController@setCompensation_payment_confirm'
        ));
        Route::post('/injuries/info/theft/compensation_payment_deny/{id}', array(
            'as' => 'injuries.info.setCompensation_payment_deny',
            'uses' => 'InjuriesTheftController@setCompensation_payment_deny'
        ));
        Route::post('/injuries/info/theft/gap_confirm/{id}', array(
            'as' => 'injuries.info.setGap_confirm',
            'uses' => 'InjuriesTheftController@setGap_confirm'
        ));
        Route::post('/injuries/info/theft/sendToDok/{id}', array(
            'as' => 'injuries.info.theft.sendToDok',
            'uses' => 'InjuriesTheftController@sendToDok'
        ));
        Route::post('/injuries/info/theft/setPunishable/{id}', array(
            'as' => 'injuries.info.setPunishable',
            'uses' => 'InjuriesTheftController@setPunishable'
        ));


        Route::get('/injuries/download-img/{id}', array(
                'as' => 'injuries-downloadImg',
                'uses' => 'InjuriesDocsController@downloadImg'
            )
        );

        Route::get('/injuries/unlock/{id}/', array(
                'as' => 'injuries-unlock',
                'uses' => 'DialogsInjuriesController@getUnlock'
            )
        );
        Route::post('/injuries/unlock/{id}/', array(
                'as' => 'injuries-setUnlock',
                'uses' => 'InjuriesController@setUnlock'
            )
        );

        Route::get('/injuries/lock/{id}/', array(
                'as' => 'injuries-lock',
                'uses' => 'DialogsInjuriesController@getLock'
            )
        );
        Route::post('/injuries/lock/{id}/', array(
                'as' => 'injuries-setLock',
                'uses' => 'InjuriesController@setLock'
            )
        );

        Route::post('/injuries/update-syjon-client/{id}/', array(
                'uses' => 'InjuriesController@updateSyjonClient'
            )
        );

        //set injury task

        Route::get('/injuries/create-cession-amounts/{id}/', array(
                'as' => 'injuries-createCessionAmounts',
                'uses' => 'DialogsInjuriesController@createCessionAmounts'
            )
        );
        Route::post('/injuries/create-cession-amounts/{id}', array(
                'as' => 'injuries-storeCessionAmounts',
                'uses' => 'InjuriesController@storeCessionAmounts'
            )
        );
        Route::get('/injuries/edit-cession-amounts/{id}/', array(
                'as' => 'injuries-editCessionAmounts',
                'uses' => 'DialogsInjuriesController@editCessionAmounts'
            )
        );
        Route::post('/injuries/edit-cession-amounts/{id}', array(
                'as' => 'injuries-updateCessionAmounts',
                'uses' => 'InjuriesController@updateCessionAmounts'
            )
        );

        Route::get('/injuries/del-image/{id}/', array(
                'as' => 'injuries-getDelImage',
                'uses' => 'DialogsInjuriesController@getDelImage'
            )
        );

        Route::post('/injuries/del-image/{id}/', array(
                'as' => 'injuries-setDelImage',
                'uses' => 'InjuriesDocsController@setDelImage'
            )
        );

        Route::get('/injuries/del-doc/{id}/', array(
                'as' => 'injuries-getDelDoc',
                'uses' => 'DialogsInjuriesController@getDelDoc'
            )
        );

        Route::get('/injuries/del-doc-conf/{id}/', array(
                'as' => 'injuries-getDelDocConf',
                'uses' => 'DialogsInjuriesController@getDelDocConf'
            )
        );

        Route::post('/injuries/del-doc/{id}/', array(
                'as' => 'injuries-setDelDoc',
                'uses' => 'InjuriesDocsController@setDelDoc'
            )
        );

        Route::post('/injuries/del-doc-conf/{id}/', array(
                'as' => 'injuries-setDelDocConf',
                'uses' => 'InjuriesDocsController@setDelDocConf'
            )
        );

        Route::get('/injuries/change-contact/{id}/', array(
                'as' => 'injuries-getChangeContact',
                'uses' => 'DialogsInjuriesController@getChangeContact'
            )
        );
        Route::post('/injuries/change-contact/{id}/', array(
                'as' => 'injuries-setChangeContact',
                'uses' => 'InjuriesController@setChangeContact'
            )
        );

        Route::get('/injuries/edit-invoice/{id}/', array(
                'as' => 'injuries-getEditInvoice',
                'uses' => 'DialogsInjuriesController@getEditInvoice'
            )
        );
        Route::post('/injuries/edit-invoice/{id}/', array(
                'as' => 'injuries-setInvoice',
                'uses' => 'InjuriesController@setInvoice'
            )
        );
        Route::post('/injuries/get-invoice-commission/{id}/', array(
                'as' => 'injuries-getInvoiceCommission',
                'uses' => 'InjuriesController@getInvoiceCommission'
            )
        );
        Route::get('/injuries/get-invoice-bank-accounts/{id}/{withTrashed}', array(
            'as' => 'injuries-getInvoiceBankAccounts',
            'uses' => 'InjuriesController@getInvoiceBankAccounts'
        ));
        Route::get('/injuries/delete-invoice/{id}/', array(
                'as' => 'injuries-getDeleteInvoice',
                'uses' => 'DialogsInjuriesController@getDeleteInvoice'
            )
        );

        Route::post('/injuries/delete-invoice/{id}/', array(
                'as' => 'injuries-setDeleteInvoice',
                'uses' => 'InjuriesController@setDeleteInvoice'
            )
        );

        Route::get('/injuries/edit-compensation/{id}/', array(
                'as' => 'injuries-getEditCompensation',
                'uses' => 'DialogsInjuriesController@getEditCompensation'
            )
        );
        Route::post('/injuries/edit-compensation/{id}/', array(
                'as' => 'injuries-setCompensation',
                'uses' => 'InjuriesController@setCompensation'
            )
        );

        Route::get('/injuries/delete-compensation/{id}/', array(
                'as' => 'injuries-getDeleteCompensation',
                'uses' => 'DialogsInjuriesController@getDeleteCompensation'
            )
        );

        Route::post('/injuries/delete-compensation/{id}/', array(
                'as' => 'injuries-setDeleteCompensation',
                'uses' => 'InjuriesController@setDeleteCompensation'
            )
        );

        Route::get('/injuries/edit-estimate/{id}/', array(
                'as' => 'injuries-getEditEstimate',
                'uses' => 'DialogsInjuriesController@getEditEstimate'
            )
        );
        Route::post('/injuries/edit-estimate/{id}/', array(
                'as' => 'injuries-setEstimate',
                'uses' => 'InjuriesController@setEstimate'
            )
        );

        Route::get('/injuries/delete-estimate/{id}/', array(
                'as' => 'injuries-getDeleteEstimate',
                'uses' => 'DialogsInjuriesController@getDeleteEstimate'
            )
        );

        Route::post('/injuries/delete-estimate/{id}/', array(
                'as' => 'injuries-setDeleteEstimate',
                'uses' => 'InjuriesController@setDeleteEstimate'
            )
        );


        Route::get('/injuries/edit/{id}/', array(
                'as' => 'injuries-getEditInjury',
                'uses' => 'DialogsInjuriesController@getEditInjury'
            )
        );
        Route::post('/injuries/edit/{id}/', array(
                'as' => 'injuries-setEditInjury',
                'uses' => 'InjuriesController@setEditInjury'
            )
        );

        Route::post('injuries/register-sap/{injury_id}', [
                'uses' => 'InjuriesCardController@postRegisterSap'
            ]);

        Route::post('injuries/update-sap/{injury_id}',
            [
                'uses' => 'InjuriesCardController@postUpdateSap'
            ]);

        Route::post('injuries/send-to-sap/{injury_id}',
            [
                'uses' => 'InjuriesCardController@postSendToSap'
            ]);

        Route::post('injuries/sync-sap-premiums/{injury_id}',
            [
                'uses' => 'InjuriesCardController@postSyncSapPremiums'
            ]);

        Route::get('/injuries/delete-premium/{id}/', array(
                'uses' => 'DialogsInjuriesController@getDeletePremium'
            )
        );
        Route::post('/injuries/remove-premium/{id}/', array(
                'uses' => 'InjuriesController@setRemovePremium'
            )
        );

        Route::get('/injuries/edit-insurance/{id}/', array(
                'as' => 'injuries-getEditInjuryInsurance',
                'uses' => 'DialogsInjuriesController@getEditInjuryInsurance'
            )
        );
        Route::post('/injuries/edit-insurance/{id}/', array(
                'as' => 'injuries-setEditInjuryInsurance',
                'uses' => 'InjuriesController@setEditInjuryInsurance'
            )
        );

        Route::get('/injuries/edit-gap/{id}/', array(
                'uses' => 'DialogsInjuriesController@getEditInjuryGap'
            )
        );
        Route::post('/injuries/edit-gap/{id}/', array(
                'uses' => 'InjuriesController@setEditInjuryGap'
            )
        );

        Route::get('/injuries/edit-driver/{id}/', array(
                'as' => 'injuries-getEditInjuryDriver',
                'uses' => 'DialogsInjuriesController@getEditInjuryDriver'
            )
        );
        Route::post('/injuries/edit-driver/{id}/', array(
                'as' => 'injuries-setEditInjuryDriver',
                'uses' => 'InjuriesController@setEditInjuryDriver'
            )
        );

        Route::get('/injuries/edit-offender/{id}/', array(
                'as' => 'injuries-getEditInjuryOffender',
                'uses' => 'DialogsInjuriesController@getEditInjuryOffender'
            )
        );
        Route::post('/injuries/edit-offender/{id}/', array(
                'as' => 'injuries-setEditInjuryOffender',
                'uses' => 'InjuriesController@setEditInjuryOffender'
            )
        );

        Route::get('/injuries/edit-notifier/{id}/', array(
                'as' => 'injuries-getEditInjuryNotifier',
                'uses' => 'DialogsInjuriesController@getEditInjuryNotifier'
            )
        );
        Route::post('/injuries/edit-notifier/{id}/', array(
                'as' => 'injuries-setEditInjuryNotifier',
                'uses' => 'InjuriesController@setEditInjuryNotifier'
            )
        );

        Route::get('/injuries/edit-clientContact/{id}/', array(
                'as' => 'injuries-getEditInjuryClientContact',
                'uses' => 'DialogsInjuriesController@getEditInjuryClientContact'
            )
        );
        Route::post('/injuries/edit-clientContact/{id}/', array(
                'as' => 'injuries-setEditInjuryClientContact',
                'uses' => 'InjuriesController@setEditInjuryClientContact'
            )
        );

        Route::get('/injuries/edit-client/{id}/', array(
                'as' => 'injuries-getEditInjuryClient',
                'uses' => 'DialogsInjuriesController@getEditInjuryClient'
            )
        );
        Route::post('/injuries/edit-client/{id}/', array(
                'as' => 'injuries-setEditInjuryClient',
                'uses' => 'InjuriesController@setEditInjuryClient'
            )
        );

        Route::get('/injuries/edit-map/{id}/', array(
                'as' => 'injuries-getEditInjuryMap',
                'uses' => 'DialogsInjuriesController@getEditInjuryMap'
            )
        );

        Route::post('/injuries/edit-map/{id}/', array(
                'as' => 'injuries-setEditInjuryMap',
                'uses' => 'InjuriesController@setEditInjuryMap'
            )
        );


        Route::get('/injuries/date-admission/{id}/', array(
                'as' => 'injuries-getDateAdmission',
                'uses' => 'DialogsInjuriesController@getDateAdmission'
            )
        );
        Route::post('/injuries/date-admission/{id}/', array(
                'as' => 'injuries-setDateAdmission',
                'uses' => 'InjuriesController@setDateAdmission'
            )
        );

        Route::post('/session/set/search', array(
                'as' => 'session.setSearch',
                'uses' => 'SessionController@setSearch'
            )
        );


        Route::get('/injuries/docs/send/{id}/', array(
                'as' => 'injuries-docs-send-dialog',
                'uses' => 'InjuriesDocsController@getSendDocs'
            )
        );
        Route::post('/injuries/docs/send/{id}/', array(
                'as' => 'injuries-docs-send',
                'uses' => 'InjuriesDocsController@postSendDocs'
            )
        );

        
        Route::get('/injuries/docs/generate-v-desk-text/view/{id}', array(
            'as' => 'injuries-getGenerateVDeskTextView',
            'uses' => 'InjuriesDocsController@getGenerateVDeskTextView'
        ));

        Route::get('/injuries/docs/generate-v-desk-text/{id}/{amount}', array(
            'as' => 'injuries-getGenerateVDeskText',
            'uses' => 'InjuriesDocsController@getGenerateVDeskText'
        ));

        Route::post('/injuries/document/{id}/', array(
                'as' => 'injuries-post-document',
                'uses' => 'InjuriesDocsController@postDocument'
            )
        );

        Route::post('/injuries/dialog-document', array(
                'as' => 'injuries-getDocumentSet',
                'uses' => 'DialogsInjuriesController@getDocumentSet'
            )
        );
        Route::post('/injuries/set-document', array(
                'as' => 'injuries-setDocumentSet',
                'uses' => 'InjuriesDocsController@setDocumentSet'
            )
        );

        Route::get('/injuries/document/match-sap/{id}', array(
                'uses' => 'DialogsInjuriesController@getDocumentMatchSap'
            )
        );
        Route::post('/injuries/document/save-match-sap/{id}', array(
                'uses' => 'InjuriesDocsController@setDocumentMatchSap'
            )
        );


        Route::post('/injuries/set-document-del', array(
                'as' => 'injuries-setDocumentDel',
                'uses' => 'InjuriesDocsController@setDocumentDel'
            )
        );
        Route::post('/injuries/chat/checkConversation/', array(
                'as' => 'chat.checkConversation',
                'uses' => 'ChatController@checkConversation'
            )
        );
        Route::post('/injuries/info/wreck/calc_balance/{id}', array(
            'as' => 'injuries.info.wreck.calc_balance',
            'uses' => 'InjuriesCardController@wreckCalcBalance'
        ));

        Route::get('/injuries/download-doc/{id}', array(
                'as' => 'injuries-downloadDoc',
                'uses' => 'InjuriesDocsController@downloadDoc'
            )
        );

        Route::get('/injuries/preview-doc/{id}/{type?}', array(
                'uses' => 'InjuriesDocsController@previewDoc'
            )
        );

        Route::get('/injuries/dialog/preview-doc/{id}/{type?}', array(
                'uses' => 'DialogsInjuriesController@previewDoc'
            )
        );

        Route::get('/injuries/download-generate-doc/{id}', array(
                'as' => 'injuries-downloadGenerateDoc',
                'uses' => 'InjuriesDocsController@downloadGenerateDoc'
            )
        );
        Route::get('/injuries/download-docs/{id}', array(
                'as' => 'injuries-downloadDocs',
                'uses' => 'InjuriesDocsController@downloadDocs'
            )
        );

        Route::post('/injuries/registration-getList', array(
                'as' => 'vehicle-registration-getList',
                'uses' => 'InjuriesController@getVehicleRegistrationList'
            )
        );

        Route::get('/injuries/damages/{id}/', array(
                'as' => 'injuries-getDamages',
                'uses' => 'DialogsInjuriesController@getDamages'
            )
        );

        Route::get('/injuries/prevPictures/{id}/', array(
                'as' => 'injuries-getUploadesPictures',
                'uses' => 'DialogsInjuriesController@getUploadesPictures'
            )
        );

        Route::get('/injuries/unprocessed/print/{id}', array(
                'as' => 'injuries.unprocessed.print',
                'uses' => 'InjuriesUnprocessedController@generate'
            )
        );

        Route::post('/injuries/unprocessed/download-images/{id}', array(
                'uses' => 'InjuriesUnprocessedController@downloadUnprocessedImages'
            )
        );


        Route::post('/injuries/get/search', array(
                'as' => 'injuries.getSearch',
                'uses' => 'InjuriesController@getSearch'
            )
        );

        Route::get('/injuries/reset-filters', array(
                'uses' => 'InjuriesController@getResetFilters'
            )
        );

        Route::get('/injuries/search/global', array(
                'as' => 'injuries-search-getAll',
                'uses' => 'InjuriesController@getSearchGlobal'
            )
        );

        Route::get('/injuries/search/global-unprocessed', array(
                'as' => 'injuries-search-getAllUnprocessed',
                'uses' => 'InjuriesController@getSearchGlobalUnprocessed'
            )
        );

        Route::get('/injuries/search/redirect/{id}/', array(
            'as' => 'injuries.global.redirect',
            'uses' => 'InjuriesController@getSearchGlobalRedirect'
        ));

        Route::get('/injuries/search/expired/{user?}', array(
                'as' => 'injuries-search-expired',
                'uses' => 'InjuriesController@getIndexTasksExpired'
            )
        );

        Route::get('/injuries/search/today/{user?}', array(
                'as' => 'injuries-search-today',
                'uses' => 'InjuriesController@getIndexTasksToday'
            )
        );

        Route::get('/injuries/unprocessed', array(
                'as' => 'injuries-unprocessed',
                'uses' => 'InjuriesController@getIndexUnprocessed'
            )
        );

        Route::get('/injuries/ea/{name_key?}', array(
                'uses' => 'InjuriesController@getIndexEa'
            )
        );

        Route::get('/injuries/pop', array(
                'uses' => 'InjuriesController@getIndexPop'
            )
        );

        Route::get('/injuries/new', array(
                'as' => 'injuries-new',
                'uses' => 'InjuriesController@getIndexNew'
            )
        );

        Route::get('/injuries/inprogress', array(
                'as' => 'injuries-inprogress',
                'uses' => 'InjuriesController@getIndexInprogress'
            )
        );

        Route::get('/injuries/completed', array(
                'as' => 'injuries-completed',
                'uses' => 'InjuriesController@getIndexCompleted'
            )
        );

        Route::get('/injuries/refused', array(
                'as' => 'injuries-refused',
                'uses' => 'InjuriesController@getIndexRefused'
            )
        );

        Route::get('/injuries/total', array(
                'as' => 'injuries-total',
                'uses' => 'InjuriesController@getIndexTotal'
            )
        );

        Route::get('/injuries/total-finished', array(
                'as' => 'injuries-total-finished',
                'uses' => 'InjuriesController@getIndexTotalFinished'
            )
        );

        Route::get('/injuries/total/changeStatus/{id}', array(
            'as' => 'injuries.total.getChangeStatus',
            'uses' => 'DialogsInjuriesController@getChangeStatus'
        ));

        Route::post('/injuries/total/changeStatus/{id}/{status_id}', array(
            'as' => 'injuries.total.setStatus',
            'uses' => 'InjuriesController@setTotalStatus'
        ));

        Route::get('/injuries/theft', array(
                'as' => 'injuries-theft',
                'uses' => 'InjuriesController@getIndexTheft'
            )
        );

        Route::get('/injuries/canceled', array(
                'as' => 'injuries-canceled',
                'uses' => 'InjuriesController@getIndexCanceled'
            )
        );

        Route::get('/injuries/deleted', array(
                'as' => 'injuries-deleted',
                'uses' => 'InjuriesController@getIndexDeleted'
            )
        );

        Route::get('/injuries/info/{id}/', array(
                'as' => 'injuries-info',
                'uses' => 'InjuriesController@getInfo'
            )
        );
        Route::post('/injuries/info/{id}/', array(
                'uses' => 'InjuriesController@getInfo'
            )
        );
        Route::post('/injuries/info/check-contract-status/{injury_id}', [
            'uses' => 'InjuriesCardController@checkContractStatus'
        ]);

        Route::get('/injuries/card/load-branch-data', [
            'uses' => 'InjuriesCardController@loadBranchData'
        ]);

        Route::get('/injuries/create', array(
                'as' => 'injuries-create',
                'uses' => 'InjuriesController@getCreate'
                //'uses' => 'InjuriesCreateController@index'
            )
        );

        Route::get('/injuries/vb/get/{action}/{id?}/{value?}/{param?}/', array(
                'as' => 'injuries.vb.get',
                function ($action, $id = null, $value = null, $param = null) {
                    if (!is_null($param))
                        return App::make('InjuriesVbController')->{$action}($id, $value, $param);
                    if (!is_null($value))
                        return App::make('InjuriesVbController')->{$action}($id, $value);

                    return App::make('InjuriesVbController')->{$action}($id);
                }
            )
        );
        Route::post('/injuries/vb/post/{action}/{id?}', array(
                'as' => 'injuries.vb.post',
                function ($action, $id = null) {
                    return App::make('InjuriesVbController')->{$action}($id);
                }
            )
        );

        Route::controller('injuries/make', 'InjuriesCreateController');

        Route::get('/injuries/create-i', array(
                'as' => 'injuries-create-i',
                'uses' => 'InjuriesInfoliniaController@getCreate'
            )
        );


        //dos pozostałe

        Route::get('/dos/other/injuries/unprocessed', array(
                'as' => 'dos.other.injuries.unprocessed',
                'uses' => 'DosOtherInjuriesController@getUnprocessed'
            )
        );

        Route::get('/dos/other/injuries/new', array(
                'as' => 'dos.other.injuries.new',
                'uses' => 'DosOtherInjuriesController@getNew'
            )
        );

        Route::get('/dos/other/injuries/inprogress', array(
                'as' => 'dos.other.injuries.inprogress',
                'uses' => 'DosOtherInjuriesController@getInprogress'
            )
        );

        Route::get('/dos/other/injuries/completed', array(
                'as' => 'dos.other.injuries.completed',
                'uses' => 'DosOtherInjuriesController@getCompleted'
            )
        );

        Route::get('/dos/other/injuries/canceled', array(
                'as' => 'dos.other.injuries.canceled',
                'uses' => 'DosOtherInjuriesController@getCanceled'
            )
        );

        Route::get('/dos/other/injuries/deleted', array(
                'as' => 'dos.other.injuries.deleted',
                'uses' => 'DosOtherInjuriesController@getDeleted'
            )
        );

        Route::get('/dos/other/injuries/total', array(
                'as' => 'dos.other.injuries.total',
                'uses' => 'DosOtherInjuriesController@getTotal'
            )
        );

        Route::get('/dos/other/injuries/theft', array(
                'uses' => 'DosOtherInjuriesController@getTheft'
            )
        );

        Route::get('/dos/other/injuries/total-finished', array(
                'as' => 'dos.other.injuries.total-finished',
                'uses' => 'DosOtherInjuriesController@getTotalFinished'
            )
        );

        Route::get('/dos/other/injuries/theft-finished', array(
                'as' => 'dos.other.injuries.theft-finished',
                'uses' => 'DosOtherInjuriesController@getTheft'
            )
        );

        Route::get('/dos/other/injuries/ppi', array(
                'as' => 'dos.other.injuries.ppi',
                'uses' => 'DosOtherInjuriesController@getPpi'
            )
        );

        Route::get('/dos/other/injuries/search/expired', array(
                'as' => 'dos.other.injuries.search.expired',
                'uses' => 'DosOtherInjuriesController@getTasksExpired'
            )
        );

        Route::post('/dos/other/injuries/get/search', array(
                'as' => 'dos.other.injuries.getSearch',
                'uses' => 'DosOtherInjuriesController@getSearch'
            )
        );

        Route::get('/dos/other/injuries/search/global', array(
                'as' => 'dos.other.injuries.search.getAll',
                'uses' => 'DosOtherInjuriesController@getSearchGlobal'
            )
        );

        Route::get('/dos/other/injuries/search/today', array(
                'as' => 'dos.other.injuries.search.today',
                'uses' => 'DosOtherInjuriesController@getTasksToday'
            )
        );

        Route::get('/dos/injuries/get/{action}/{id}/', array(
                'as' => 'dos.other.injuries.get',
                function ($action, $id) {
                    return App::make('DosOtherInjuriesCardDialogsController')->{$action}($id);
                }
            )
        );


        Route::post('/dos/injuries/set/{action}/{id}', array(
                'as' => 'dos.other.injuries.set',
                function ($action, $id) {
                    return App::make('DosOtherInjuriesController')->{$action}($id);
                }
            )
        );


        Route::get('/dos/other/injuries/info/{id}/', array(
                'as' => 'dos.other.injuries.info',
                'uses' => 'DosOtherInjuriesCardController@index'
            )
        );

        //info card
        Route::get('/dos/injuries/edit/{id}/', array(
                'as' => 'dos.other.injuries.getEditInjury',
                'uses' => 'DosOtherInjuriesCardDialogsController@getEditInjury'
            )
        );
        Route::get('/dos/other/injuries/info/preview-doc/{id}/', array(
                'uses' => 'DosOtherInjuriesCardController@previewDoc'
            )
        );
        Route::post('/dos/other/injuries/info/update-compensation/{id}/', array(
                'uses' => 'DosOtherInjuriesCardController@updateCompensation'
            )
        );
        Route::post('/dos/other/injuries/info/delete-compensation/{id}/', array(
                'uses' => 'DosOtherInjuriesCardController@deleteCompensation'
            )
        );
        Route::post('/dos/injuries/edit/{id}/', array(
                'as' => 'dos.other.injuries.setEditInjury',
                'uses' => 'DosOtherInjuriesCardController@setEditInjury'
            )
        );
        Route::get('/dos/injuries/edit/insurance/{id}/', array(
                'as' => 'dos.other.injuries.getEditInjuryInsurance',
                'uses' => 'DosOtherInjuriesCardDialogsController@getEditInjuryInsurance'
            )
        );
        Route::post('/dos/injuries/edit/insurance/{id}/', array(
                'as' => 'dos.other.injuries.setEditInjuryInsurance',
                'uses' => 'DosOtherInjuriesCardController@setEditInjuryInsurance'
            )
        );
        Route::get('/dos/injuries/edit/clientContact/{id}/', array(
                'as' => 'dos.other.injuries.getEditInjuryClientContact',
                'uses' => 'DosOtherInjuriesCardDialogsController@getEditInjuryClientContact'
            )
        );
        Route::post('/dos/injuries/edit/clientContact/{id}/', array(
                'as' => 'dos.other.injuries.setEditInjuryClientContact',
                'uses' => 'DosOtherInjuriesCardController@setEditInjuryClientContact'
            )
        );
        Route::get('/dos/injuries/info/edit/{id}/', array(
                'as' => 'dos.other.injuries.getEditInjuryInfo',
                'uses' => 'DosOtherInjuriesCardDialogsController@getEditInjuryInfo'
            )
        );
        Route::post('/dos/injuries/info/post/{id}/', array(
                'as' => 'dos.other.injuries.postEditInjuryInfo',
                'uses' => 'DosOtherInjuriesCardController@postEditInjuryInfo'
            )
        );
        Route::get('/dos/injuries/edit-map/{id}/', array(
                'as' => 'dos.other.injuries.getEditInjuryMap',
                'uses' => 'DosOtherInjuriesCardDialogsController@getEditInjuryMap'
            )
        );
        Route::post('/dos/injuries/edit-map/{id}/', array(
                'as' => 'dos.other.injuries.setEditInjuryMap',
                'uses' => 'DosOtherInjuriesCardController@setEditInjuryMap'
            )
        );

        Route::get('/dos/injuries/generate-docs-info/{id}/{key}/', array(
                'as' => 'dos.other.injuries.generate.docs',
                'uses' => 'DosOtherInjuriesDocsController@getGenerateDocs'
            )
        );

        Route::post('/dos/generate/{id}/{document_type_id}', array(
                'as' => 'dos.other.injuries.get-doc-generate',
                'uses' => 'DosOtherInjuriesDocsController@generateDoc'
            )
        );
        Route::post('/dos/injuries/document/{id}/', array(
                'as' => 'dos.other.injuries.post-document',
                'uses' => 'DosOtherInjuriesDocsController@postDocument'
            )
        );
        Route::post('/dos/injuries/dialog-document', array(
                'as' => 'dos.other.injuries.getDocumentSet',
                'uses' => 'DosOtherInjuriesCardDialogsController@getDocumentSet'
            )
        );
        Route::post('/dos/injuries/set-document', array(
                'as' => 'dos.other.injuries.setDocumentSet',
                'uses' => 'DosOtherInjuriesDocsController@setDocumentSet'
            )
        );
        Route::post('/dos/injuries/set-document-del', array(
                'as' => 'dos.other.injuries.setDocumentDel',
                'uses' => 'DosOtherInjuriesDocsController@setDocumentDel'
            )
        );

        Route::get('/dos/injuries/download-generate-doc/{id}', array(
                'as' => 'dos.other.injuries.downloadGenerateDoc',
                'uses' => 'DosOtherInjuriesDocsController@downloadGenerateDoc'
            )
        );
        Route::get('/dos/injuries/download-img/{id}', array(
                'as' => 'dos.other.injuries.downloadImg',
                'uses' => 'DosOtherInjuriesDocsController@downloadImg'
            )
        );
        Route::get('/dos/injuries/download-doc/{id}', array(
                'as' => 'dos.other.injuries.downloadDoc',
                'uses' => 'DosOtherInjuriesDocsController@downloadDoc'
            )
        );
        Route::get('/dos/injuries/del-doc/{id}/', array(
                'as' => 'dos.other.injuries.getDelDoc',
                'uses' => 'DosOtherInjuriesCardDialogsController@getDelDoc'
            )
        );

        Route::get('/dos/injuries/del-doc-conf/{id}/', array(
                'as' => 'dos.other.injuries.getDelDocConf',
                'uses' => 'DosOtherInjuriesCardDialogsController@getDelDocConf'
            )
        );

        Route::post('/dos/injuries/del-doc/{id}/', array(
                'as' => 'dos.other.injuries.setDelDoc',
                'uses' => 'DosOtherInjuriesDocsController@setDelDoc'
            )
        );

        Route::post('/dos/injuries/del-doc-conf/{id}/', array(
                'as' => 'dos.other.injuries.setDelDocConf',
                'uses' => 'DosOtherInjuriesDocsController@setDelDocConf'
            )
        );

        Route::post('/dos/injuries/images/{id}/{key}/', array(
                'as' => 'dos.other.injuries.post-image',
                'uses' => 'DosOtherInjuriesDocsController@postImage'
            )
        );
        Route::post('/dos/injuries/del-image/{id}/', array(
                'as' => 'dos.other.injuries.setDelImage',
                'uses' => 'DosOtherInjuriesDocsController@setDelImage'
            )
        );

        Route::get('/dos/injuries/edit-invoice/{id}/', array(
                'as' => 'dos.other.injuries.getEditInvoice',
                'uses' => 'DosOtherInjuriesCardDialogsController@getEditInvoice'
            )
        );
        Route::post('/dos/injuries/edit-invoice/{id}/', array(
                'as' => 'dos.other.injuries.setInvoice',
                'uses' => 'DosOtherInjuriesCardController@setInvoice'
            )
        );
        Route::get('/dos/injuries/del-image/{id}/', array(
                'as' => 'dos.other.injuries.getDelImage',
                'uses' => 'DosOtherInjuriesCardDialogsController@getDelImage'
            )
        );

        Route::get('/dos/injuries/history/add/{id}/', array(
                'as' => 'dos.other.injuries.add-history',
                'uses' => 'DosOtherInjuriesCardDialogsController@getAddHistory'
            )
        );
        Route::post('/dos/injuries/history/create/{id}/', array(
                'as' => 'dos.other.injuries.create-history',
                'uses' => 'DosOtherInjuriesCardController@createHistory'
            )
        );
        Route::post('/dos/injuries/send-sms/{id}/', array(
                'uses' => 'DosOtherInjuriesCardController@sendSms'
            )
        );
        Route::get('/dos/injuries/edit-injury-offender/{id}/', array(
                'as' => 'dos.other.injuries.getEditInjuryOffender',
                'uses' => 'DosOtherInjuriesCardDialogsController@getEditInjuryOffender'
            )
        );

        Route::post('/dos/injuries/edit-injury-offender/{id}/', array(
                'as' => 'dos.other.injuries.setEditInjuryOffender',
                'uses' => 'DosOtherInjuriesDocsController@setEditInjuryOffender'
            )
        );

        //chat
        Route::get('/dos/injuries/{id}/chat/create/', array(
                'as' => 'dos.other.chat.create',
                'uses' => 'DosOtherInjuriesChatController@create'
            )
        );

        Route::post('/dos/injuries/{id}/chat/post/', array(
                'as' => 'dos.other.chat.post',
                'uses' => 'DosOtherInjuriesChatController@post'
            )
        );

        Route::get('/dos/injuries/chat/{id}/replay/', array(
                'as' => 'dos.other.chat.replay',
                'uses' => 'DosOtherInjuriesChatController@replay'
            )
        );

        Route::post('/dos/injuries/chat/{id}/replay/', array(
                'as' => 'dos.other.chat.postReplay',
                'uses' => 'DosOtherInjuriesChatController@postReplay'
            )
        );

        Route::get('/dos/injuries/chat/{id}/deadline/', array(
                'as' => 'dos.other.chat.deadline',
                'uses' => 'DosOtherInjuriesChatController@deadline'
            )
        );

        Route::post('/dos/injuries/chat/{id}/deadline/', array(
                'as' => 'dos.other.chat.postDeadline',
                'uses' => 'DosOtherInjuriesChatController@postDeadline'
            )
        );

        Route::post('/dos/injuries/chat/checkConversation/', array(
                'as' => 'dos.other.chat.checkConversation',
                'uses' => 'DosOtherInjuriesChatController@checkConversation'
            )
        );

        Route::get('/dos/injuries/chat/{id}/close/', array(
                'as' => 'dos.other.chat.close',
                'uses' => 'DosOtherInjuriesChatController@close'
            )
        );

        Route::post('/dos/injuries/chat/{id}/close/', array(
                'as' => 'dos.other.chat.postClose',
                'uses' => 'DosOtherInjuriesChatController@postClose'
            )
        );

        Route::get('/dos/injuries/chat/{id}/accept/', array(
                'as' => 'dos.other.chat.accept',
                'uses' => 'DosOtherInjuriesChatController@accept'
            )
        );

        Route::post('/dos/injuries/chat/{id}/accept/', array(
                'as' => 'dos.other.chat.postAccept',
                'uses' => 'DosOtherInjuriesChatController@postAccept'
            )
        );

        Route::get('/dos/injuries/chat/{id}/removeDeadline/', array(
                'as' => 'dos.other.chat.removeDeadline',
                'uses' => 'DosOtherInjuriesChatController@removeDeadline'
            )
        );

        Route::post('/dos/injuries/chat/{id}/removeDeadline/', array(
                'as' => 'dos.other.chat.postRemoveDeadline',
                'uses' => 'DosOtherInjuriesChatController@postRemoveDeadline'
            )
        );
        // ---chat

        //kradziez
        Route::post('/dos/other/injuries/info/input/setAlert/{name}/{id}/{model}/{desc}', array(
            'as' => 'dos.other.injuries.info.setAlert',
            'uses' => 'DosOtherInjuriesCardController@setAlert'
        ));

        Route::get('/dos/injuries/theft/{action}/{id}/{option?}', array(
                'as' => 'dos.other.injuries.theft',
                function ($action, $id, $option = null) {
                    if ($option == null)
                        return App::make('DosOtherInjuriesTheftController')->{$action}($id);
                    else
                        return App::make('DosOtherInjuriesTheftController')->{$action}($id, $option);
                }
            )
        );


        Route::post('/dos/injuries/theft/{action}/{id}/{option?}', array(
                'as' => 'dos.other.injuries.theft',
                function ($action, $id, $option = null) {
                    if ($option == null)
                        return App::make('DosOtherInjuriesTheftController')->{$action}($id);
                    else
                        return App::make('DosOtherInjuriesTheftController')->{$action}($id, $option);
                }
            )
        );
        // --- kradziez


        // -- kartoteka

        Route::get('/dos/other/injuries/create-mobile/{injury_id}', array(
                'as' => 'dos.other.injuries.create-mobile',
                'uses' => 'DosOtherInjuriesCreateController@indexMobile'
            )
        );
        Route::get('/dos/other/injuries/create', array(
                'as' => 'dos.other.injuries.create',
                'uses' => 'DosOtherInjuriesCreateController@index'
            )
        );
        Route::get('/dos/other/injuries/create/clear', array(
                'as' => 'dos.other.injuries.create.clear',
                'uses' => 'DosOtherInjuriesCreateController@indexClear'
            )
        );

        Route::get('/dos/other/injuries/create-mobile/clear/{injury_id}', array(
                'as' => 'dos.other.injuries.create-mobile.clear',
                'uses' => 'DosOtherInjuriesCreateController@indexMobileClear'
            )
        );

        Route::get('/dos/other/injuries/create/client', array(
                'as' => 'dos.other.injuries.newClient',
                'uses' => 'DosOtherInjuriesCreateController@createClient'
            )
        );
        Route::post('/dos/other/injuries/store/client', array(
                'as' => 'dos.other.injuries.storeClient',
                'uses' => 'DosOtherInjuriesCreateController@storeClient'
            )
        );
        Route::post('/dos/other/injuries/check/client', array(
                'as' => 'dos.other.injuries.checkClientNIP',
                'uses' => 'DosOtherInjuriesCreateController@checkClientNIP'
            )
        );
        Route::get('/dos/other/injuries/list/clients', array(
                'as' => 'dos.other.injuries.listClients',
                'uses' => 'DosOtherInjuriesCreateController@listClients'
            )
        );
        Route::get('/dos/other/injuries/dialog/create/category', array(
                'as' => 'dos.other.injuries.dialog.create.category',
                'uses' => 'DosOtherInjuriesCreateController@dialogCreateCategory'
            )
        );
        Route::post('/dos/other/injuries/dialog/create/category', array(
                'as' => 'dos.other.injuries.dialog.post.category',
                'uses' => 'DosOtherInjuriesCreateController@dialogPostCategory'
            )
        );


        Route::post('/dos/other/injuries/post', array(
            'as' => 'dos.other.injuries.post',
            'uses' => 'DosOtherInjuriesCreateController@post'
        ));

        Route::post('/dos/other/injuries/isdl-getList', array(
                'as' => 'dos.other.injuries.getIsdlList',
                'uses' => 'DosOtherInjuriesCreateController@getIsdlList'
            )
        );
        Route::post('/dos/other/injuries/object/data', array(
                'as' => 'dos.other.injuries.getObjectData',
                'uses' => 'DosOtherInjuriesCreateController@getObjectData'
            )
        );
        Route::post('/dos/other/injuries/checkContract', array(
                'as' => 'dos.other.contract.getList',
                'uses' => 'DosOtherInjuriesCreateController@getContractList'
            )
        );
        Route::post('/dos/other/injuries/checkContract/nonIsdl', array(
                'as' => 'dos.other.contract.getListNonIsdl',
                'uses' => 'DosOtherInjuriesCreateController@getContractListNonIsdl'
            )
        );
        Route::post('/dos/other/injuries/object-check-injuries', array(
                'as' => 'dos.other.object.checkInjuries',
                'uses' => 'DosOtherInjuriesCreateController@getObjectCheckInjuries'
            )
        );

        Route::post('/dos/other/injuries/object/check-if-exist', array(
                'as' => 'dos.other.object.checkIfExist',
                'uses' => 'DosOtherInjuriesCreateController@checkIfObjectExist'
            )
        );
        Route::post('/dos/other/injuries/search/client', array(
                'as' => 'dos.other.client.search',
                'uses' => 'DosOtherInjuriesCreateController@getSearchClient'
            )
        );

        Route::post('/dos/other/injuries/object/search-in-insurances', array(
                'as' => 'dos.other.object.searchInInsurances',
                'uses' => 'DosOtherInjuriesCreateController@searchInInsurances'
            )
        );

        Route::get('/dos/other/injuries/object/select-insurance-object', array(
                'as' => 'dos.other.object.selectInsuranceObject',
                'uses' => 'DosOtherInjuriesCreateController@getSelectInsuranceObject'
            )
        );

        Route::get('/dos/other/injuries/create/infolinia', array(
                'as' => 'dos.other.injuries.create.infolinia',
                'uses' => 'DosOtherInjuriesCreateController@indexInfolinia'
            )
        );

        Route::get('/dos/other/injuries/create/infolinia/clear/', array(
                'as' => 'dos.other.injuries.create.infolinia.clear',
                'uses' => 'DosOtherInjuriesCreateController@indexInfoliniaClear'
            )
        );

        Route::post('/dos/other/injuries/object/check-if-exist', array(
                'as' => 'dos.other.object.checkIfExist',
                'uses' => 'DosOtherInjuriesCreateController@checkIfObjectExist'
            )
        );

        Route::get('/dos/other/injuries/dialog/create/category', array(
                'as' => 'dos.other.injuries.dialog.create.category',
                'uses' => 'DosOtherInjuriesCreateController@dialogCreateCategory'
            )
        );
        Route::post('/dos/other/injuries/dialog/create/category', array(
                'as' => 'dos.other.injuries.dialog.post.category',
                'uses' => 'DosOtherInjuriesCreateController@dialogPostCategory'
            )
        );


        Route::get('/dos/other/injuries/create/client', array(
                'as' => 'dos.other.injuries.newClient',
                'uses' => 'DosOtherInjuriesCreateController@createClient'
            )
        );
        Route::post('/dos/other/injuries/store/client', array(
                'as' => 'dos.other.injuries.storeClient',
                'uses' => 'DosOtherInjuriesCreateController@storeClient'
            )
        );
        Route::post('/dos/other/injuries/check/client', array(
                'as' => 'dos.other.injuries.checkClientNIP',
                'uses' => 'DosOtherInjuriesCreateController@checkClientNIP'
            )
        );
        Route::get('/dos/other/injuries/list/clients', array(
                'as' => 'dos.other.injuries.listClients',
                'uses' => 'DosOtherInjuriesCreateController@listClients'
            )
        );

        Route::post('/dos/other/injuries/post', array(
            'as' => 'dos.other.injuries.post',
            'uses' => 'DosOtherInjuriesCreateController@post'
        ));

        Route::post('/dos/other/injuries/isdl-getList', array(
                'as' => 'dos.other.injuries.getIsdlList',
                'uses' => 'DosOtherInjuriesCreateController@getIsdlList'
            )
        );
        Route::post('/dos/other/injuries/object/data', array(
                'as' => 'dos.other.injuries.getObjectData',
                'uses' => 'DosOtherInjuriesCreateController@getObjectData'
            )
        );
        Route::post('/dos/other/injuries/checkContract', array(
                'as' => 'dos.other.contract.getList',
                'uses' => 'DosOtherInjuriesCreateController@getContractList'
            )
        );
        Route::post('/dos/other/injuries/checkContract/nonIsdl', array(
                'as' => 'dos.other.contract.getListNonIsdl',
                'uses' => 'DosOtherInjuriesCreateController@getContractListNonIsdl'
            )
        );
        Route::post('/dos/other/injuries/object-check-injuries', array(
                'as' => 'dos.other.object.checkInjuries',
                'uses' => 'DosOtherInjuriesCreateController@getObjectCheckInjuries'
            )
        );


        Route::get('/dos/other/injuries/infolinia/search', array(
                'as' => 'dos.other.injuries.infolinia.search',
                'uses' => 'DosOtherInjuriesInfoliniaController@getSearch'
            )
        );

        Route::post('/dos/other/injuries/infolinia/search/contract', array(
                'as' => 'dos.other.injuries.infolinia.search.contract',
                'uses' => 'DosOtherInjuriesInfoliniaController@getSearchContract'
            )
        );

        Route::post('/dos/other/injuries/infolinia/search/injury_nr', array(
                'as' => 'dos.other.injuries.infolinia.search.injury_nr',
                'uses' => 'DosOtherInjuriesInfoliniaController@getSearchInjury_nr'
            )
        );

        Route::get('/dos/other/injuries/infolinia/info/{id}/', array(
                'as' => 'dos.other.injuries.infolinia.info',
                'uses' => 'DosOtherInjuriesInfoliniaController@getInfo'
            )
        );

        Route::post('/dos/other/injuries/infolinia/contract-getList', array(
                'as' => 'dos.other.injuries.object.contract-getList',
                'uses' => 'DosOtherInjuriesInfoliniaController@getObjectContractList'
            )
        );

        Route::post('/dos/other/injuries/infolinia/injury_nr-getList', array(
                'as' => 'dos.other.injuries.object.injury_nr-getList',
                'uses' => 'DosOtherInjuriesInfoliniaController@getObjectInjury_nrList'
            )
        );

        Route::get('/dos/injuries/{id}/chat/create/', array(
                'as' => 'dos.other.chat.create',
                'uses' => 'DosOtherInjuriesChatController@create'
            )
        );

        Route::post('/dos/injuries/{id}/chat/post/', array(
                'as' => 'dos.other.chat.post',
                'uses' => 'DosOtherInjuriesChatController@post'
            )
        );

        Route::get('/dos/injuries/chat/{id}/replay/', array(
                'as' => 'dos.other.chat.replay',
                'uses' => 'DosOtherInjuriesChatController@replay'
            )
        );

        Route::post('/dos/injuries/chat/{id}/replay/', array(
                'as' => 'dos.other.chat.postReplay',
                'uses' => 'DosOtherInjuriesChatController@postReplay'
            )
        );
        Route::post('/dos/injuries/chat/checkConversation/', array(
                'as' => 'dos.other.chat.checkConversation',
                'uses' => 'DosOtherInjuriesChatController@checkConversation'
            )
        );
        // dos pozostałe

        if (Config::get('webconfig.WEBCONFIG_SETTINGS_zarzadzanie_pojazdami') == 1) {
            //zarzadzanie pojazdami
            Route::get('/vmanage/{controller}/{action}/{id?}', array(
                    'as' => 'vmanage.get',
                    function ($controller, $action, $id = null) {
                        return App::make('Vmanage' . ucfirst($controller) . 'Controller')->{$action}($id);
                    }
                )
            );

            Route::post('/vmanage/{controller}/{action}/{id?}/', array(
                    'as' => 'vmanage.post',
                    function ($controller, $action, $id = null) {
                        return App::make('Vmanage' . ucfirst($controller) . 'Controller')->{$action}($id);
                    }
                )
            );

        }

        // -- zarzadzanie pojazdami

        //settings

        Route::controller('settings/users', 'UsersController');
        Route::controller('settings/user/groups', 'UserGroupsController');
        Route::controller('settings/contractor-groups', 'SettingsContractorGroupsController');
        Route::controller('settings/sales-programs', 'SettingsSalesProgramsController');

        //brands
        Route::get('/settings/brands', array(
                'as' => 'brands',
                'uses' => 'BrandsController@index'
            )
        );
        Route::get('/settings/brands/{id}/edit/', array(
                'as' => 'brands-edit',
                'uses' => 'BrandsController@getEdit'
            )
        );
        Route::get('/settings/brands/create', array(
                'as' => 'brands-create',
                'uses' => 'BrandsController@getCreate'
            )
        );
        Route::get('/settings/brands/{id}/delete/', array(
                'as' => 'brands-delete',
                'uses' => 'BrandsController@getDelete'
            )
        );
        Route::post('/settings/brands/{id}/delete', array(
                'as' => 'brands-delete',
                'uses' => 'BrandsController@delete'
            )
        );
        Route::post('/settings/brands/{id}/edit', array(
                'as' => 'brands-set',
                'uses' => 'BrandsController@set'
            )
        );
        Route::post('/settings/brands/add', array(
                'as' => 'brands-add',
                'uses' => 'BrandsController@postCreate'
            )
        );
        
        //insurance companies
        Route::get('/settings/insurance_companies', array(
                'as' => 'insurance_companies',
                'uses' => 'InsuranceCompaniesController@index'
            )
        );
        Route::get('/settings/insurance_companies/{id}/edit/', array(
                'as' => 'insurance_companies-edit',
                'uses' => 'InsuranceCompaniesController@getEdit'
            )
        );
        Route::get('/settings/insurance_companies/create', array(
                'as' => 'insurance_companies-create',
                'uses' => 'InsuranceCompaniesController@getCreate'
            )
        );
        Route::get('/settings/insurance_companies/create-injury', array(
                'as' => 'insurance_companies-create-injury',
                'uses' => 'InsuranceCompaniesController@getCreateInjury'
            )
        );

        Route::get('/settings/insurance_companies/{id}/delete', array(
                'as' => 'insurance_companies-delete',
                'uses' => 'InsuranceCompaniesController@getDelete'
            )
        );

        Route::get('/settings/insurance_companies/{id}/set-parent', array(
            'as' => 'insurance_companies-set-parent',
            'uses' => 'InsuranceCompaniesController@getSetParent'
            )
        );

        Route::post('/settings/insurance_companies/{id}/set-parent', array(
            'as' => 'insurance_companies-set-parent',
            'uses' => 'InsuranceCompaniesController@postSetParent'
            )
        );

        Route::get('/settings/insurance_companies/list', array(
                'as' => 'insurance_companies-list',
                'uses' => 'InsuranceCompaniesController@getList'
            )
        );
        Route::post('/settings/insurance_companies/{id}/delete', array(
                'as' => 'insurance_companies-delete',
                'uses' => 'InsuranceCompaniesController@delete'
            )
        );
        
        Route::post('/settings/insurance_companies/{id}/edit', array(
                'as' => 'insurance_companies-set',
                'uses' => 'InsuranceCompaniesController@set'
            )
        );
        Route::post('/settings/insurance_companies/add', array(
                'as' => 'insurance_companies-add',
                'uses' => 'InsuranceCompaniesController@postCreate'
            )
        );

        //companies
        Route::post('/company/guardian/edit', array(
            'as' => 'company-guardian-set',
            'uses' => 'CompaniesController@postAssignGuardian'
            )
        );

        Route::get('/company/guardians/get/{action}', array(
            'as' => 'company.guardians.get',
                function ($action) {
                    return App::make('CompanyGuardiansController')->{$action}();     
                }
            )
        );

        Route::post('/company/guardians/post/{action}', array(
            'as' => 'company.guardians.post',
                function ($action) {
                    return App::make('CompanyGuardiansController')->{$action}();
                }
            )
        );
        
        //idea data
        Route::get('/settings/idea_data', array(
                'as' => 'idea-data',
                'uses' => 'IdeaController@index'
            )
        );
        Route::get('/settings/idea_data/edit/{owner_id}/{parameter_id}', array(
                'as' => 'idea-edit',
                'uses' => 'IdeaController@getEdit'
            )
        );
        Route::post('/settings/idea_data/edit/{owner_id}/{parameter_id}', array(
                'as' => 'idea-set',
                'uses' => 'IdeaController@set'
            )
        );

        Route::get('/settings/idea_offices', array(
            'as' => 'idea.offices',
            'uses' => 'IdeaOfficesController@index'
        ));

        Route::get('/settings/idea_offices/create', array(
            'as' => 'idea.offices.create',
            'uses' => 'IdeaOfficesController@create'
        ));

        Route::post('/settings/idea_offices/post', array(
            'as' => 'idea.offices.post',
            'uses' => 'IdeaOfficesController@post'
        ));

        Route::get('/settings/idea_offices/edit/{id}', array(
            'as' => 'idea.offices.edit',
            'uses' => 'IdeaOfficesController@edit'
        ));

        Route::put('/settings/idea_offices/update/{id}', array(
            'as' => 'idea.offices.update',
            'uses' => 'IdeaOfficesController@update'
        ));

        Route::get('/settings/idea_offices/delete/{id}', array(
            'as' => 'idea.offices.delete',
            'uses' => 'IdeaOfficesController@delete'
        ));

        Route::delete('/setting/idea_offices/destroy/{id}', array(
            'as' => 'idea.offices.destroy',
            'uses' => 'IdeaOfficesController@destroy'
        ));


        //processes
        Route::get('/settings/processes', array(
                'as' => 'settings.processes',
                'uses' => 'ProcessesController@index'
            )
        );

        Route::get('/settings/processes/{id}', array(
                'as' => 'settings.processes.info',
                'uses' => 'ProcessesController@getInfo'
            )
        );

        Route::get('/settings/processes/node/{id}/', array(
                'as' => 'settings.processes.info-node',
                'uses' => 'ProcessesController@getInfoNode'
            )
        );

        Route::get('/settings/processes/edit/{id}', array(
                'as' => 'settings.processes.edit',
                'uses' => 'ProcessesController@getEdit'
            )
        );

        Route::get('/settings/processes/edit/{id}/node', array(
                'as' => 'settings.processes.edit-node',
                'uses' => 'ProcessesController@getEditNode'
            )
        );

        Route::post('/settings/processes/{id}', array(
                'as' => 'settings.processes.set',
                'uses' => 'ProcessesController@set'
            )
        );

        Route::post('/settings/processes/node/{id}/', array(
                'as' => 'settings.processes.set-node',
                'uses' => 'ProcessesController@setNode'
            )
        );

        Route::get('/settings/processes/{id}/append_user/', array(
                'as' => 'settings.processes.getAppendUser',
                'uses' => 'ProcessesController@getAppendUser'
            )
        );

        Route::post('/settings/processes/{id}/search_user/', array(
                'as' => 'settings.processes.searchUsers',
                'uses' => 'ProcessesController@getSearchUsers'
            )
        );

        Route::post('/settings/processes/{id}/appendUser', array(
                'as' => 'settings.processes.appendUser',
                'uses' => 'ProcessesController@appendUser'
            )
        );

        Route::get('/settings/processes/{id}/delete_user/', array(
                'as' => 'settings.processes.getDeleteUser',
                'uses' => 'ProcessesController@getDeleteUser'
            )
        );
        Route::post('/settings/processes/{id}/delete_user/', array(
                'as' => 'settings.processes.deleteUser',
                'uses' => 'ProcessesController@deleteUser'
            )
        );

        Route::post('/settings/processes/{id}/priority/', array(
                'as' => 'settings.processes.priority',
                'uses' => 'ProcessesController@setPriority'
            )
        );

        Route::post('/settings/processes/{id}/priority/node', array(
                'as' => 'settings.processes.priority-node',
                'uses' => 'ProcessesController@setPriorityNode'
            )
        );


        //work hours
        Route::get('/settings/hours', array(
                'as' => 'settings.hours',
                'uses' => 'WorkSettingsController@index'
            )
        );

        Route::get('/settings/hours/edit/{id}', array(
                'as' => 'settings.hours.edit',
                'uses' => 'WorkSettingsController@edit'
            )
        );

        Route::post('/settings/hours/set/{id}', array(
                'as' => 'settings.hours.set',
                'uses' => 'WorkSettingsController@set'
            )
        );

        Route::post('/settings/hours/holidays/register', array(
                'as' => 'settings.hours.holidays.register',
                'uses' => 'WorkSettingsController@registerHoliday'
            )
        );

        Route::post('/settings/hours/holidays/unregister', array(
                'as' => 'settings.hours.holidays.unregister',
                'uses' => 'WorkSettingsController@unregisterHoliday'
            )
        );

        Route::get('/settings/hours/holidays/get', array(
                'as' => 'settings.hours.holidays.get',
                'uses' => 'WorkSettingsController@getHolidays'
            )
        );

        //sms templates
        Route::get('/settings/sms-templates', array(
                'as' => 'settings.sms-templates',
                'uses' => 'SmsTemplatesController@index'
            )
        );

        Route::get('/settings/sms-templates/create', array(
                'as' => 'settings.sms-templates.create',
                'uses' => 'SmsTemplatesController@create'
            )
        );

        Route::get('/settings/sms-templates/edit/{id}', array(
                'as' => 'settings.sms-templates.edit',
                'uses' => 'SmsTemplatesController@edit'
            )
        );

        Route::get('/settings/sms-templates/delete/{id}', array(
                'as' => 'settings.sms-templates.delete',
                'uses' => 'SmsTemplatesController@delete'
            )
        );

        Route::get('/settings/sms-templates/show/{id}', array(
                'as' => 'settings.sms-templates.show',
                'uses' => 'SmsTemplatesController@show'
            )
        );
        Route::post('/settings/sms-templates/store', array(
                'as' => 'settings.sms-templates.store',
                'uses' => 'SmsTemplatesController@store'
            )
        );

        Route::post('/settings/sms-templates/update/{id}', array(
                'as' => 'settings.sms-templates.update',
                'uses' => 'SmsTemplatesController@update'
            )
        );

        Route::post('/settings/sms-templates/destroy/{id}', array(
                'as' => 'settings.sms-templates.destroy',
                'uses' => 'SmsTemplatesController@destroy'
            )
        );

        //adverts

        Route::get('/settings/adverts', array(
                'as' => 'settings.adverts',
                'uses' => 'AdvertsController@index'
            )
        );

        Route::get('/settings/adverts/create/{resolution_type_id}', array(
                'as' => 'settings.adverts.create',
                'uses' => 'AdvertsController@create'
            )
        );

        Route::post('/settings/adverts/cut', array(
                'as' => 'settings.adverts.cut',
                'uses' => 'AdvertsController@cut'
            )
        );

        Route::post('/settings/adverts/store/{resolution_type_id}', array(
                'as' => 'settings.adverts.store',
                'uses' => 'AdvertsController@store'
            )
        );

        Route::get('/settings/adverts/delete/{id}', array(
                'as' => 'settings.adverts.delete',
                'uses' => 'AdvertsController@getDelete'
            )
        );

        Route::post('/settings/adverts/delete/{id}', array(
                'as' => 'settings.adverts.destroy',
                'uses' => 'AdvertsController@delete'
            )
        );

        Route::get('/settings/adverts/edit/{id}', array(
                'as' => 'settings.adverts.edit',
                'uses' => 'AdvertsController@getEdit'
            )
        );

        Route::post('/settings/adverts/update/{id}', array(
                'as' => 'settings.adverts.update',
                'uses' => 'AdvertsController@update'
            )
        );


        //liquidation_cards
        Route::get('/settings/liquidation_cards/{action}/{id?}', array(
                'as' => 'settings.liquidation_cards',
                function ($action, $id = null) {
                    return App::make('LiquidationCardsController')->{$action}($id);
                }
            )
        );

        Route::post('/settings/liquidation_cards/{action}/{id?}', array(
                'as' => 'settings.liquidation_cards',
                function ($action, $id = null) {
                    return App::make('LiquidationCardsController')->{$action}($id);
                }
            )
        );


        //reports assignment
        Route::get('/settings/custom_reports/{action}/{id?}', array(
                'as' => 'settings.custom_reports',
                function ($action, $id = null) {
                    return App::make('SettingsCustomReportsController')->{$action}($id);
                }
            )
        );

        Route::post('/settings/custom_reports/{action}/{id?}', array(
                'as' => 'settings.custom_reports',
                function ($action, $id = null) {
                    return App::make('SettingsCustomReportsController')->{$action}($id);
                }
            )
        );


        //documents avaibility
        Route::get('/settings/documents/{action}/{id?}/{value?}', array(
                'as' => 'settings.documents',
                function ($action, $id = null, $value = null) {
                    if (!is_null($value))
                        return App::make('SettingsDocumentsController')->{$action}($id, $value);

                    return App::make('SettingsDocumentsController')->{$action}($id);
                }
            )
        );

        Route::post('/settings/documents/{action}/{id?}/{value?}', array(
                'as' => 'settings.documents',
                function ($action, $id = null, $value = null) {
                    if (!is_null($value))
                        return App::make('SettingsDocumentsController')->{$action}($id, $value);

                    return App::make('SettingsDocumentsController')->{$action}($id);
                }
            )
        );


        //annex refers dictionary settings
        Route::get('/settings/insurance-annex-refer/{action}/{id?}/{value?}', array(
                'as' => 'settings.insurance_annex_refer_get',
                function ($action, $id = null, $value = null) {
                    if (!is_null($value))
                        return App::make('SettingsInsuranceAnnexReferController')->{$action}($id, $value);

                    return App::make('SettingsInsuranceAnnexReferController')->{$action}($id);
                }
            )
        );

        Route::post('/settings/insurance-annex-refer/{action}/{id?}/{value?}', array(
                'as' => 'settings.insurance_annex_refer_post',
                function ($action, $id = null, $value = null) {
                    if (!is_null($value))
                        return App::make('SettingsInsuranceAnnexReferController')->{$action}($id, $value);

                    return App::make('SettingsInsuranceAnnexReferController')->{$action}($id);
                }
            )
        );


        //end settings

        //raporty

        Route::get('/reports/injuries/{func_name}', array(
                'as' => 'reports.injuries.get',
                function ($func_name) {
                    return App::make('InjuriesReportsController')->{$func_name}();
                }
            )
        );
        Route::post('/reports/injuries/{func_name}', array(
                'as' => 'reports.injuries.post',
                function ($func_name) {
                    return App::make('InjuriesReportsController')->{$func_name}();
                }
            )
        );

        Route::post('/reports/injuries/revert/{injury_id}', 'InjuriesReportsController@revert');

        //raporty specjalne
        Route::get('/reports/special/{func_name}/{param?}/{param2?}/', array(
                'as' => 'reports.custom.get',
                function ($func_name, $param = null, $param2 = null) {
                    return App::make('CustomReportsController')->{$func_name}($param, $param2);
                }
            )
        );
        Route::post('/reports/special/{func_name}/{param?}/{param2?}/', array(
                'as' => 'reports.custom.post',
                function ($func_name, $param = null, $param2 = null) {
                    return App::make('CustomReportsController')->{$func_name}($param, $param2);
                }
            )
        );

        //bramka sms

        Route::get('/sms', array(
                'as' => 'sms.index',
                'uses' => 'SmsController@index'
            )
        );

        Route::post('/sms/send', array(
                'as' => 'sms.send',
                'uses' => 'SmsController@send'
            )
        );

        Route::post('/sms/send-i/{id}', array(
                'as' => 'sms.send_i',
                'uses' => 'SmsController@send_i'
            )
        );


        //DOK
        //zgloszenia
        Route::get('/dok/notifications/new', array(
                'as' => 'dok.notifications.new',
                'uses' => 'DokNotificationsController@indexNew'
            )
        );

        Route::get('/dok/notifications/inprogress', array(
                'as' => 'dok.notifications.inprogress',
                'uses' => 'DokNotificationsController@indexInprogress'
            )
        );

        Route::get('/dok/notifications/completed', array(
                'as' => 'dok.notifications.completed',
                'uses' => 'DokNotificationsController@indexCompleted'
            )
        );

        Route::get('/dok/notifications/canceled', array(
                'as' => 'dok.notifications.canceled',
                'uses' => 'DokNotificationsController@indexCanceled'
            )
        );

        Route::get('/dok/notifications/create', array(
                'as' => 'dok.notifications.create',
                'uses' => 'DokNotificationsController@getCreate'
            )
        );

        Route::get('/dok/notifications/info/{id}', array(
                'as' => 'dok.notifications.info',
                'uses' => 'DokNotificationsController@indexInfo'
            )
        );

        Route::post('/dok/notifications/create/processes', array(
                'as' => 'dok.notifications.create.processes',
                'uses' => 'DokNotificationsController@getProcesses'
            )
        );

        Route::post('/dok/notifications/create/getNewGroup', array(
                'as' => 'dok.notifications.create.getNewGroup',
                'uses' => 'DokNotificationsController@getNewGroup'
            )
        );

        Route::post('/dok/notifications/store', array(
                'as' => 'dok.notifications.store',
                'uses' => 'DokNotificationsController@store'
            )
        );

        Route::get('/dok/notifications/set-cancel{id}/', array(
                'as' => 'dok.notifications.getCancel',
                'uses' => 'DialogsDokNotificationsController@getCancel'
            )
        );

        Route::get('/dok/notifications/set-inprogress/{id}/', array(
                'as' => 'dok.notifications.getInprogress',
                'uses' => 'DialogsDokNotificationsController@getInprogress'
            )
        );

        Route::get('/dok/notifications/set-complete/{id}/', array(
                'as' => 'dok.notifications.getComplete',
                'uses' => 'DialogsDokNotificationsController@getComplete'
            )
        );

        Route::post('/dok/notifications/set-cancel/{id}/', array(
                'as' => 'dok.notifications.setCancel',
                'uses' => 'DokNotificationsController@setCancel'
            )
        );

        Route::post('/dok/notifications/set-inprogress/{id}/', array(
                'as' => 'dok.notifications.setInprogress',
                'uses' => 'DokNotificationsController@setInprogress'
            )
        );

        Route::post('/dok/notifications/set-complete/{id}/', array(
                'as' => 'dok.notifications.setComplete',
                'uses' => 'DokNotificationsController@setComplete'
            )
        );

        Route::get('/dok/notifications/change-process/{id}/', array(
            'as' => 'dok.notifications.getChangeProcess',
            'uses' => 'DialogsDokNotificationsController@getChangeProcess'
        ));

        Route::post('/dok/notifications/change-process/{id}/', array(
            'as' => 'dok.notifications.setChangeProcess',
            'uses' => 'DialogsDokNotificationsController@setChangeProcess'
        ));

        //dok user view

        Route::get('/dok/notifications/new/{id}', array(
                'as' => 'dok.notifications-user.new',
                'uses' => 'DokNotificationsUserController@indexNew'
            )
        );

        Route::get('/dok/notifications/inprogress/{id}', array(
                'as' => 'dok.notifications-user.inprogress',
                'uses' => 'DokNotificationsUserController@indexInprogress'
            )
        );

        Route::get('/dok/notifications/completed/{id}', array(
                'as' => 'dok.notifications-user.completed',
                'uses' => 'DokNotificationsUserController@indexCompleted'
            )
        );

        Route::get('/dok/notifications/canceled/{id}', array(
                'as' => 'dok.notifications-user.canceled',
                'uses' => 'DokNotificationsUserController@indexCanceled'
            )
        );

        //dok kartoteka

        Route::post('/dok/notifications/document/{id}/', array(
                'as' => 'dok.notifications.postDocument',
                'uses' => 'DokNotificationsInfoController@postDocument'
            )
        );

        Route::post('/dok/notifications/dialog-document', array(
                'as' => 'dok.notifications.getDocumentSet',
                'uses' => 'DialogsDokNotificationsController@getDocumentSet'
            )
        );
        Route::post('/dok/notifications/set-document', array(
                'as' => 'dok.notifications.setDocumentSet',
                'uses' => 'DokNotificationsInfoController@setDocumentSet'
            )
        );
        Route::post('/dok/notifications/set-document-del', array(
                'as' => 'dok.notifications.setDocumentDel',
                'uses' => 'DokNotificationsInfoController@setDocumentDel'
            )
        );

        Route::get('/dok/notifications/download-doc/{id}', array(
                'as' => 'dok.notifications.downloadDoc',
                'uses' => 'DokNotificationsInfoController@downloadDoc'
            )
        );
        Route::get('/dok/notifications/download-generate-doc/{id}', array(
                'as' => 'dok.notifications.downloadGenerateDoc',
                'uses' => 'DokNotificationsInfoController@downloadGenerateDoc'
            )
        );

        Route::get('/dok/notifications/del-doc/{id}/', array(
                'as' => 'dok.notifications.getDelDoc',
                'uses' => 'DialogsDokNotificationsController@getDelDoc'
            )
        );

        Route::get('/dok/notifications/del-doc-conf/{id}/', array(
                'as' => 'dok.notifications.getDelDocConf',
                'uses' => 'DialogsDokNotificationsController@getDelDocConf'
            )
        );

        Route::post('/dok/notifications/del-doc/{id}/', array(
                'as' => 'dok.notifications.setDelDoc',
                'uses' => 'DokNotificationsInfoController@setDelDoc'
            )
        );

        Route::post('/dok/notifications/del-doc-conf/{id}/', array(
                'as' => 'dok.notifications.setDelDocConf',
                'uses' => 'DokNotificationsInfoController@setDelDocConf'
            )
        );

        Route::post('/dok/notifications/change-priority/{id}/', array(
                'as' => 'dok.notifications.setPriority',
                'uses' => 'DokNotificationsInfoController@setPriority'
            )
        );


        Route::get('/dok/notifications/{id}/chat/create/', array(
                'as' => 'dok.notifications.chat.create',
                'uses' => 'DokNotificationsChatController@create'
            )
        );

        Route::post('/dok/notifications/{id}/chat/post/', array(
                'as' => 'dok.notifications.chat.post',
                'uses' => 'DokNotificationsChatController@post'
            )
        );

        Route::get('/dok/notifications/chat/{id}/replay/', array(
                'as' => 'dok.notifications.chat.replay',
                'uses' => 'DokNotificationsChatController@replay'
            )
        );

        Route::post('/dok/notifications/chat/{id}/replay/', array(
                'as' => 'dok.notifications.chat.postReplay',
                'uses' => 'DokNotificationsChatController@postReplay'
            )
        );

        Route::get('/dok/notifications/chat/{id}/deadline/', array(
                'as' => 'dok.notifications.chat.deadline',
                'uses' => 'DokNotificationsChatController@deadline'
            )
        );

        Route::post('/dok/notifications/chat/{id}/deadline/', array(
                'as' => 'dok.notifications.chat.postDeadline',
                'uses' => 'DokNotificationsChatController@postDeadline'
            )
        );

        Route::get('/dok/notifications/chat/{id}/close/', array(
                'as' => 'dok.notifications.chat.close',
                'uses' => 'DokNotificationsChatController@close'
            )
        );

        Route::post('/dok/notifications/chat/{id}/close/', array(
                'as' => 'dok.notifications.chat.postClose',
                'uses' => 'DokNotificationsChatController@postClose'
            )
        );

        Route::get('/dok/notifications/chat/{id}/accept/', array(
                'as' => 'dok.notifications.chat.accept',
                'uses' => 'DokNotificationsChatController@accept'
            )
        );

        Route::post('/dok/notifications/chat/{id}/accept/', array(
                'as' => 'dok.notifications.chat.postAccept',
                'uses' => 'DokNotificationsChatController@postAccept'
            )
        );

        Route::get('/dok/notifications/chat/{id}/removeDeadline/', array(
                'as' => 'dok.notifications.chat.removeDeadline',
                'uses' => 'DokNotificationsChatController@removeDeadline'
            )
        );

        Route::post('/dok/notifications/chat/{id}/removeDeadline/', array(
                'as' => 'dok.notifications.chat.postRemoveDeadline',
                'uses' => 'DokNotificationsChatController@postRemoveDeadline'
            )
        );
        Route::get('/dok/notifications/chat/{id}/deleteMessage/', array(
                'as' => 'dok.notifications.chat.deleteMessage',
                'uses' => 'DokNotificationsChatController@deleteMessage'
            )
        );
        Route::post('/dok/notifications/chat/{id}/removeMessage/', array(
                'as' => 'dok.notifications.chat.removeMessage',
                'uses' => 'DokNotificationsChatController@removeMessage'
            )
        );

        Route::post('/dok/notifications/chat/checkConversation/', array(
                'as' => 'dok.notifications.chat.checkConversation',
                'uses' => 'DokNotificationsChatController@checkConversation'
            )
        );


        //zarządzanie polisami
            Route::controller('insurances/communicator', 'InsurancesCommunicatorController');
            Route::controller('insurances/create', 'InsurancesCreateController');
            Route::controller('insurances/create-yacht',  'InsurancesCreateYachtController');
            Route::controller('insurances/deductible', 'InsurancesDeductibleController');
            Route::controller('insurances/documents', 'InsurancesDocumentsController');
            Route::controller('insurances/groups', 'InsurancesGroupsController');
            Route::controller('insurances/info-client', 'InsurancesInfoClientController');
            Route::controller('insurances/info', 'InsurancesInfoController');
            Route::controller('insurances/info-dialog', 'InsurancesInfoDialogController');
            Route::controller('insurances/info-insurances', 'InsurancesInfoInsurancesController');
            //Route::controller('insurances/inprogress', 'InsurancesInprogressController');
            Route::controller('insurances/manage-actions', 'InsurancesManageActionsController');
            Route::controller('insurances/manage', 'InsurancesManageController');
            Route::controller('insurances/manage-dialog', 'InsurancesManageDialogController');
            Route::controller('insurances/reports', 'InsurancesReportsController');
            Route::controller('insurances/store', 'InsurancesStoreController');
            Route::controller('insurances/upload', 'InsurancesUploadController');


        //zgrupowane routy dla kontrolerów
        Route::get('/{module}/{controller}/{action?}/{id?}/{value?}', array(
                'as' => 'routes.get',
                function ($module, $controller, $action = null, $id = null, $value = null) {
                    if (is_null($id) && is_numeric($action)) {
                        $id = $action;
                        $action = $controller;
                        $controller = '';
                    } else if (is_null($id) && is_null($action)) {
                        $action = $controller;
                        $controller = '';
                    }
                    $action = str_replace('-', ' ', $action);
                    $action = ucwords($action);
                    $action = str_replace(' ', '', $action);
                    $action = ucfirst($action);

                    if (!is_null($value))
                        return App::make(ucfirst($module) . ucfirst($controller) . 'Controller')->{$action}($id, $value);

                    return App::make(ucfirst($module) . ucfirst($controller) . 'Controller')->{$action}($id);
                }
            )
        );

        Route::post('/{module}/{controller}/{action?}/{id?}/{value?}', array(
                'as' => 'routes.post',
                'before' => 'csrf',
                function ($module, $controller, $action = null, $id = null, $value = null) {
                    if (is_null($id) && is_numeric($action)) {
                        $id = $action;
                        $action = $controller;
                        $controller = '';
                    } else if (is_null($id) && is_null($action)) {
                        $action = $controller;
                        $controller = '';
                    }
                    $action = str_replace('-', ' ', $action);
                    $action = ucwords($action);
                    $action = str_replace(' ', '', $action);
                    $action = ucfirst($action);

                    if (!is_null($value))
                        return App::make(ucfirst($module) . ucfirst($controller) . 'Controller')->{$action}($id, $value);

                    return App::make(ucfirst($module) . ucfirst($controller) . 'Controller')->{$action}($id);
                }
            )
        );
        // --- zgrupowane routy dla kontrolerów ---
    });
});

Route::group(['before' => 'guest'], function () {
    Route::group(array('before' => 'csrf'), function () {

        Route::post('/', array(
            'as' => 'login-post',
            'uses' => 'LoginController@postLogin'
        ));

    });

    Route::get('{all}', array(
        'as' => 'login-main',
        'uses' => 'LoginController@getLogin'
    ))->where('all', '.*');
});
