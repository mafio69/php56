<?php
class DictionarySeeder extends Seeder
{
    private function insertAction($model, $value, $deleted = null, $update = false) {
        if(method_exists($model, 'withTrashed')){
            $object = $model::where('id', $value['id'])->withTrashed()->first();
        }else{
            $object = $model::where('id', $value['id'])->first();
        }
        if(!$object){
            $new_model = clone $model;
            $new_model->fill($value);
            $new_model->save();
        }else{
            if($deleted){
                if(!$object->deleted_at) {
                    $object->delete();
                }
            }elseif($update){
                $object->timestamps = false;
                $object->fill($value);
                if(method_exists($model, 'withTrashed')) {
                    $object->deleted_at = null;
                }
                if($object->isDirty()){
                    $object->save();
                }
            }
        }
    }
    private function insertPivotAction($table, $value) {
        $object = DB::table($table)->where(function($query)use($value){
            $query->where(key($value), $value[key($value)]);
            next($value);
            $query->where(key($value), $value[key($value)]);
        })->first();
        if(!$object){
            DB::table($table)->insert($value);
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
        {
            echo ("\n" );
            foreach(get_class_methods(get_class($this)) as $method) {
                if (substr( $method, 0, 5 ) === "seed_") {
                    call_user_func([$this, $method]);
                    echo ("\033[0;32m".$method."\033[0m\n" );
                }
            }
            echo ("\n" );
    }

    private function seed_CompanyGroup () {
        $model = new CompanyGroup;

        $this->insertAction($model, ['id'=>'1', 'name'=>'CAS', 'marker'=>'blue', 'deleted_at'=>null, ]);
        $this->insertAction($model, ['id'=>'2', 'name'=>'Zewnętrzne', 'marker'=>null ], true);
        $this->insertAction($model, ['id'=>'5', 'name'=>'Idea Fleet S.A.', 'marker'=>'purple', 'deleted_at'=>null, ]);
        $this->insertAction($model, ['id'=>'6', 'name'=>'Nowy', 'marker'=>null, 'deleted_at'=>null, ]);
    }

    private function seed_InjuryDocumentType() {
        $model = new InjuryDocumentType;

        $this->insertAction($model, ['id'=>'1', 'name'=>'Upoważnienie do odbioru i transportu pojazdu', 'short_name'=>'auth_transport', 'template_name'=>'auth_transport', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'2', 'name'=>'Upoważnienie dla serwisu', 'short_name'=>'auth_receive_service', 'template_name'=>'auth_receive_service', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'3', 'name'=>'Upoważnienie dla Korzystającego', 'short_name'=>'auth_receive', 'template_name'=>'auth_receive', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'4', 'name'=>'Upoważnienie dla Idea Leasing', 'short_name'=>'auth_receive_idea', 'template_name'=>'auth_receive_idea', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'5', 'name'=>'Odmowa wydania upoważnienia', 'short_name'=>'refusal_auth', 'template_name'=>'refusal_auth', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'6', 'name'=>'Zlecenie do serwisu', 'short_name'=>'notification', 'template_name'=>'notification', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'7', 'name'=>'Upoważnienie dla Korzystającego bez faktur', 'short_name'=>'auth_receive_without_invoice', 'template_name'=>'auth_receive_without_invoice', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'8', 'name'=>'Upoważnienie dla serwisu współpracującego z IL', 'short_name'=>'auth_receive_service_cooperate', 'template_name'=>'auth_receive_service_cooperate', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'9', 'name'=>'Upoważnienie do odbioru pojazdu', 'short_name'=>'auth_receive_vehicle', 'template_name'=>'auth_receive_vehicle', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'10', 'name'=>'Upoważnienie do odbioru oceny technicznej', 'short_name'=>'auth_receive_ot', 'template_name'=>'auth_receive_ot', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'11', 'name'=>'Deklaracja odkupu wraku przez leasingobiorcę', 'short_name'=>'declaration_repurchase', 'template_name'=>'declaration_repurchase', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'alert_repurchase', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'12', 'name'=>'Upoważnienie do odbioru wraku', 'short_name'=>'auth_wreck', 'template_name'=>'auth_wreck', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'1', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'13', 'name'=>'Wypowiedzenie polisy OC', 'short_name'=>'termination_oc', 'template_name'=>'termination_oc', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'1', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'14', 'name'=>'Pismo dotyczące przesłania dokumentów do nabywcy', 'short_name'=>'sending_docs_buyer', 'template_name'=>'sending_docs_buyer', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'1', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'15', 'name'=>'Wniosek o naprawę szkody całkowitej', 'short_name'=>'repair', 'template_name'=>'repair', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'alert_receive', 'conditions'=>'1', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'16', 'name'=>'Potwierdzenie oferty odkupu wraka przez nabywcę aukcyjnego', 'short_name'=>'buyer_confirmation', 'template_name'=>'buyer_confirmation', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'alert_buyer', 'conditions'=>'1', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'17', 'name'=>'Zgoda na naprawę samochodu po szkodzie całkowitej.', 'short_name'=>'repair_total_agreement', 'template_name'=>'repair_total_agreement', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'18', 'name'=>'Prośba o wyrejestrowanie pojazdu', 'short_name'=>'unregister_vehicle_request', 'template_name'=>'unregister_vehicle_request', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'19', 'name'=>'Faktura proforma', 'short_name'=>'proforma_invoice', 'template_name'=>'proforma_invoice', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'20', 'name'=>'Przesłanie do ZU dokumentów po kradzieży', 'short_name'=>'send_ic_theft_docs', 'template_name'=>'send_ic_theft_docs', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'21', 'name'=>'Przesłanie dokumentów do nabywcy', 'short_name'=>'send_docs_buyer', 'template_name'=>'send_docs_buyer', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'22', 'name'=>'Upoważnienie do zezłomowania.', 'short_name'=>'auth_scrapping', 'template_name'=>'auth_scrapping', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'23', 'name'=>'Oświadczenie w związku z wynajmem pojazdu zastępczego.', 'short_name'=>'rent_statement', 'template_name'=>'rent_statement', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'24', 'name'=>'Zał. nr 1 - Druk zgłoszenia szkody', 'short_name'=>'damage_notification', 'template_name'=>'damage_notification_vb', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'1', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'25', 'name'=>'Zał. nr 1 - Druk zgłoszenia szkody', 'short_name'=>'damage_notification', 'template_name'=>'damage_notification_vb_idea', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'1', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'26', 'name'=>'Zał. nr 2 - Upoważnienie', 'short_name'=>'auth_receive_compensation', 'template_name'=>'auth_receive_compensation', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'2', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'27', 'name'=>'Zał. nr 3 - Upoważnienie', 'short_name'=>'auth_deliver', 'template_name'=>'auth_deliver', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'3', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'28', 'name'=>'Zał. nr 4', 'short_name'=>'request_informations', 'template_name'=>'request_informations', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'4', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'29', 'name'=>'Zał. nr 5 - Druk szkody', 'short_name'=>'injury_printing', 'template_name'=>'injury_printing', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'5', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'30', 'name'=>'Zał. nr 6 - Kosztorys', 'short_name'=>'refusal_estimate', 'template_name'=>'refusal_estimate', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'6', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'31', 'name'=>'Zał. nr 7 - Upoważnienie kosztorys', 'short_name'=>'auth_estimate', 'template_name'=>'auth_estimate', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'1', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'7', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'32', 'name'=>'Zał. nr 7 - Upoważnienie kosztorys wariant II', 'short_name'=>'auth_estimate', 'template_name'=>'auth_estimate_ver2', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'1', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'7', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'33', 'name'=>'Zał. nr 8 - FV', 'short_name'=>'fv_owner', 'template_name'=>'fv_owner', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'8', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'34', 'name'=>'Zał. nr 9 - FV - służbowy', 'short_name'=>'fv_owner', 'template_name'=>'fv_owner_ver2', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'9', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'35', 'name'=>'Zał. nr 10 - Decyzja TU - odmowa wypłaty odszkodowania', 'short_name'=>'tu_decision', 'template_name'=>'tu_decision_refusal', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'10', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'36', 'name'=>'Zał. nr 10 - Decyzja TU - wypłata odszkodowania', 'short_name'=>'tu_decision', 'template_name'=>'tu_decision_payment', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'10', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'37', 'name'=>'Zał. nr 10 - Decyzja TU - wypłata odszkodowania - pow.', 'short_name'=>'tu_decision', 'template_name'=>'tu_decision_payment_ver2', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'10', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'38', 'name'=>'Zał. nr 12 - E-mail zabl. umowy', 'short_name'=>'blocking_contract', 'template_name'=>'blocking_contract', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'12', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'39', 'name'=>'Zał. nr 13 - Upoważnienie całka', 'short_name'=>'auth_total', 'template_name'=>'auth_total', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'13', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'40', 'name'=>'Zał. nr 14 - Dok do szkody', 'short_name'=>'dok_to_injury', 'template_name'=>'dok_to_injury', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'14', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'41', 'name'=>'Zał. nr 15 - Transport pozos', 'short_name'=>'request_transport_other', 'template_name'=>'request_transport_other', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'15', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'42', 'name'=>'Zał. nr 15 - Transport pozos wariant II', 'short_name'=>'request_transport_other', 'template_name'=>'request_transport_other_ver2', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'15', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'43', 'name'=>'Zał. nr 16 - Transport II wezwanie', 'short_name'=>'request_transport_buy', 'template_name'=>'request_transport_buy', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'16', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'44', 'name'=>'Zał. nr 16 - Transport II wezwanie wariant II', 'short_name'=>'request_transport_buy', 'template_name'=>'request_transport_buy_ver2', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'16', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'45', 'name'=>'Zał. nr 17 - Tabela', 'short_name'=>'order_for_transport', 'template_name'=>'order_for_transport', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'17', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'46', 'name'=>'Zał. nr 18 - Email zabl. umowy kradzież', 'short_name'=>'blocking_theft', 'template_name'=>'blocking_theft', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'18', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'47', 'name'=>'Zał. nr 19 - Upoważnienie kradzież', 'short_name'=>'auth_theft', 'template_name'=>'auth_theft', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'19', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'48', 'name'=>'Zał. nr 20 - Dok do kradzieży', 'short_name'=>'dok_to_theft', 'template_name'=>'dok_to_theft', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'20', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'49', 'name'=>'Zał. nr 24 - Zgłoszenie szkody wariant serwisowy', 'short_name'=>'notification', 'template_name'=>'notification_service', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'24', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'50', 'name'=>'Pismo przewodnie do wniosku.', 'short_name'=>'cover_letter', 'template_name'=>'cover_letter', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'51', 'name'=>'Zał. - upoważnienie na Idea Fleet', 'short_name'=>'fleet_auth_receive_compensation', 'template_name'=>'fleet_auth_receive_compensation', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'52', 'name'=>'Zał. nr 1- zgłoszenie szkody do serwisu współpracującego z IF', 'short_name'=>'fleet_notification', 'template_name'=>'fleet_notification', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'1', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'53', 'name'=>'Zał. nr 2-upoważnienie na serwis współpracujący z Idea Fleet', 'short_name'=>'fleet_auth_receive_compensation_company', 'template_name'=>'fleet_auth_receive_compensation_company', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'2', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'54', 'name'=>'Zał. nr 3-e-mail zabl umowy Idea Fleet', 'short_name'=>'fleet_blocking_contract', 'template_name'=>'fleet_blocking_contract', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'3', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'55', 'name'=>'Zał. nr 4-upoważnienie całka Idea Fleet', 'short_name'=>'fleet_auth_total', 'template_name'=>'fleet_auth_total', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'4', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'56', 'name'=>'Zał. nr 5-dok do szkody Idea Fleet', 'short_name'=>'fleet_doc_total', 'template_name'=>'fleet_doc_total', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'5', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'57', 'name'=>'Zał. nr 6 -e-mail zabl umowy kradzież Idea Fleet', 'short_name'=>'fleet_blocking_theft', 'template_name'=>'fleet_blocking_theft', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'6', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'58', 'name'=>'Zał. nr 7 -upowaznienie kradzież Idea Fleet', 'short_name'=>'fleet_auth_theft', 'template_name'=>'fleet_auth_theft', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'7', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'59', 'name'=>'Zał. nr 8 -dok do kradzieży Idea Fleet', 'short_name'=>'fleet_doc_to_theft', 'template_name'=>'fleet_doc_to_theft', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'8', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'60', 'name'=>'Zał. 1 s zlecenie naprawy do serwisu współpracującego', 'short_name'=>'commissioned_by_the_service', 'template_name'=>'commissioned_by_the_service', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'1', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'61', 'name'=>'Zał. 1 c informacja o zablokowaniu umowy - szkoda całkowita', 'short_name'=>'information_about_blocking', 'template_name'=>'information_about_blocking', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'1', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'62', 'name'=>'zał. 1 k informacja o zablokowaniu umowy - kradzież', 'short_name'=>'information_about_blocking2', 'template_name'=>'information_about_blocking2', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'1', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'63', 'name'=>'Zał. 3 C I Informacja, pismo do LB o konieczności przetransportowania', 'short_name'=>'lb_need_for_transport', 'template_name'=>'lb_need_for_transport', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'3', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'64', 'name'=>'Zał. 3 C II Informacja, pismo do LB o konieczności przetransportowania', 'short_name'=>'lb_need_for_transport2', 'template_name'=>'lb_need_for_transport2', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'3', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'65', 'name'=>'Zał. 4 c /4 cp Deklaracja LB/PB', 'short_name'=>'declaration', 'template_name'=>'declaration', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'4', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'66', 'name'=>'zał. 8 c Pismo do LB II wezwanie do transportu wraku dla dawnej IL', 'short_name'=>'call_to_transport_wreck', 'template_name'=>'call_to_transport_wreck', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'8', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'67', 'name'=>'Zał. 10 c Deklaracja dotycząca naprawy - dotyczy szkód z OC', 'short_name'=>'declaration_concerning_repairs', 'template_name'=>'declaration_concerning_repairs', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'10', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'68', 'name'=>'Zał. 8 Dyspozycja wypłaty na podstawie kosztorysu wariant II na LB', 'short_name'=>'disposition_compensation', 'template_name'=>'disposition_compensation', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'8', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'69', 'name'=>'Zał. 2 c / 2cp Pismo do LB/PB Informacyjne o szkodzie całkowitej', 'short_name'=>'information_about_total_damage', 'template_name'=>'information_about_total_damage', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'2', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'70', 'name'=>'Zał. 2 k  Upoważnienie do odbioru odszkodowania na IL kradzież', 'short_name'=>'reception_compensation', 'template_name'=>'reception_compensation', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'2', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'71', 'name'=>'Zał. 2 s Upoważnienie na serwis współpracujący', 'short_name'=>'authorization_service', 'template_name'=>'authorization_service', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'2', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'72', 'name'=>'Zał. 2 Upoważnienie do odbioru odszkodowania za szkodę częściową', 'short_name'=>'authorization_receive_compensation', 'template_name'=>'authorization_receive_compensation', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'2', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'73', 'name'=>'zał. 3 DYSPOZYCJA WYPŁATY ODSZKODOWANIA ZA SZKODĘ CZĘŚCIOWĄ NA KONTO IL', 'short_name'=>'disposition_compensation2', 'template_name'=>'disposition_compensation2', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'3', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'74', 'name'=>'Zał. 4 Pismo do LB o dostarczenie do ubezpieczyciela wymaganych dokumentów', 'short_name'=>'provide_the_insurer_required', 'template_name'=>'provide_the_insurer_required', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'4', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'75', 'name'=>'Zał. 5 c Zlecenie wystawienia FV PRO FROMA', 'short_name'=>'order_of_issue_fv', 'template_name'=>'order_of_issue_fv', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'5', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'76', 'name'=>'Zał. 5 Pismo do LB o wskazanie firmy upoważnionej do odbioru odszkodowania', 'short_name'=>'indication_company_authorized', 'template_name'=>'indication_company_authorized', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'5', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'77', 'name'=>'Zał. 6 Pismo o kosztorysowe rozliczenie szkody częściowej', 'short_name'=>'settlement_partial_loss', 'template_name'=>'settlement_partial_loss', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'1', 'mail'=>'0', 'chronology'=>'6', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'78', 'name'=>'Zał. 7 c Pismo do LB II wezwanie do transportu wraku dla dawnego VB', 'short_name'=>'call_to_transport_wreck2', 'template_name'=>'call_to_transport_wreck2', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'7', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'79', 'name'=>'Zał. 7 Dyspozycja wypłaty na podstawie kosztorysu wariant I na IL', 'short_name'=>'disposition_compensation3', 'template_name'=>'disposition_compensation3', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'7', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'80', 'name'=>'Zał. 9 c Upoważnienie do odbioru odszkodowania na IL Szkoda całkowita', 'short_name'=>'authorization_to_receive_compensation', 'template_name'=>'authorization_to_receive_compensation', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'9', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'81', 'name'=>'Zał. 9 Zgoda na wystawienie faktury za naprawę na IL', 'short_name'=>'consent_to_invoice', 'template_name'=>'consent_to_invoice', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'9', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'82', 'name'=>'Zał. 10 a Pismo do LB WYPŁATA z TU', 'short_name'=>'decision_payments', 'template_name'=>'decision_payments', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'10', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'83', 'name'=>'Zał. 10 b Pismo do LB ODMOWA wypłaty', 'short_name'=>'decision_to_refuse', 'template_name'=>'decision_to_refuse', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'10', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'84', 'name'=>'Zał. 11 Pismo do LB decyzja wypłaty na IL pow 10%', 'short_name'=>'decision_payments_il', 'template_name'=>'decision_payments_il', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'11', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'85', 'name'=>'Protokół przekazania po sprzedaży', 'short_name'=>'transfer_protocol_after_sale', 'template_name'=>'transfer_protocol_after_sale', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'86', 'name'=>'zał. Nr 1. DRUK ZGŁOSZENIA SZKODY/WNIOSEK O WYDANIE UPOWAZNIENIA', 'short_name'=>'wniosek_o_wystawienie_upowaznienia', 'template_name'=>'wniosek_o_wystawienie_upowaznienia', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'1', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'87', 'name'=>'Wniosek o handlowy ubytek wartości pojazdu', 'short_name'=>'request_loss_value', 'template_name'=>'request_loss_value', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'88', 'name'=>'zgłoszenie szkody do TU przez CAS', 'short_name'=>'send_injury_to_ic', 'template_name'=>'send_injury_to_ic', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'1', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'89', 'name'=>'Zał. 6 a Pismo o kosztorysowe rozliczenie szkody na podstawie zdjęć', 'short_name'=>'settlement_partial_loss_photos', 'template_name'=>'settlement_partial_loss_photos', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'6', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'90', 'name'=>'Zał. 5 a Pismo do LB o wskazanie firmy upoważnionej do odbioru odszkodowania i oferta naprawy', 'short_name'=>'indication_company_authorized_repair', 'template_name'=>'indication_company_authorized_repair', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'5', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'91', 'name'=>'Cesja dla serwisów CAS', 'short_name'=>'cession', 'template_name'=>'cession', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'1', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'92', 'name'=>'Cesja AC-GL bez zaległości na LB', 'short_name'=>'cession_AC_LB', 'template_name'=>'cession_AC_LB', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'1', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'93', 'name'=>'Cesja AC-GL z zaległościami na IGL', 'short_name'=>'cession_AC_IGL', 'template_name'=>'cession_AC_IGL', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'1', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'94', 'name'=>'Cesja OC-GL bez zaległości na LB', 'short_name'=>'cession_OC_LB', 'template_name'=>'cession_OC_LB', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'1', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'95', 'name'=>'Cesja OC-GL z zaległościami na IGL', 'short_name'=>'cession_OC_IGL', 'template_name'=>'cession_OC_IGL', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'1', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'96', 'name'=>'GL oświadczenie LB o dokonywanej naprawie na podstawie kosztorysu', 'short_name'=>'rapair_based_on_estiamte', 'template_name'=>'rapair_based_on_estiamte', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'1', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'31', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'97', 'name'=>'Zał. 1 GL- zgłoszenie szkody IGL', 'short_name'=>'injury_report', 'template_name'=>'injury_report', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'1', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'98', 'name'=>'Zał. 1c GL- Informacja o zablokowaniu umowy (szkoda całkowita)', 'short_name'=>'agreement_blocked_info', 'template_name'=>'agreement_blocked_info', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'99', 'name'=>'Zał. 1k GL- upoważnienie do odbioru odszkodowania', 'short_name'=>'compensation_receive_auth_1', 'template_name'=>'compensation_receive_auth_1', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'100', 'name'=>'Zał. 2c GL- Pismo do LB, informacja o szkodzie całkowitej', 'short_name'=>'total_loss_info', 'template_name'=>'total_loss_info', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'101', 'name'=>'Zał. 3c GL- Informacja, pismo do LB o konieczności przetransportowania wraku', 'short_name'=>'wreck_transport_info', 'template_name'=>'wreck_transport_info', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'102', 'name'=>'Zał. 4c GL- Deklaracja do LB', 'short_name'=>'LB_declaration', 'template_name'=>'LB_declaration', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'alert_repurchase', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'103', 'name'=>'Zał. 6c GL- Upoważneinie do odbioru odszkodowania', 'short_name'=>'compensation_receive_auth_2', 'template_name'=>'compensation_receive_auth_2', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'104', 'name'=>'Zał. nr 2 GL UPOWAŻNIENIE DO ODBIORU ODSZKODOWANIA ZA SZKODĘ CZĘŚCIOWĄ', 'short_name'=>'compensation_receive_auth_partial_loss', 'template_name'=>'compensation_receive_auth_partial_loss', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'1', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'105', 'name'=>'Zał. nr 3 GL DYSPOZYCJA WYPŁATY ODSZKODOWANIA NA KONTO IGL', 'short_name'=>'IGL_payment_disposition', 'template_name'=>'IGL_payment_disposition', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'1', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'106', 'name'=>'Zał. nr 5a GL_PISMO DO LB z info o Asyście_pow 3,5 t', 'short_name'=>'LB_assist_info_heavy', 'template_name'=>'LB_assist_info_heavy', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'107', 'name'=>'Zał. nr 5 GL_PISMO DO LB z info o Asyście', 'short_name'=>'LB_assist_info', 'template_name'=>'LB_assist_info', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'108', 'name'=>'Zał. nr 7 GL DYSPOZYCJA WYPŁATY NA PODST. KOSZTORYSU wariant I na IGL', 'short_name'=>'IGL_payment_disposition_estimate', 'template_name'=>'IGL_payment_disposition_estimate', 'task_authorization'=>'1', 'fee'=>'1', 'if_fee_collection'=>'1', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'109', 'name'=>'Zał. nr 8 GL DYSPOZYCJA WYPŁATY NA PODST. KOSZTORYSU wariant II na LB', 'short_name'=>'LB_payment_disposition_estimate', 'template_name'=>'LB_payment_disposition_estimate', 'task_authorization'=>'1', 'fee'=>'0', 'if_fee_collection'=>'1', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'110', 'name'=>'Zał. 5c GL- Deklaracja dotycząca naprawy_dotyczy szkody z OC', 'short_name'=>'service_declaration_OC', 'template_name'=>'service_declaration_OC', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'111', 'name'=>'Zał. 9c Wezwanie do zwrotu przedmiotu po szkodzie', 'short_name'=>'item_return', 'template_name'=>'item_return', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'112', 'name'=>'DYSPOZYCJA ZWROTU IGL', 'short_name'=>'IGL_return_disposition', 'template_name'=>'IGL_return_disposition', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'1', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'113', 'name'=>'Strona tytułowa – dokumenty do rozliczenia', 'short_name'=>'title_page_documents_to_be_settled', 'template_name'=>'title_page_documents_to_be_settled', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'1', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>null, 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'114', 'name'=>'GL - Wniosek o zwrot nadpłaty', 'short_name'=>'GL_refund_request', 'template_name'=>'GL_refund_request', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'1', 'pdf'=>'1', 'mail'=>'0', 'chronology'=>'31', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'115', 'name'=>'GL_Oświadczenie LB o dokonanej naprawie_wniosek o zwrot odszkodowania', 'short_name'=>'GL_repair_compensation_refund', 'template_name'=>'GL_repair_compensation_refund', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'1', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'31', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'116', 'name'=>'IGL Wniosek_dyspozycja zwrotu odszkodowania na serwis lub LB', 'short_name'=>'IGL_compensation_refund', 'template_name'=>'IGL_compensation_refund', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'1', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'2', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'31', 'active'=>'0', ]);
        $this->insertAction($model, ['id'=>'117', 'name'=>'Dane Klienta', 'short_name'=>'client_data', 'template_name'=>'client_data', 'task_authorization'=>'0', 'fee'=>'0', 'if_fee_collection'=>'0', 'if_pure'=>'0', 'alert_name'=>'0', 'conditions'=>'0', 'cfm'=>'0', 'pdf'=>'0', 'mail'=>'0', 'chronology'=>'33', 'active'=>'0', ]);
    }

    private function seed_InjuryStepStage() {
        $model = new InjuryStepStage;

        $this->insertAction($model, ['id'=>'1', 'injury_step_id'=>'0', 'name'=>'niezgłoszona do TU', 'condition'=>'kiedy zaznaczono NIE podczas rejestracji zgłoszenia', 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'2', 'injury_step_id'=>'0', 'name'=>'zgłoszona do TU', 'condition'=>'kiedy zaznaczono TAK podczas rejestracji zgłoszenia lub kiedy przypisany numer szkody', 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'3', 'injury_step_id'=>'0', 'name'=>'wysłano wniosek do LB', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'4', 'injury_step_id'=>'10', 'name'=>'wystawiono UP na IL', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'5', 'injury_step_id'=>'10', 'name'=>'wystawiono UP na LB', 'condition'=>'zaznaczono odbiór odszkodowania "leasingobiorca"', 'next_injury_step_id'=>'23', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'6', 'injury_step_id'=>'10', 'name'=>'wystawiono UP na serwis', 'condition'=>'zaznaczono odbiór odszkodowania "serwis"', 'next_injury_step_id'=>'23', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'7', 'injury_step_id'=>'11', 'name'=>'wysłano zlecenie do serwisu', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'8', 'injury_step_id'=>'11', 'name'=>'wystawiono UP do zlecenia', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'9', 'injury_step_id'=>'20', 'name'=>'odmowa - brak dokum.', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'10', 'injury_step_id'=>'20', 'name'=>'odmowa - tryb odwoławczy', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'11', 'injury_step_id'=>'20', 'name'=>'odmowa zasadna -  zgodna z OWU', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'12', 'injury_step_id'=>'20', 'name'=>'odmowa zasadna - brak dokumentów', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'13', 'injury_step_id'=>'20', 'name'=>'odmowa zasadna - poniżej franszyzy', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'14', 'injury_step_id'=>'20', 'name'=>'odmowa zasadna - brak potwierdzenia przez sprawcę', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'15', 'injury_step_id'=>'20', 'name'=>'odmowa zasadna - rażące niedbalstwo', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'16', 'injury_step_id'=>'20', 'name'=>'odmowa zasadna - brak zabezpieczeń', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'17', 'injury_step_id'=>'20', 'name'=>'odmowa zasadna - przywłaszczenie', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'18', 'injury_step_id'=>'20', 'name'=>'odmowa zasadna - GAP', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'19', 'injury_step_id'=>'20', 'name'=>'odmowa zasadna - inne', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'20', 'injury_step_id'=>'22', 'name'=>'odmowa - brak dokum.', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'21', 'injury_step_id'=>'22', 'name'=>'odmowa - tryb odwoławczy', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'22', 'injury_step_id'=>'22', 'name'=>'odmowa zasadna -  zgodna z OWU', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'23', 'injury_step_id'=>'22', 'name'=>'odmowa zasadna - brak dokumentów', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'24', 'injury_step_id'=>'22', 'name'=>'odmowa zasadna - poniżej franszyzy', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'25', 'injury_step_id'=>'22', 'name'=>'odmowa zasadna - brak potwierdzenia przez sprawcę', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'26', 'injury_step_id'=>'22', 'name'=>'odmowa zasadna - rażące niedbalstwo', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'27', 'injury_step_id'=>'22', 'name'=>'odmowa zasadna - brak zabezpieczeń', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'28', 'injury_step_id'=>'22', 'name'=>'odmowa zasadna - przywłaszczenie', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'29', 'injury_step_id'=>'22', 'name'=>'odmowa zasadna - GAP', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'30', 'injury_step_id'=>'22', 'name'=>'odmowa zasadna - inne', 'condition'=>null, 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'32', 'injury_step_id'=>'23', 'name'=>'wystawiono UP na LB', 'condition'=>'odznaczono wystawiono upoważnienie', 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'33', 'injury_step_id'=>'23', 'name'=>'wystawiono UP na serwis', 'condition'=>'odznaczono wystawiono upoważnienie', 'next_injury_step_id'=>null, 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'34', 'injury_step_id'=>'11', 'name'=>'odmowa - brak dokum.', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'35', 'injury_step_id'=>'11', 'name'=>'odmowa - tryb odwoławczy', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'36', 'injury_step_id'=>'11', 'name'=>'odmowa zasadna -  zgodna z OWU', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'37', 'injury_step_id'=>'11', 'name'=>'odmowa zasadna - brak dokumentów', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'38', 'injury_step_id'=>'11', 'name'=>'odmowa zasadna - poniżej franszyzy', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'39', 'injury_step_id'=>'11', 'name'=>'odmowa zasadna - brak potwierdzenia przez sprawcę', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'40', 'injury_step_id'=>'11', 'name'=>'odmowa zasadna - rażące niedbalstwo', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'41', 'injury_step_id'=>'11', 'name'=>'odmowa zasadna - brak zabezpieczeń', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'42', 'injury_step_id'=>'11', 'name'=>'odmowa zasadna - przywłaszczenie', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'43', 'injury_step_id'=>'11', 'name'=>'odmowa zasadna - GAP', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'44', 'injury_step_id'=>'11', 'name'=>'odmowa zasadna - inne', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'45', 'injury_step_id'=>'10', 'name'=>'odmowa - brak dokum.', 'condition'=>null, 'next_injury_step_id'=>'20', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'46', 'injury_step_id'=>'10', 'name'=>'odmowa - tryb odwoławczy', 'condition'=>null, 'next_injury_step_id'=>'20', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'47', 'injury_step_id'=>'10', 'name'=>'odmowa zasadna -  zgodna z OWU', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'48', 'injury_step_id'=>'10', 'name'=>'odmowa zasadna - brak dokumentów', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'49', 'injury_step_id'=>'10', 'name'=>'odmowa zasadna - poniżej franszyzy', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'50', 'injury_step_id'=>'10', 'name'=>'odmowa zasadna - brak potwierdzenia przez sprawcę', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'51', 'injury_step_id'=>'10', 'name'=>'odmowa zasadna - rażące niedbalstwo', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'52', 'injury_step_id'=>'10', 'name'=>'odmowa zasadna - brak zabezpieczeń', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'53', 'injury_step_id'=>'10', 'name'=>'odmowa zasadna - przywłaszczenie', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'54', 'injury_step_id'=>'10', 'name'=>'odmowa zasadna - GAP', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'55', 'injury_step_id'=>'10', 'name'=>'odmowa zasadna - inne', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'56', 'injury_step_id'=>'0', 'name'=>'odmowa - brak dokum.', 'condition'=>null, 'next_injury_step_id'=>'20', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'57', 'injury_step_id'=>'0', 'name'=>'odmowa - tryb odwoławczy', 'condition'=>null, 'next_injury_step_id'=>'20', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'58', 'injury_step_id'=>'0', 'name'=>'odmowa zasadna -  zgodna z OWU', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'59', 'injury_step_id'=>'0', 'name'=>'odmowa zasadna - brak dokumentów', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'60', 'injury_step_id'=>'0', 'name'=>'odmowa zasadna - poniżej franszyzy', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'61', 'injury_step_id'=>'0', 'name'=>'odmowa zasadna - brak potwierdzenia przez sprawcę', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'62', 'injury_step_id'=>'0', 'name'=>'odmowa zasadna - rażące niedbalstwo', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'63', 'injury_step_id'=>'0', 'name'=>'odmowa zasadna - brak zabezpieczeń', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'64', 'injury_step_id'=>'0', 'name'=>'odmowa zasadna - przywłaszczenie', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'65', 'injury_step_id'=>'0', 'name'=>'odmowa zasadna - GAP', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'66', 'injury_step_id'=>'0', 'name'=>'odmowa zasadna - inne', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'67', 'injury_step_id'=>'23', 'name'=>'odmowa - brak dokum.', 'condition'=>null, 'next_injury_step_id'=>'20', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'68', 'injury_step_id'=>'23', 'name'=>'odmowa - tryb odwoławczy', 'condition'=>null, 'next_injury_step_id'=>'20', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'69', 'injury_step_id'=>'23', 'name'=>'odmowa zasadna -  zgodna z OWU', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'70', 'injury_step_id'=>'23', 'name'=>'odmowa zasadna - brak dokumentów', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'71', 'injury_step_id'=>'23', 'name'=>'odmowa zasadna - poniżej franszyzy', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'72', 'injury_step_id'=>'23', 'name'=>'odmowa zasadna - brak potwierdzenia przez sprawcę', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'73', 'injury_step_id'=>'23', 'name'=>'odmowa zasadna - rażące niedbalstwo', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'74', 'injury_step_id'=>'23', 'name'=>'odmowa zasadna - brak zabezpieczeń', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'75', 'injury_step_id'=>'23', 'name'=>'odmowa zasadna - przywłaszczenie', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'76', 'injury_step_id'=>'23', 'name'=>'odmowa zasadna - GAP', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'77', 'injury_step_id'=>'23', 'name'=>'odmowa zasadna - inne', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'1', ]);
        $this->insertAction($model, ['id'=>'78', 'injury_step_id'=>'26', 'name'=>'wygenerowano upoważnienie do odbioru odszkodowania Zał. 2 GL', 'condition'=>null, 'next_injury_step_id'=>'23', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'79', 'injury_step_id'=>'26', 'name'=>'podpięcie Decyzji ZU', 'condition'=>null, 'next_injury_step_id'=>'15', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'80', 'injury_step_id'=>'26', 'name'=>'odmowa zasadna', 'condition'=>null, 'next_injury_step_id'=>'24', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'81', 'injury_step_id'=>'20', 'name'=>'---', 'condition'=>null, 'next_injury_step_id'=>'23', 'parent_stage_id'=>'78', 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'82', 'injury_step_id'=>'20', 'name'=>'---', 'condition'=>null, 'next_injury_step_id'=>'22', 'parent_stage_id'=>'7', 'next_step_condition'=>'0', ]);
        $this->insertAction($model, ['id'=>'83', 'injury_step_id'=>'20', 'name'=>'decyzja ZU', 'condition'=>null, 'next_injury_step_id'=>'15', 'parent_stage_id'=>null, 'next_step_condition'=>'0', ]);

    }

    private function seed_injury_step_stage_uploaded_document_type() {
        $table = 'injury_step_stage_uploaded_document_type';

        $this->insertPivotAction($table, ['injury_step_stage_id'=>'11', 'injury_uploaded_document_type_id'=>'28', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'12', 'injury_uploaded_document_type_id'=>'29', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'13', 'injury_uploaded_document_type_id'=>'30', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'14', 'injury_uploaded_document_type_id'=>'31', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'15', 'injury_uploaded_document_type_id'=>'32', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'16', 'injury_uploaded_document_type_id'=>'33', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'17', 'injury_uploaded_document_type_id'=>'34', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'18', 'injury_uploaded_document_type_id'=>'35', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'19', 'injury_uploaded_document_type_id'=>'36', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'9', 'injury_uploaded_document_type_id'=>'25', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'10', 'injury_uploaded_document_type_id'=>'26', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'20', 'injury_uploaded_document_type_id'=>'25', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'21', 'injury_uploaded_document_type_id'=>'26', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'24', 'injury_uploaded_document_type_id'=>'30', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'25', 'injury_uploaded_document_type_id'=>'31', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'26', 'injury_uploaded_document_type_id'=>'32', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'27', 'injury_uploaded_document_type_id'=>'33', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'28', 'injury_uploaded_document_type_id'=>'34', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'29', 'injury_uploaded_document_type_id'=>'35', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'30', 'injury_uploaded_document_type_id'=>'36', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'22', 'injury_uploaded_document_type_id'=>'28', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'23', 'injury_uploaded_document_type_id'=>'29', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'34', 'injury_uploaded_document_type_id'=>'25', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'35', 'injury_uploaded_document_type_id'=>'26', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'36', 'injury_uploaded_document_type_id'=>'28', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'37', 'injury_uploaded_document_type_id'=>'29', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'38', 'injury_uploaded_document_type_id'=>'30', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'39', 'injury_uploaded_document_type_id'=>'31', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'40', 'injury_uploaded_document_type_id'=>'32', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'41', 'injury_uploaded_document_type_id'=>'33', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'42', 'injury_uploaded_document_type_id'=>'34', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'43', 'injury_uploaded_document_type_id'=>'35', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'44', 'injury_uploaded_document_type_id'=>'36', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'45', 'injury_uploaded_document_type_id'=>'25', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'46', 'injury_uploaded_document_type_id'=>'26', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'47', 'injury_uploaded_document_type_id'=>'28', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'48', 'injury_uploaded_document_type_id'=>'29', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'49', 'injury_uploaded_document_type_id'=>'30', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'50', 'injury_uploaded_document_type_id'=>'31', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'51', 'injury_uploaded_document_type_id'=>'32', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'52', 'injury_uploaded_document_type_id'=>'33', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'53', 'injury_uploaded_document_type_id'=>'34', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'54', 'injury_uploaded_document_type_id'=>'35', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'55', 'injury_uploaded_document_type_id'=>'36', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'56', 'injury_uploaded_document_type_id'=>'25', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'57', 'injury_uploaded_document_type_id'=>'26', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'58', 'injury_uploaded_document_type_id'=>'28', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'59', 'injury_uploaded_document_type_id'=>'29', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'60', 'injury_uploaded_document_type_id'=>'30', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'61', 'injury_uploaded_document_type_id'=>'31', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'62', 'injury_uploaded_document_type_id'=>'32', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'63', 'injury_uploaded_document_type_id'=>'33', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'64', 'injury_uploaded_document_type_id'=>'34', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'65', 'injury_uploaded_document_type_id'=>'35', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'66', 'injury_uploaded_document_type_id'=>'36', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'69', 'injury_uploaded_document_type_id'=>'28', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'70', 'injury_uploaded_document_type_id'=>'29', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'71', 'injury_uploaded_document_type_id'=>'30', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'72', 'injury_uploaded_document_type_id'=>'31', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'73', 'injury_uploaded_document_type_id'=>'32', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'74', 'injury_uploaded_document_type_id'=>'33', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'75', 'injury_uploaded_document_type_id'=>'34', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'76', 'injury_uploaded_document_type_id'=>'35', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'77', 'injury_uploaded_document_type_id'=>'36', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'79', 'injury_uploaded_document_type_id'=>'6', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'80', 'injury_uploaded_document_type_id'=>'28', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'80', 'injury_uploaded_document_type_id'=>'29', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'80', 'injury_uploaded_document_type_id'=>'30', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'80', 'injury_uploaded_document_type_id'=>'31', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'80', 'injury_uploaded_document_type_id'=>'32', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'80', 'injury_uploaded_document_type_id'=>'33', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'80', 'injury_uploaded_document_type_id'=>'34', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'80', 'injury_uploaded_document_type_id'=>'35', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'80', 'injury_uploaded_document_type_id'=>'36', ]);
        $this->insertPivotAction($table, ['injury_step_stage_id'=>'83', 'injury_uploaded_document_type_id'=>'6', ]);
        
    }

    private function seed_injury_step_stage_document_type() {
        $table = 'injury_step_stage_document_type';

		$this->insertPivotAction($table, ['injury_step_stage_id'=>'3', 'injury_document_type_id'=>'86', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'5', 'injury_document_type_id'=>'72', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'7', 'injury_document_type_id'=>'60', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'8', 'injury_document_type_id'=>'8', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'8', 'injury_document_type_id'=>'53', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'8', 'injury_document_type_id'=>'71', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'4', 'injury_document_type_id'=>'73', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'5', 'injury_document_type_id'=>'68', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'4', 'injury_document_type_id'=>'79', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'7', 'injury_document_type_id'=>'52', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'32', 'injury_document_type_id'=>'109', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'32', 'injury_document_type_id'=>'72', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'33', 'injury_document_type_id'=>'72', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'3', 'injury_document_type_id'=>'97', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'6', 'injury_document_type_id'=>'72', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'7', 'injury_document_type_id'=>'6', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'5', 'injury_document_type_id'=>'104', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'4', 'injury_document_type_id'=>'105', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'4', 'injury_document_type_id'=>'108', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'32', 'injury_document_type_id'=>'68', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'5', 'injury_document_type_id'=>'109', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'32', 'injury_document_type_id'=>'104', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'33', 'injury_document_type_id'=>'104', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'6', 'injury_document_type_id'=>'104', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'78', 'injury_document_type_id'=>'104', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'81', 'injury_document_type_id'=>'104', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'81', 'injury_document_type_id'=>'109', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'81', 'injury_document_type_id'=>'72', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'81', 'injury_document_type_id'=>'68', ]);
		$this->insertPivotAction($table, ['injury_step_stage_id'=>'82', 'injury_document_type_id'=>'60', ]);

    }

    private function seed_task_steps(){
        $model = new TaskStep;

        $this->insertAction($model, ['id' => '1', 'name' => 'nowa']);
        $this->insertAction($model, ['id' => '2', 'name' => 'w realizacji']);
        $this->insertAction($model, ['id' => '3', 'name' => 'przekazana']);
        $this->insertAction($model, ['id' => '4', 'name' => 'zakończona']);
        $this->insertAction($model, ['id' => '5', 'name' => 'zakończona bez czynności']);
        $this->insertAction($model, ['id' => '6', 'name' => 'przekazana z powodu nieobecności']);
        $this->insertAction($model, ['id' => '7', 'name' => 'pobrana przez pracownika']);
    }

    private function seed_task_groups(){
        $model = new TaskGroup;

        $this->insertAction($model, ['id' => '1', 'name' => 'Szkody częściowe', 'ord' => 1]);
        $this->insertAction($model, ['id' => '2', 'name' => 'Szkody CFM', 'ord' => 3]);
        $this->insertAction($model, ['id' => '3', 'name' => 'Szkody całkowite', 'ord' => 4]);
        $this->insertAction($model, ['id' => '4', 'name' => 'Szkody częściowe (asysta)', 'ord' => 2]);
    }

    private function seed_task_subgroups(){
        $model = new TaskSubgroup;

        $this->insertAction($model, ['id' => '1', 'task_group_id' => 1, 'name' => 'poza asystą']);
        $this->insertAction($model, ['id' => '2', 'task_group_id' => 1,  'name' => 'asysta']);
    }

    private function seed_task_types(){
        $model = new TaskType;

        $this->insertAction($model, ['id' => '1', 'name' => 'zgłoszenie szkody', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '2', 'name' => 'wniosek o wydanie upoważnienia', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '3', 'name' => 'pismo', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '4', 'name' => 'kosztorysowe rozliczenie szkody', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '5', 'name' => 'zwrot odszkodowania', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '6', 'name' => 'cesja wierzytelności', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '7', 'name' => 'reklamacje', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '8', 'name' => 'windykacja', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '9', 'name' => 'asysta', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '10', 'name' => 'odwołania', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '11', 'name' => 'wniosek o przejęcie faktury', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '12', 'name' => 'inne', 'task_group_id'=>1, 'task_subgroup_id' =>1]);
        $this->insertAction($model, ['id' => '13', 'name' => 'Bank Kierowcy', 'task_group_id'=>1, 'task_subgroup_id' =>1],true);
        $this->insertAction($model, ['id' => '14', 'name' => 'zgłoszenie online', 'task_group_id'=>1, 'task_subgroup_id' =>1], true);


        $this->insertAction($model, ['id' => '15', 'name' => 'pisma', 'task_group_id'=>4, 'task_subgroup_id' =>2]);
        $this->insertAction($model, ['id' => '16', 'name' => 'rejestracja szkody', 'task_group_id'=>4, 'task_subgroup_id' =>2]);
        $this->insertAction($model, ['id' => '17', 'name' => 'obsługa szkody', 'task_group_id'=>4, 'task_subgroup_id' =>2]);
        $this->insertAction($model, ['id' => '18', 'name' => 'Bank Kierowcy', 'task_group_id'=>4, 'task_subgroup_id' =>2]);
        $this->insertAction($model, ['id' => '19', 'name' => 'reklamacje/odwołania', 'task_group_id'=>4, 'task_subgroup_id' =>2]);

        $this->insertAction($model, ['id' => '20', 'name' => 'rejestracja szkody', 'task_group_id'=>2]);
        $this->insertAction($model, ['id' => '21', 'name' => 'zlecenie naprawy', 'task_group_id'=>2]);
        $this->insertAction($model, ['id' => '22', 'name' => 'obsługa szkody', 'task_group_id'=>2]);
        $this->insertAction($model, ['id' => '23', 'name' => 'rozliczenie', 'task_group_id'=>2]);
        $this->insertAction($model, ['id' => '24', 'name' => 'reklamacje', 'task_group_id'=>2]);
        $this->insertAction($model, ['id' => '25', 'name' => 'korespondencja z właścicielem', 'task_group_id'=>2]);
        $this->insertAction($model, ['id' => '26', 'name' => 'korespondencja z klientem', 'task_group_id'=>2]);
        $this->insertAction($model, ['id' => '27', 'name' => 'cesje', 'task_group_id'=>2]);
        $this->insertAction($model, ['id' => '28', 'name' => 'Assistance', 'task_group_id'=>2]);
        $this->insertAction($model, ['id' => '29', 'name' => 'obsługa pism', 'task_group_id'=>2]);

        $this->insertAction($model, ['id' => '30', 'name' => 'korespondencja z TU', 'task_group_id'=>3]);
        $this->insertAction($model, ['id' => '31', 'name' => 'korespondencja z oferentami', 'task_group_id'=>3]);
        $this->insertAction($model, ['id' => '32', 'name' => 'korespondencja z LB/PB', 'task_group_id'=>3]);
        $this->insertAction($model, ['id' => '33', 'name' => 'korespondencja z IGL', 'task_group_id'=>3]);
        $this->insertAction($model, ['id' => '34', 'name' => 'reklamacje', 'task_group_id'=>3]);
        $this->insertAction($model, ['id' => '35', 'name' => 'korespondencja z windykacją', 'task_group_id'=>3]);
        $this->insertAction($model, ['id' => '36', 'name' => 'korespondencja z rejestratorami', 'task_group_id'=>3]);
        $this->insertAction($model, ['id' => '37', 'name' => 'korespondencja z placem IGL', 'task_group_id'=>3]);
        $this->insertAction($model, ['id' => '38', 'name' => 'korespondencja z MFE', 'task_group_id'=>3]);
    }

    private function seed_task_sources(){
        $model = new TaskSource;

        $this->insertAction($model, ['id' => '1', 'name' => 'mail', 'style' => 'info']);
        $this->insertAction($model, ['id' => '2', 'name' => 'druk online', 'style' => 'primary']);
        $this->insertAction($model, ['id' => '3', 'name' => 'pismo przychodzące', 'style' => 'success']);
        $this->insertAction($model, ['id' => '4', 'name' => 'zadanie terminowe', 'style' => 'default']);
    }

    private function seed_historyType() {
        $model = new History_type;

        $this->insertAction($model, ['id'=>'1', 'content'=>'Wprowadzenie szkody do systemu.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'2', 'content'=>'Przypisanie nr szkody.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'3', 'content'=>'Ustalenie przewidywanego terminu naprawy.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'4', 'content'=>'Ustalenie przewidywanego kosztu naprawy.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'5', 'content'=>'Ustawienie odbioru odszkodowania.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'6', 'content'=>'Edycja świadków zdarzenia.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'7', 'content'=>'Ustalenie cechy: \'zawiadomiono policję\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'8', 'content'=>'Ustalenie cechy: \'spisano oświadczenia\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'9', 'content'=>'Ustalenie cechy: \'czy zabrano dowód rejestracyjny\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'10', 'content'=>'Ustalenie cechy: \'kolizja z innym pojazdem\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'11', 'content'=>'Ustalenie cechy: \'potrącenie pieszego/ innego uczestnika ruchu\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'12', 'content'=>'Ustalenie cechy: \'kolizja ze zwierzęciem\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'13', 'content'=>'Ustalenie cechy: \'szkoda parkingowa, sprawca nieznany\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'14', 'content'=>'Ustalenie cechy: \'włamanie do pojazdu\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'15', 'content'=>'Ustalenie cechy: \'akt wandalizmu\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'16', 'content'=>'Ustalenie cechy: \'uszkodzenie w wyniku działania sił natury\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'17', 'content'=>'Ustalenie cechy: \'kradzież części/ wyposażenia\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'18', 'content'=>'Ustalenie cechy: \'kolizja z przedmiotem\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'19', 'content'=>'Ustalenie cechy: \'inne\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'20', 'content'=>'Wprowadzenie dokumentu.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'21', 'content'=>'Usunięcie dokumentu.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'22', 'content'=>'Wprowadzenie zdjęcia.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'23', 'content'=>'Usunięcie zdjęcia.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'25', 'content'=>'Dodanie problemu do sprawy.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'26', 'content'=>'Dodanie uwagi do sprawy.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'27', 'content'=>'Zmiana statusu weryfikacji na "do akceptacji".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'28', 'content'=>'Zmiana statusu weryfikacji na "poprawione".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'29', 'content'=>'Przeniesienie szkody do anulowanych.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'30', 'content'=>'Zakwalifikowanie szkody jako szkoda całkowita.', 'injury_processing_type_id'=>'2', ]);
        $this->insertAction($model, ['id'=>'31', 'content'=>'Przypisanie warsztatu.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'32', 'content'=>'Zmiana statusu "Telefon do assistance".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'33', 'content'=>'Zmiana statusu "Oględziny".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'34', 'content'=>'Zmiana statusu "Dodatkowe oględziny".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'35', 'content'=>'Zmiana statusu "Akceptacja techniczna".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'36', 'content'=>'Zmiana statusu "Części zamówione".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'37', 'content'=>'Zmiana statusu "Części na miejscu".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'38', 'content'=>'Przeniesie szkody do "w trakcie naprawy.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'39', 'content'=>'Przeniesienie do zrealizowanych.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'40', 'content'=>'Ustalenie uszkodzenia: \'zderzak przedni\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'41', 'content'=>'Ustalenie uszkodzenia: \'atrapa przednia\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'42', 'content'=>'Ustalenie uszkodzenia: \'maska przednia\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'43', 'content'=>'Ustalenie uszkodzenia: \'osprzęt silnika\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'44', 'content'=>'Ustalenie uszkodzenia: \'szyba czołowa\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'45', 'content'=>'Ustalenie uszkodzenia: \'dach\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'46', 'content'=>'Ustalenie uszkodzenia: \'reflektor przedni\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'47', 'content'=>'Ustalenie uszkodzenia: \'lampa boczna\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'48', 'content'=>'Ustalenie uszkodzenia: \'błotnik przedni\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'49', 'content'=>'Ustalenie uszkodzenia: \'koło przednie\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'50', 'content'=>'Ustalenie uszkodzenia: \'zawieszenie przednie\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'51', 'content'=>'Ustalenie uszkodzenia: \'drzwi przednie\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'52', 'content'=>'Ustalenie uszkodzenia: \'szyba drzwi przednich\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'53', 'content'=>'Ustalenie uszkodzenia: \'lusterko boczne\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'54', 'content'=>'Ustalenie uszkodzenia: \'zamek drzwi przednich\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'55', 'content'=>'Ustalenie uszkodzenia: \'próg\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'56', 'content'=>'Ustalenie uszkodzenia: \'drzwi tylne\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'57', 'content'=>'Ustalenie uszkodzenia: \'szyba drzwi tylnych\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'58', 'content'=>'Ustalenie uszkodzenia: \'błotnik tylny\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'59', 'content'=>'Ustalenie uszkodzenia: \'koło tylne\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'60', 'content'=>'Ustalenie uszkodzenia: \'zawieszenie tylne\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'61', 'content'=>'Ustalenie uszkodzenia: \'szyba boczna tylna\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'62', 'content'=>'Ustalenie uszkodzenia: \'lampa tylna\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'63', 'content'=>'Ustalenie uszkodzenia: \'pokrywa tylna\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'64', 'content'=>'Ustalenie uszkodzenia: \'szyba tylna\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'65', 'content'=>'Ustalenie uszkodzenia: \'zderzak tylny\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'66', 'content'=>'Ustalenie uszkodzenia: \'podłoga\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'67', 'content'=>'Ustalenie uszkodzenia: \'osłona dolna\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'68', 'content'=>'Ustalenie uszkodzenia: \'poduszka powietrzna\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'69', 'content'=>'Ustalenie uszkodzenia: \'kołpaki\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'70', 'content'=>'Ustalenie uszkodzenia: \'akcesoria\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'71', 'content'=>'Ustalenie uszkodzenia: \'oklejenia\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'72', 'content'=>'Odebranie sprawy.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'73', 'content'=>'Przyjęcie szkody.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'74', 'content'=>'Przeniesienie do zakończonych.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'75', 'content'=>'Ustalenie wartości szkody.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'76', 'content'=>'Zgłoszenie reklamacji', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'77', 'content'=>'Ustalanie konieczności holowania.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'78', 'content'=>'Ustalanie konieczności samochodu zastępczego.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'79', 'content'=>'Zmiana opisu zdarzenia.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'80', 'content'=>'Zmiana uwag do zdarzenia.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'81', 'content'=>'Przywrócenie szkody ze stanu \'szkoda całkowita, kradzież\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'82', 'content'=>'Przywrócenie szkody ze stanu \'szkoda anulowana\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'83', 'content'=>'Zmiana statusu "Akceptacja faktury".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'84', 'content'=>'Zmiana statusu "Zgoda warsztatu".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'85', 'content'=>'Zmiana statusu "Weryfikacja".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'86', 'content'=>'Zmiana statusu "Akceptacja faktury".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'87', 'content'=>'Zatwierdzenie dokumentu.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'88', 'content'=>'Aktualizacja dokumentu.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'89', 'content'=>'Zgłoszenie kosztorysu Polcar do akceptacji', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'90', 'content'=>'Akceptacja kosztorysu Polcar', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'91', 'content'=>'Edycja danych zgłaszającego', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'92', 'content'=>'Edycja danych zdarzenia', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'93', 'content'=>'Edycja płatnika VAT', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'94', 'content'=>'Akceptacja kosztorysu naprawy (poziom CSV)', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'95', 'content'=>'Odrzucenie kosztorysu naprawy (poziom CSV)', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'96', 'content'=>'Akceptacja kosztorysu naprawy (poziom klienta)', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'97', 'content'=>'Odrzucenie kosztorysu naprawy (poziom klienta)', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'98', 'content'=>'Rozpoczęcie naprawy', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'99', 'content'=>'Odrzucenie realizacji naprawy', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'100', 'content'=>'Zmiana statusu "Części Polcar"', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'101', 'content'=>'Zakończenie naprawy', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'102', 'content'=>'Zmiana w zakresie naprawy', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'103', 'content'=>'Zmiana w informacjach dodatkowych', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'104', 'content'=>'Zamknięcie rozliczenia faktury', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'105', 'content'=>'Przypisanie naprawy do warsztatu', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'106', 'content'=>'Przyjęcie naprawy przez warsztat', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'107', 'content'=>'Zmiana typu szkody - ', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'108', 'content'=>'Zamiana odbiorcy odszkodowania. ', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'109', 'content'=>'Zmiana daty zdarzenia - ', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'110', 'content'=>'Zmiana statusu "Samochód odebrany przez klienta".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'111', 'content'=>'Zmiana statusu "Upoważnienie".', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'112', 'content'=>'Oblokowanie szkody.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'113', 'content'=>'Zablokowanie szkody.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'114', 'content'=>'Zakończenie szkody w trybie normalnym.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'115', 'content'=>'Zakończenie szkody bez likwidacji.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'116', 'content'=>'Zakończenie szkody bez naprawy.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'117', 'content'=>'Odmowa zakładu ubezpieczeń.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'118', 'content'=>'Zakwalifikowanie szkody jako kradzież.', 'injury_processing_type_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'119', 'content'=>'Zmiana danych kierowcy.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'120', 'content'=>'Zmiana danych zgłaszającego szkodę.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'121', 'content'=>'Zmiana danych kontaktowych klienta.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'122', 'content'=>'Zmiana miejsca zdarzenia szkody.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'123', 'content'=>'Ustalenie terminu przyjęcie.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'124', 'content'=>'Zamiana odbiorcy faktury.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'125', 'content'=>'Ustalenie door2door.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'126', 'content'=>'Wygenerowanie dokumentu.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'127', 'content'=>'Zmiana zakładu ubezpieczeń.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'128', 'content'=>'Notka własna.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'129', 'content'=>'Edycja informacji wewnętrznej.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'130', 'content'=>'Edycja uwag do uszkodzeń.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'131', 'content'=>'Procedowanie szkody bez serwisu.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'132', 'content'=>'Ustalenie cechy: \'uszkodzenie szyby\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'133', 'content'=>'Kartoteka zlecenia otwarta przez pracownika Infolinii.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'134', 'content'=>'Zamknięcie rozmowy.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'135', 'content'=>'Zmiana danych sprawcy.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'136', 'content'=>'Zmiana osoby kontaktowej.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'137', 'content'=>'Wysłanie sms z informacją o zarejestrowaniu szkody na nr:', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'138', 'content'=>'Wysłanie sms z informacją o warsztacie wykonującym naprawę na nr:', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'139', 'content'=>'Ustalenie cechy: \'przywłaszczenie\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'140', 'content'=>'Przywrócenie szkody.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'141', 'content'=>'Wysłanie wiadomości sms z bramki.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'142', 'content'=>'Przeniesienie szkody do zakończonych totalnie.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'143', 'content'=>'Zmiana daty parametru:', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'144', 'content'=>'Potwierdzono:', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'145', 'content'=>'Zmieniono wartość parametru:', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'146', 'content'=>'Przekazano do DOK.
        ', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'147', 'content'=>'Wysłanie prośby o fakturę pro forma.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'148', 'content'=>'Wysłanie prośby o fakturę.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'149', 'content'=>'Odtwierdzono: ', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'150', 'content'=>'Ustalenie cechy:', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'151', 'content'=>'Zlecenie w obsłudze.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'152', 'content'=>'Zakwalifikowanie szkody jako szkoda totalna.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'153', 'content'=>'Edycja danych pojazdu.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'154', 'content'=>'Edycja danych klienta', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'155', 'content'=>'Edycja danych odszkodowania.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'156', 'content'=>'Edycja danych przedmiotu umowy.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'157', 'content'=>'Ustalenie kosztorysowe rozliczenie.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'158', 'content'=>'Przypisanie pisma do szkody.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'159', 'content'=>'Zmiana opisu szkody.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'160', 'content'=>'Zmiana właściciela pojazdu.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'161', 'content'=>'Rozpoczęcie ponownej sprzedaży.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'162', 'content'=>'Przeniesienie szkody do rozliczenia.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'163', 'content'=>'Szkoda rozliczona.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'164', 'content'=>'Rezygnacja z rozszczeń', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'165', 'content'=>'Przywrócenie szkody na etap \'w obsłudze\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'166', 'content'=>'Ustalenie cechy: \'wina kierowcy\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'167', 'content'=>'Przypisanie prowadzącego.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'168', 'content'=>'Wysłanie dokumentów.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'169', 'content'=>'Cofnięcie nadesłania dokumentu.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'170', 'content'=>'Usunięcie faktury.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'171', 'content'=>'Usunięcie odszkodowania.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'172', 'content'=>'Przypisanie prowadzącego rozliczenia.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'173', 'content'=>'Zakończenie szkody odmową ZU.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'174', 'content'=>'Zakończenie szkody - wystawiono upoważnienie', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'175', 'content'=>'Ustawienie etapu naprawy', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'176', 'content'=>'Zmienie etapu kradzieży na przekazane do DOSU do rozliczenia', 'injury_processing_type_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'177', 'content'=>'Zmienie etapu kradzieży na zakończona - brak znamion czynu karalnego', 'injury_processing_type_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'178', 'content'=>'Zmiana statusu na kradzież zakończona odmową', 'injury_processing_type_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'179', 'content'=>'Zmiana statusu na kradzież zakończona wypłatą', 'injury_processing_type_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'180', 'content'=>'Zmiana statusu szkoda całkowita zakończona wypłatą ', 'injury_processing_type_id'=>'2', ]);
        $this->insertAction($model, ['id'=>'181', 'content'=>'Zmiana statusu szkoda całkowita zakończona odmową ', 'injury_processing_type_id'=>'2', ]);
        $this->insertAction($model, ['id'=>'182', 'content'=>'Zmiana statusu na szkoda całkowita brak roszczeń', 'injury_processing_type_id'=>'2', ]);
        $this->insertAction($model, ['id'=>'183', 'content'=>'Zmiana statusu na szkoda całkowita umowa rozliczona', 'injury_processing_type_id'=>'2', ]);
        $this->insertAction($model, ['id'=>'184', 'content'=>'Zmiana etapu na oczekiwanie na ofertanta', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'185', 'content'=>'Zmiana etapu na wrak w trakcie sprzedaży', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'186', 'content'=>'Zmiana etapu na zakończona sprzedaż wraka', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'187', 'content'=>'Zmiana etapu na ponowna sprzedaż', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'188', 'content'=>'Zmiana etapu na sprzedaż realizowana przez DSP', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'189', 'content'=>'Zmiana etapu na brak sprzedaży', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'190', 'content'=>'Zmiana etapu na sprzedaż kompesata', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'191', 'content'=>'Zmiana statusu na szkoda całkowita w trakcie rozliczenia', 'injury_processing_type_id'=>'2', ]);
        $this->insertAction($model, ['id'=>'192', 'content'=>'Edycja danych kosztorysu', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'193', 'content'=>'Usunięcie Kosztorysu', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'194', 'content'=>'Zmiana statusu na kradzież umowa rozliczona', 'injury_processing_type_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'195', 'content'=>'Zmiana etapu procesowania', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'196', 'content'=>'Wysłanie druku "Zgłoszenia szkody do TU przez EDB"', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'197', 'content'=>'Ustalenie uszkodzenia: \'zbiornik paliwa\'.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'198', 'content'=>'Zmienie etapu kradzieży na zakończona totalnie - kradzież przywłaszczenie.', 'injury_processing_type_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'199', 'content'=>'Usunięcie prowadzącego.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'200', 'content'=>'Zmiana statusu na zakończone wypłatą', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'201', 'content'=>'Zmiana statusu na zakończone odmową', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'202', 'content'=>'Zmiana statusu na całkowita wypłata ', 'injury_processing_type_id'=>'2', ]);
        $this->insertAction($model, ['id'=>'203', 'content'=>'Zmiana statusu na całkowita odmowa ', 'injury_processing_type_id'=>'2', ]);
        $this->insertAction($model, ['id'=>'204', 'content'=>'Zmiana statusu na kradzież wypłata ', 'injury_processing_type_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'205', 'content'=>'Zmiana statusu na kradzież odmowa ', 'injury_processing_type_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'206', 'content'=>'Zmiana etapu na zakończona bez asysty', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'207', 'content'=>'Wysłanie sms z informacją RODO', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'208', 'content'=>'Przekazanie faktury', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'209', 'content'=>'Zwrot faktury', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'210', 'content'=>'Przekazanie ponowne faktury', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'211', 'content'=>'Zmiana etapu na zakończona bez asysty - szkoda całkowita', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'212', 'content'=>'Edycja danych szkody', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'213', 'content'=>'Przepięta na status', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'214', 'content'=>'Powiązanie szkody z poczekalni ze szkodą w DLS', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'215', 'content'=>'Zmiana przypisanych numerów kont bankowych', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'216', 'content'=>'Zarejestrowano w SAP.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'217', 'content'=>'Wysłano zmiany do SAP.', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'218', 'content'=>'Przywrócono status szkody na \'', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'219', 'content'=>'Zmiana statusu na', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'220', 'content'=>'Przypięcie zadania', 'injury_processing_type_id'=>null, ]);
        $this->insertAction($model, ['id'=>'221', 'content'=>'Zmiana statusu na umowa rozliczona', 'injury_processing_type_id'=>'1', ]);
        $this->insertAction($model, ['id'=>'222', 'content'=>'Odpięcie zadania', 'injury_processing_type_id'=>null, ]);

    }

    private function seed_dos_other_injury_steps () {
        $model = new DosOtherInjurySteps;

        $this->insertAction($model, ['id'=>'-10', 'name'=>'anulowana', ]);
        $this->insertAction($model, ['id'=>'0', 'name'=>'zarejestrowana', ]);
        $this->insertAction($model, ['id'=>'10', 'name'=>'w obsłudze', ]);
        $this->insertAction($model, ['id'=>'15', 'name'=>'zakończone w trybie normalnym', ]);
        $this->insertAction($model, ['id'=>'17', 'name'=>'zakończone bez likwidacji', ]);
        $this->insertAction($model, ['id'=>'19', 'name'=>'zakończone bez naprawy', ]);
        $this->insertAction($model, ['id'=>'20', 'name'=>'zakończona wypłatą', ]);
        $this->insertAction($model, ['id'=>'21', 'name'=>'zakończona odmową', ]);
        $this->insertAction($model, ['id'=>'25', 'name'=>'całkowita', ]);
        $this->insertAction($model, ['id'=>'26', 'name'=>'całkowita wypłata', ]);
        $this->insertAction($model, ['id'=>'27', 'name'=>'całkowita odmowa', ]);
        $this->insertAction($model, ['id'=>'28', 'name'=>'całkowita zakończona wypłata', ]);
        $this->insertAction($model, ['id'=>'29', 'name'=>'całkowita zakończona odmową', ]);
        $this->insertAction($model, ['id'=>'30', 'name'=>'kradzież', ]);
        $this->insertAction($model, ['id'=>'31', 'name'=>'kradzież wypłata', ]);
        $this->insertAction($model, ['id'=>'32', 'name'=>'kradzież odmowa', ]);
        $this->insertAction($model, ['id'=>'33', 'name'=>'kradzież zakończona wypłata', ]);
        $this->insertAction($model, ['id'=>'34', 'name'=>'kradzież zakończona odmową', ]);
        $this->insertAction($model, ['id'=>'41', 'name'=>'częsciowa rezygnacja z roszczeń', ]);
        $this->insertAction($model, ['id'=>'42', 'name'=>'szkoda całkowita rezygnacja z roszczeń', ]);
        $this->insertAction($model, ['id'=>'43', 'name'=>'kradzież rezygnacja z roszczeń', ]);

        $this->insertAction($model, ['id'=>'44', 'name'=>'szkoda całkowita umowa rozliczona', ]);
        $this->insertAction($model, ['id'=>'45', 'name'=>'kradzież umowa rozliczona', ]);
        $this->insertAction($model, ['id'=>'46', 'name'=>'częsciowa umowa rozliczona', ]);

    }

    private function seed_injuries_types () {
        $model = new Injuries_type;

        $this->insertAction($model, ['id'=>'1', 'name'=>'AC', 'sap_name'=>'AC', 'if_injury_vehicle'=>'1', 'if_injury_other'=>'1', ]);
        $this->insertAction($model, ['id'=>'2', 'name'=>'OC', 'sap_name'=>'OCK', 'if_injury_vehicle'=>'1', 'if_injury_other'=>'1', ]);
        $this->insertAction($model, ['id'=>'3', 'name'=>'własna', 'sap_name'=>'AC', 'if_injury_vehicle'=>'1', 'if_injury_other'=>'0', ]);
        $this->insertAction($model, ['id'=>'4', 'name'=>'AC-regres', 'sap_name'=>'AC', 'if_injury_vehicle'=>'1', 'if_injury_other'=>'0', ]);
        $this->insertAction($model, ['id'=>'5', 'name'=>'OC-BLS', 'sap_name'=>'OCK', 'if_injury_vehicle'=>'1', 'if_injury_other'=>'1', ]);
        $this->insertAction($model, ['id'=>'6', 'name'=>'OC własne', 'sap_name'=>'OCK', 'if_injury_vehicle'=>'1', 'if_injury_other'=>'1', ]);
        $this->insertAction($model, ['id'=>'7', 'name'=>'AC Obce', 'sap_name'=>'AC', 'if_injury_vehicle'=>'1', 'if_injury_other'=>'1', ]);
        $this->insertAction($model, ['id'=>'8', 'name'=>'Auto-szyba', 'sap_name'=>'', 'if_injury_vehicle'=>'1', 'if_injury_other'=>'0', ]);
    }

    private function seed_modules(){
        $model = new Module;

        $this->insertAction($model, ['id'=>'1', 'name'=>'MENU GÓRNE' ]);
        $this->insertAction($model, ['id'=>'2', 'name'=>'BRAMKA SMS' ]);
        $this->insertAction($model, ['id'=>'3', 'name'=>'MODUŁ USTAWIENIA' ]);
        $this->insertAction($model, ['id'=>'4', 'name'=>'PANEL STARTOWY' ]);
        $this->insertAction($model, ['id'=>'5', 'name'=>'MODUŁ DLS POJAZDY' ]);
        $this->insertAction($model, ['id'=>'6', 'name'=>'MODUŁ DLS MAJĄTEK' ]);
        $this->insertAction($model, ['id'=>'7', 'name'=>'MODUŁ POLIS' ]);
        $this->insertAction($model, ['id'=>'8', 'name'=>'MODUŁ ZADAŃ' ]);
    }

    private function seed_gap_type() {
        $model = new GapType;

        $this->insertAction($model, ['id'=>'1', 'name'=>'casco', ]);
        $this->insertAction($model, ['id'=>'2', 'name'=>'fakturowy', ]);
        $this->insertAction($model, ['id'=>'3', 'name'=>'casco portfel', ]);
        $this->insertAction($model, ['id'=>'4', 'name'=>'fakturowy portfel', ]);
        $this->insertAction($model, ['id'=>'5', 'name'=>'indeksowy', ]);
        $this->insertAction($model, ['id'=>'6', 'name'=>'finansowy', ]);
    }

    private function seed_injury_compensation_type() {
        $model = new InjuryCompensationDecisionType;

        $this->insertAction($model, ['id'=>'1', 'name'=>'kwota bezsporna', 'short_name'=>'kw. bezsp.', ]);
        $this->insertAction($model, ['id'=>'2', 'name'=>'odszkodowanie', 'short_name'=>'odszk.', ]);
        $this->insertAction($model, ['id'=>'3', 'name'=>'odszkodowanie - dopłata', 'short_name'=>'odszk. dopł.', ]);
        $this->insertAction($model, ['id'=>'4', 'name'=>'badanie techniczne', 'short_name'=>'bad. techn.', ]);
        $this->insertAction($model, ['id'=>'5', 'name'=>'holowanie', 'short_name'=>'holowanie', ]);
        $this->insertAction($model, ['id'=>'6', 'name'=>'samochód zastępczy', 'short_name'=>'sam. zast.', ]);
        $this->insertAction($model, ['id'=>'7', 'name'=>'żądanie zwrotu do ZU', 'short_name'=>'żadanie zwr. do ZU', ]);
        $this->insertAction($model, ['id'=>'8', 'name'=>'parking', 'short_name'=>'parking', ]);
        $this->insertAction($model, ['id'=>'9', 'name'=>'odszkodowanie – GAP', 'short_name'=>'odszk. GAP', ]);
        $this->insertAction($model, ['id'=>'10', 'name'=>'handlowy ubytek wartości', 'short_name'=>'handl. ub. wart.']);

    }

    private function seed_permission(){
        $model = new Permission;

        $this->insertAction($model, ['id'=>'1', 'short_name'=>'menu_gorne#bramka_sms#wejscie', 'path'=>'Bramka SMS', 'name'=>'Wejście', 'module_id'=>'1', ]);
        $this->insertAction($model, ['id'=>'2', 'short_name'=>'ustawienia#wejscie', 'path'=>'Ustawienia', 'name'=>'Wejście', 'module_id'=>'1', ]);
        $this->insertAction($model, ['id'=>'3', 'short_name'=>'dls_pojazdy#wejscie', 'path'=>'DLS Pojazdy', 'name'=>'Wejście', 'module_id'=>'1', ]);
        $this->insertAction($model, ['id'=>'4', 'short_name'=>'dls_majatek#wejscie', 'path'=>'DLS Majątek', 'name'=>'Wejście', 'module_id'=>'1', ]);
        $this->insertAction($model, ['id'=>'5', 'short_name'=>'zarzadzanie_pojazdami#wejscie', 'path'=>'Zarządzanie pojazdami', 'name'=>'Wejście', 'module_id'=>'1', ]);
        $this->insertAction($model, ['id'=>'6', 'short_name'=>'bramka_sms#wejscie', 'path'=>'Bramka SMS', 'name'=>'Wejście', 'module_id'=>'2', ]);
        $this->insertAction($model, ['id'=>'7', 'short_name'=>'grupy#dodawanie_grupy#wejscie', 'path'=>'Grupy / Dodawanie grupy', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'8', 'short_name'=>'grupy#edycja_grupy#uprawnienia_dla_grupy', 'path'=>'Grupy / Edycja grupy', 'name'=>'Uprawnienia dla grupy', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'9', 'short_name'=>'grupy#edycja_grupy#uzytkownicy_dla_grupy', 'path'=>'Grupy / Edycja grupy', 'name'=>'Użytkownicy dla grupy', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'10', 'short_name'=>'grupy#edycja_grupy#wejscie', 'path'=>'Grupy / Edycja grupy', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'11', 'short_name'=>'grupy#lista_grup#usuwanie_grupy', 'path'=>'Grupy / Lista grup', 'name'=>'Usuwanie grupy', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'12', 'short_name'=>'grupy#lista_grup#wejscie', 'path'=>'Grupy / Lista grup', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'13', 'short_name'=>'slowniki#zarzadzanie#wejscie', 'path'=>'Słowniki / Zarządzanie', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'14', 'short_name'=>'uzytkownicy#dodawanie_uzytkownika#wejscie', 'path'=>'Użytkownicy / Dodawanie użytkownika', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'15', 'short_name'=>'uzytkownicy#edycja_uzytkownika#grupy', 'path'=>'Użytkownicy / Edycja użytkownika', 'name'=>'Grupy', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'16', 'short_name'=>'uzytkownicy#edycja_uzytkownika#wejscie', 'path'=>'Użytkownicy / Edycja użytkownika', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'17', 'short_name'=>'uzytkownicy#lista_uzytkownikow#ustawianie_podpisu', 'path'=>'Użytkownicy / Lista użytkowników', 'name'=>'Ustawianie podpisu', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'18', 'short_name'=>'uzytkownicy#lista_uzytkownikow#wejscie', 'path'=>'Użytkownicy / Lista użytkowników', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'19', 'short_name'=>'uzytkownicy#podglad_uzytkownika#blokowanie_konta', 'path'=>'Użytkownicy / Podgląd użytkownika', 'name'=>'Blokowanie konta', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'20', 'short_name'=>'uzytkownicy#podglad_uzytkownika#ustawianie_hasla', 'path'=>'Użytkownicy / Podgląd użytkownika', 'name'=>'Ustawianie hasła', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'21', 'short_name'=>'uzytkownicy#podglad_uzytkownika#wejscie', 'path'=>'Użytkownicy / Podgląd użytkownika', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'22', 'short_name'=>'lista_ubezpieczalni#wejscie', 'path'=>'Lista ubezpieczalni', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'23', 'short_name'=>'lista_marek_samochodow#wejscie', 'path'=>'Lista marek samochodów', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'24', 'short_name'=>'edycja_danych_rejestrowych_idea#wejscie', 'path'=>'Edycja danych rejestrowych Idea', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'25', 'short_name'=>'baza_oddzialow_idealeasing#wejscie', 'path'=>'Baza oddziałów IdeaLeasing', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'26', 'short_name'=>'edycja_procesow#wejscie', 'path'=>'Edycja procesów', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'27', 'short_name'=>'edycja_godzin_pracy#wejscie', 'path'=>'Edycja godzin pracy', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'28', 'short_name'=>'szablony_sms#wejscie', 'path'=>'Szablony SMS', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'29', 'short_name'=>'reklamy_aplikacji_mobilnej#wejscie', 'path'=>'Reklamy aplikacji mobilnej', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'30', 'short_name'=>'karty_likwidacji_szkod#wejscie', 'path'=>'Karty likwidacji szkód', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'31', 'short_name'=>'przypisywanie_raportow#wejscie', 'path'=>'Przypisywanie raportów', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'32', 'short_name'=>'dostepnosc_dokumentow#wejscie', 'path'=>'Dostępność dokumentów', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'33', 'short_name'=>'zarzadzanie_etapami#wejscie', 'path'=>'Zarządzanie etapami', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'34', 'short_name'=>'slownik_aneksow_ubezpieczen#wejscie', 'path'=>'Słownik aneksów ubezpieczeń', 'name'=>'Wejście', 'module_id'=>'3', ]);
        $this->insertAction($model, ['id'=>'35', 'short_name'=>'zablokowani_uzytkownicy#zablokowani_uzytkownicy', 'path'=>'Zablokowani użytkownicy', 'name'=>'Zablokowani użytkownicy', 'module_id'=>'4', ]);
        $this->insertAction($model, ['id'=>'36', 'short_name'=>'nieczynni_platnicy_vat#nieczynni_platnicy_vat', 'path'=>'Nieczynni płatnicy VAT', 'name'=>'Nieczynni płatnicy VAT', 'module_id'=>'4', ]);
        $this->insertAction($model, ['id'=>'37', 'short_name'=>'zlecenia_(szkody)#wejscie', 'path'=>'Zlecenia (szkody)', 'name'=>'Wejście', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'38', 'short_name'=>'zlecenia_(szkody)#wyszukaj_pojazd', 'path'=>'Zlecenia (szkody)', 'name'=>'Wyszukaj pojazd', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'39', 'short_name'=>'zlecenia_(szkody)#wgraj_szkody_nieprzetworzone', 'path'=>'Zlecenia (szkody)', 'name'=>'Wgraj szkody nieprzetworzone', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'40', 'short_name'=>'zlecenia_(szkody)#szkody_nieprzetworzone', 'path'=>'Zlecenia (szkody)', 'name'=>'Szkody Nieprzetworzone', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'41', 'short_name'=>'zlecenia_(szkody)#szkody_zarejestrowane', 'path'=>'Zlecenia (szkody)', 'name'=>'Szkody Zarejestrowane', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'42', 'short_name'=>'zlecenia_(szkody)#szkody_calkowite', 'path'=>'Zlecenia (szkody)', 'name'=>'Szkody całkowite', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'43', 'short_name'=>'zlecenia_(szkody)#szkody_anulowane', 'path'=>'Zlecenia (szkody)', 'name'=>'Szkody anulowane', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'44', 'short_name'=>'zlecenia_(szkody)#zarzadzaj', 'path'=>'Zlecenia (szkody)', 'name'=>'Zarządzaj', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'45', 'short_name'=>'zlecenia_(szkody)#zarzadzaj#przepnij_szkode', 'path'=>'Zlecenia (szkody) / Zarządzaj', 'name'=>'Przepnij szkodę', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'46', 'short_name'=>'kartoteka_szkody#wejscie', 'path'=>'Kartoteka szkody', 'name'=>'Wejście', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'47', 'short_name'=>'kartoteka_szkody#komunikator', 'path'=>'Kartoteka szkody', 'name'=>'Komunikator', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'48', 'short_name'=>'kartoteka_szkody#dane_szkody', 'path'=>'Kartoteka szkody', 'name'=>'Dane szkody', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'49', 'short_name'=>'kartoteka_szkody#lokalizacja_zdarzenia', 'path'=>'Kartoteka szkody', 'name'=>'Lokalizacja zdarzenia', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'50', 'short_name'=>'kartoteka_szkody#uszkodzenia', 'path'=>'Kartoteka szkody', 'name'=>'Uszkodzenia', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'51', 'short_name'=>'kartoteka_szkody#dokumentacja', 'path'=>'Kartoteka szkody', 'name'=>'Dokumentacja', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'52', 'short_name'=>'kartoteka_szkody#rozliczenia_szkody', 'path'=>'Kartoteka szkody', 'name'=>'Rozliczenia szkody', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'53', 'short_name'=>'kartoteka_szkody#zdjecia', 'path'=>'Kartoteka szkody', 'name'=>'Zdjęcia', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'54', 'short_name'=>'kartoteka_szkody#generowanie_dokumentow', 'path'=>'Kartoteka szkody', 'name'=>'Generowanie dokumentów', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'55', 'short_name'=>'kartoteka_szkody#bramka_sms', 'path'=>'Kartoteka szkody', 'name'=>'Bramka SMS', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'56', 'short_name'=>'kartoteka_szkody#historia', 'path'=>'Kartoteka szkody', 'name'=>'Historia', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'57', 'short_name'=>'kartoteka_szkody#sprzedaz_wraku', 'path'=>'Kartoteka szkody', 'name'=>'Sprzedaż wraku', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'58', 'short_name'=>'kartoteka_szkody#bilans_sprzedazy', 'path'=>'Kartoteka szkody', 'name'=>'Bilans sprzedaży', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'59', 'short_name'=>'kartoteka_szkody#kradziez', 'path'=>'Kartoteka szkody', 'name'=>'Kradzież', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'60', 'short_name'=>'kartoteka_szkody#komunikator#przypisz_prowadzacego', 'path'=>'Kartoteka szkody / Komunikator', 'name'=>'Przypisz prowadzącego', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'61', 'short_name'=>'kartoteka_szkody#komunikator#usun_prowadzacego', 'path'=>'Kartoteka szkody / Komunikator', 'name'=>'Usuń prowadzącego', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'62', 'short_name'=>'kartoteka_szkody#komunikator#dodaj_wpis', 'path'=>'Kartoteka szkody / Komunikator', 'name'=>'Dodaj wpis', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'63', 'short_name'=>'kartoteka_szkody#komunikator#zarzadzaj_wpisem', 'path'=>'Kartoteka szkody / Komunikator', 'name'=>'Zarządzaj wpisem', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'64', 'short_name'=>'kartoteka_szkody#komunikator#usun_wpis', 'path'=>'Kartoteka szkody / Komunikator', 'name'=>'Usuń wpis', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'65', 'short_name'=>'kartoteka_szkody#komunikator#zarzadzanie_etapem', 'path'=>'Kartoteka szkody / Komunikator', 'name'=>'Zarządzanie etapem', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'66', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#dane_umowy_w_syjon', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Dane umowy w SYJON', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'67', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_klienta', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Edytuj dane klienta', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'68', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_szkody', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Edytuj dane szkody', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'69', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_polisy_ac', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Edytuj dane polisy AC', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'70', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_informacje_wewnetrzna', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Edytuj informację wewnętrzną', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'71', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#dane_pojazdu_w_syjon', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Dane pojazdu w SYJON', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'72', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_pojazdu', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Edytuj dane pojazdu', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'73', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_zglaszajacego', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Edytuj dane zgłaszającego', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'74', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_kierowcy', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Edytuj dane kierowcy', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'75', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#zmien_osobe_kontaktowa', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Zmień osobę kontaktową', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'76', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#dane_wlasciciela_w_syjon', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Dane właściciela w SYJON', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'77', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_wlasciciela', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Edytuj dane właściciela', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'78', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_sprawcy', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Edytuj dane sprawcy', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'79', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_opis_szkody', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Edytuj opis szkody', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'80', 'short_name'=>'kartoteka_szkody#uszkodzenia#edytuj_uszkodzenia', 'path'=>'Kartoteka szkody / Uszkodzenia', 'name'=>'Edytuj uszkodzenia', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'81', 'short_name'=>'kartoteka_szkody#uszkodzenia#edytuj_uwagi_do_uszkodzen', 'path'=>'Kartoteka szkody / Uszkodzenia', 'name'=>'Edytuj uwagi do uszkodzeń', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'82', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_dokument', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj dokument', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'83', 'short_name'=>'kartoteka_szkody#dokumentacja#wyslij_dokument', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Wyślij dokument', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'84', 'short_name'=>'kartoteka_szkody#dokumentacja#usun_dokument', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Usuń dokument', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'85', 'short_name'=>'kartoteka_szkody#rozliczenia_szkody#zarzadzaj', 'path'=>'Kartoteka szkody / Rozliczenia szkody', 'name'=>'Zarządzaj', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'86', 'short_name'=>'kartoteka_szkody#zdjecia#dodaj_zdjecia', 'path'=>'Kartoteka szkody / Zdjęcia', 'name'=>'Dodaj zdjęcia', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'87', 'short_name'=>'kartoteka_szkody#zdjecia#usun_zdjecie', 'path'=>'Kartoteka szkody / Zdjęcia', 'name'=>'Usuń zdjęcie', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'88', 'short_name'=>'serwisy#wejscie', 'path'=>'Serwisy', 'name'=>'Wejście', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'89', 'short_name'=>'serwisy#dodaj_firme', 'path'=>'Serwisy', 'name'=>'Dodaj firmę', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'90', 'short_name'=>'serwisy#stworz_grupe', 'path'=>'Serwisy', 'name'=>'Stwórz grupę', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'91', 'short_name'=>'serwisy#zarzadzaj', 'path'=>'Serwisy', 'name'=>'Zarządzaj', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'92', 'short_name'=>'serwisy#dodaj_firme#przypisanie_grupy', 'path'=>'Serwisy / Dodaj firmę', 'name'=>'Przypisanie grupy', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'93', 'short_name'=>'serwisy#warsztaty#dodaj_warsztat', 'path'=>'Serwisy / Warsztaty', 'name'=>'Dodaj warsztat', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'94', 'short_name'=>'serwisy#warsztaty#zarzadzaj', 'path'=>'Serwisy / Warsztaty', 'name'=>'Zarządzaj', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'95', 'short_name'=>'mapa_serwisow#wejscie', 'path'=>'Mapa serwisów', 'name'=>'Wejście', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'96', 'short_name'=>'mapa_serwisow#edycja', 'path'=>'Mapa serwisów', 'name'=>'Edycja', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'97', 'short_name'=>'prowizje#wejscie', 'path'=>'Prowizje', 'name'=>'Wejście', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'98', 'short_name'=>'pojazdy#raporty#wejscie', 'path'=>'Raporty', 'name'=>'Wejście', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'99', 'short_name'=>'baza_pism#wejscie', 'path'=>'Baza pism', 'name'=>'Wejście', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'100', 'short_name'=>'zarzadzanie_nabywcami#wejscie', 'path'=>'Zarządzanie nabywcami', 'name'=>'Wejście', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'101', 'short_name'=>'zlecenia#wejscie', 'path'=>'Zlecenia', 'name'=>'Wejście', 'module_id'=>'6', ]);
        $this->insertAction($model, ['id'=>'102', 'short_name'=>'zlecenia#szkody_nieprzetworzone', 'path'=>'Zlecenia', 'name'=>'Szkody Nieprzetworzone', 'module_id'=>'6', ]);
        $this->insertAction($model, ['id'=>'103', 'short_name'=>'zlecenia#szkody_zarejestrowane', 'path'=>'Zlecenia', 'name'=>'Szkody Zarejestrowane', 'module_id'=>'6', ]);
        $this->insertAction($model, ['id'=>'104', 'short_name'=>'zlecenia#szkody_calkowite#kradzieze', 'path'=>'Zlecenia', 'name'=>'Szkody całkowite / Kradzieże', 'module_id'=>'6', ]);
        $this->insertAction($model, ['id'=>'105', 'short_name'=>'zlecenia#szkody_anulowane', 'path'=>'Zlecenia', 'name'=>'Szkody anulowane', 'module_id'=>'6', ]);
        $this->insertAction($model, ['id'=>'106', 'short_name'=>'zlecenia#wprowadz_zlecenie', 'path'=>'Zlecenia', 'name'=>'Wprowadź zlecenie', 'module_id'=>'6', ]);
        $this->insertAction($model, ['id'=>'107', 'short_name'=>'zlecenia#zarzadzaj', 'path'=>'Zlecenia', 'name'=>'Zarządzaj', 'module_id'=>'6', ]);
        $this->insertAction($model, ['id'=>'108', 'short_name'=>'majatek#raporty#wejscie', 'path'=>'Raporty', 'name'=>'Wejście', 'module_id'=>'6', ]);
        $this->insertAction($model, ['id'=>'109', 'short_name'=>'zarzadzanie_polisami#wejscie', 'path'=>'Zarządzanie polisami', 'name'=>'Wejście', 'module_id'=>'1', ]);
        $this->insertAction($model, ['id'=>'110', 'short_name'=>'kartoteka_szkody#komunikator#wez_sprawe', 'path'=>'Kartoteka szkody / Komunikator', 'name'=>'Weź sprawę', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'111', 'short_name'=>'kartoteka_szkody#zarejestruj_w_sap', 'path'=>'Kartoteka szkody', 'name'=>'Zarejestruj w SAP', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'112', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#zmien_etap_procesowania', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Zmień etap procesowania', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'113', 'short_name'=>'kartoteka_szkody#sprzedaz_wraku#zarzadzaj', 'path'=>'Kartoteka szkody / Sprzedaż wraku', 'name'=>'Zarządzaj', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'114', 'short_name'=>'kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_warsztat', 'path'=>'Kartoteka szkody / Dane szkody i pojazdu', 'name'=>'Edytuj serwis', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'115', 'short_name'=>'zlecenia_(szkody)#wyszukaj_szkode', 'path'=>'Zlecenia (szkody)', 'name'=>'Wyszukaj szkodę', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'116', 'short_name'=>'wykaz_polis#wejscie', 'path'=>'Wykaz polis', 'name'=>'Wejście', 'module_id'=>'7', ]);
        $this->insertAction($model, ['id'=>'117', 'short_name'=>'wykaz_polis#zarzadzaj', 'path'=>'Wykaz polis', 'name'=>'Zarządzaj', 'module_id'=>'7', ]);
        $this->insertAction($model, ['id'=>'118', 'short_name'=>'wykaz_polis#wprowadzenie_umowy', 'path'=>'Wykaz polis', 'name'=>'Wprowadzenie umowy', 'module_id'=>'7', ]);
        $this->insertAction($model, ['id'=>'119', 'short_name'=>'kartoteka_polisy#wejscie', 'path'=>'Kartoteka polisy', 'name'=>'Wejście', 'module_id'=>'7', ]);
        $this->insertAction($model, ['id'=>'120', 'short_name'=>'kartoteka_polisy#zarzadzaj', 'path'=>'Kartoteka polisy', 'name'=>'Zarządzaj', 'module_id'=>'7', ]);
        $this->insertAction($model, ['id'=>'121', 'short_name'=>'kartoteka_polisy#certyfikat', 'path'=>'Kartoteka polisy', 'name'=>'Certyfikat', 'module_id'=>'7', ]);
        $this->insertAction($model, ['id'=>'122', 'short_name'=>'wykaz_stawek#wejscie', 'path'=>'Wykaz stawek', 'name'=>'Wejście', 'module_id'=>'7', ]);
        $this->insertAction($model, ['id'=>'123', 'short_name'=>'wykaz_stawek#zarzadzaj', 'path'=>'Wykaz stawek', 'name'=>'Zarządzaj', 'module_id'=>'7', ]);
        $this->insertAction($model, ['id'=>'124', 'short_name'=>'wykaz_franszyz#wejscie', 'path'=>'Wykaz franszyz', 'name'=>'Wejście', 'module_id'=>'7', ]);
        $this->insertAction($model, ['id'=>'125', 'short_name'=>'wykaz_franszyz#zarzadzaj', 'path'=>'Wykaz franszyz', 'name'=>'Zarządzaj', 'module_id'=>'7', ]);
        $this->insertAction($model, ['id'=>'126', 'short_name'=>'raporty#wejscie', 'path'=>'Raporty', 'name'=>'Wejście', 'module_id'=>'7', ]);
        $this->insertAction($model, ['id'=>'127', 'short_name'=>'kartoteka_szkody#kradziez#edycja', 'path'=>'Kartoteka szkody', 'name'=>'Edycja kradzież', 'module_id'=>'5', ]);

        $this->insertAction($model, ['id'=>'128', 'short_name'=>'wykaz_zadan#wejscie', 'path'=>'Wykaz zadań', 'name'=>'Wejście', 'module_id'=>'8', ]);
        $this->insertAction($model, ['id'=>'129', 'short_name'=>'raporty#wejscie', 'path'=>'Raporty', 'name'=>'Wejście', 'module_id'=>'8', ]);
        $this->insertAction($model, ['id'=>'130', 'short_name'=>'skrzynki_pocztowe#wejscie', 'path'=>'Skrzynki pocztowe', 'name'=>'Wejście', 'module_id'=>'8', ]);
        $this->insertAction($model, ['id'=>'131', 'short_name'=>'grupy_zadan#wejscie', 'path'=>'Grupy zadań', 'name'=>'Wejście', 'module_id'=>'8', ]);
        $this->insertAction($model, ['id'=>'132', 'short_name'=>'przypisania_indywidualne#wejscie', 'path'=>'Przypisania indywidualne', 'name'=>'Wejście', 'module_id'=>'8', ]);
        $this->insertAction($model, ['id'=>'133', 'short_name'=>'programy#wejscie', 'path'=>'Programy', 'name'=>'Wejście', 'module_id'=>'5', ]);
        $this->insertAction($model, ['id'=>'134', 'short_name'=>'programy#zarzadzaj', 'path'=>'Programy', 'name'=>'Zarządzaj', 'module_id'=>'5', ]);

        $this->insertAction($model, ['id'=>'135', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_1', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - wniosek o upoważnienie', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'136', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_2', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - kosztorys', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'137', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_3', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - faktura VAT', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'138', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_4', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - faktura VAT korekta', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'139', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_5', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - pismo ZU', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'140', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_6', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - decyzja ZU', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'141', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_7', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa ZU', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'142', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_8', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - notatka policyjna', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'143', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_9', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - oświadczenie sprawcy', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'144', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_11', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - prawo jazdy', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'145', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_12', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - dowód rej.', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'146', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_13', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - karta pojazdu', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'147', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_14', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - zaświadczenie o wyrejestrowaniu pojazdu', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'148', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_15', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - cesja praw na ZU', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'149', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_16', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - inne', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'150', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_17', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - druk zgłoszenia szkody', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'151', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_18', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - kwalifikacja szkody', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'152', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_19', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - rozrachunek', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'153', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_20', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - OBIEGÓWKA', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'154', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_21', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - WYCENA', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'155', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_22', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - Polisa', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'156', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_23', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - rozrachunek - zaległ. do 30 dni', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'157', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_24', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - podpisane zlecenie', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'158', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_25', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa - brak dok.', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'159', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_26', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa - tryb odwoławczy', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'160', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_27', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa zasadna', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'161', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_28', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa zasadna - zgodna z OWU', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'162', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_29', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa zasadna - brak dokumentów', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'163', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_30', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa zasadna - poniżej franszyzy', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'164', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_31', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa zasadna - brak potwierdzenia przez sprawcę', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'165', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_32', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa zasadna - rażące niedbalstwo', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'166', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_33', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa zasadna - brak zabezpieczeń', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'167', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_34', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa zasadna - przywłaszczenie', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'168', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_35', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa zasadna - GAP', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'169', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_36', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - odmowa zasadna - inne', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'170', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_37', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - decyzja ZU - koszty dodatkowe', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'171', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_38', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - faktura sprzedaży wraka', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'172', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_39', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - postanowienie o umorzeniu dochodzenia', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'173', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_40', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - protokół odbioru', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'174', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_41', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - decyzja IL ', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'175', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_42', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - deklaracja LB', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'176', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_43', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - korzysta z prawa pierwokupu', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'177', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_44', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - rezygnuje z prawa pierwokupu', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'178', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_45', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - zgłoszenie z EAP ', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'179', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_46', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - zgoda na odstępstwo od opłaty za UP', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'180', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_47', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - Zgłoszenie asysty', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'181', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_48', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - decyzja wypłaty - Handlowy Ubytek Wartości', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'182', 'short_name'=>'kartoteka_szkody#dokumentacja#dodaj_usun_dokument_49', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Dodaj/usuń dokument - decyzja odmowy - Handlowy Ubytek Wartości', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'183', 'short_name'=>'kartoteka_szkody#dokumentacja#usun_dokument_generowany', 'path'=>'Kartoteka szkody / Dokumentacja', 'name'=>'Usuń dokument generowany', 'module_id'=>'5']);
        $this->insertAction($model, ['id'=>'184', 'short_name'=>'menu_gorne#zadania#wejscie', 'path'=>'Zadania', 'name'=>'Wejście', 'module_id'=>'1']);

        $this->insertAction($model, ['id'=>'185', 'short_name'=>'zadania#nieobecnosci_pracownikow#wejscie', 'path'=>'Nieobecności pracowników', 'name'=>'Wejście', 'module_id'=>'8', ]);
        $this->insertAction($model, ['id'=>'186', 'short_name'=>'zadania#czarna_lista#wejscie', 'path'=>'Czarna lista', 'name'=>'Wejście', 'module_id'=>'8', ]);

        $this->insertAction($model, ['id'=>'187', 'short_name'=>'wykaz_zadan#osoba_przypisana', 'path'=>'Wykaz zadań', 'name'=>'Dostęp do osób przypisanych', 'module_id'=>'8', ]);

        $this->insertAction($model, ['id'=>'188', 'short_name'=>'zadania#slownik_typow_spraw#wejscie', 'path'=>'Słownik typów spraw', 'name'=>'Wejście', 'module_id'=>'8', ]);
        $this->insertAction($model, ['id'=>'189', 'short_name'=>'zadania#ksiazka_adresowa#wejscie', 'path'=>'Książka adresowa', 'name'=>'Wejście', 'module_id'=>'8', ]);
    }

    public function seed_injury_uploaded_document_types(){
        $model = new InjuryUploadedDocumentType;

        $this->insertAction($model, ['id'=>'1', 'parent_id'=>null, 'name'=>'wniosek o upoważnienie', 'ordering'=>'1', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'2', 'parent_id'=>null, 'name'=>'kosztorys', 'ordering'=>'5', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'3', 'parent_id'=>null, 'name'=>'faktura VAT', 'ordering'=>'10', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'4', 'parent_id'=>null, 'name'=>'faktura VAT korekta', 'ordering'=>'15', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'5', 'parent_id'=>null, 'name'=>'pismo ZU', 'ordering'=>'20', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'6', 'parent_id'=>null, 'name'=>'decyzja ZU', 'ordering'=>'25', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'7', 'parent_id'=>null, 'name'=>'odmowa ZU', 'ordering'=>'30', 'hidden'=>'1', ]);
        $this->insertAction($model, ['id'=>'8', 'parent_id'=>null, 'name'=>'notatka policyjna', 'ordering'=>'35', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'9', 'parent_id'=>null, 'name'=>'oświadczenie sprawcy', 'ordering'=>'40', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'11', 'parent_id'=>null, 'name'=>'prawo jazdy', 'ordering'=>'45', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'12', 'parent_id'=>null, 'name'=>'dowód rej.', 'ordering'=>'50', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'13', 'parent_id'=>null, 'name'=>'karta pojazdu', 'ordering'=>'55', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'14', 'parent_id'=>null, 'name'=>'zaświadczenie o wyrejestrowaniu pojazdu', 'ordering'=>'60', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'15', 'parent_id'=>null, 'name'=>'cesja praw na ZU', 'ordering'=>'65', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'16', 'parent_id'=>null, 'name'=>'inne', 'ordering'=>'1000', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'17', 'parent_id'=>null, 'name'=>'druk zgłoszenia szkody', 'ordering'=>'70', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'18', 'parent_id'=>null, 'name'=>'kwalifikacja szkody', 'ordering'=>'75', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'19', 'parent_id'=>null, 'name'=>'rozrachunek', 'ordering'=>'80', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'20', 'parent_id'=>null, 'name'=>'OBIEGÓWKA', 'ordering'=>'85', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'21', 'parent_id'=>null, 'name'=>'WYCENA', 'ordering'=>'90', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'22', 'parent_id'=>null, 'name'=>'Polisa', 'ordering'=>'100', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'23', 'parent_id'=>null, 'name'=>'rozrachunek - zaległ. do 30 dni', 'ordering'=>'95', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'24', 'parent_id'=>null, 'name'=>'podpisane zlecenie', 'ordering'=>'105', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'25', 'parent_id'=>null, 'name'=>'odmowa - brak dok.', 'ordering'=>'110', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'26', 'parent_id'=>null, 'name'=>'odmowa - tryb odwoławczy', 'ordering'=>'115', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'27', 'parent_id'=>null, 'name'=>'odmowa zasadna', 'ordering'=>'120', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'28', 'parent_id'=>'27', 'name'=>'odmowa zasadna - zgodna z OWU', 'ordering'=>'125', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'29', 'parent_id'=>'27', 'name'=>'odmowa zasadna - brak dokumentów', 'ordering'=>'130', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'30', 'parent_id'=>'27', 'name'=>'odmowa zasadna - poniżej franszyzy', 'ordering'=>'135', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'31', 'parent_id'=>'27', 'name'=>'odmowa zasadna - brak potwierdzenia przez sprawcę', 'ordering'=>'140', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'32', 'parent_id'=>'27', 'name'=>'odmowa zasadna - rażące niedbalstwo', 'ordering'=>'145', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'33', 'parent_id'=>'27', 'name'=>'odmowa zasadna - brak zabezpieczeń', 'ordering'=>'150', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'34', 'parent_id'=>'27', 'name'=>'odmowa zasadna - przywłaszczenie', 'ordering'=>'155', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'35', 'parent_id'=>'27', 'name'=>'odmowa zasadna - GAP', 'ordering'=>'160', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'36', 'parent_id'=>'27', 'name'=>'odmowa zasadna - inne', 'ordering'=>'165', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'37', 'parent_id'=>null, 'name'=>'decyzja ZU - koszty dodatkowe', 'ordering'=>'26', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'38', 'parent_id'=>null, 'name'=>'faktura sprzedaży wraka', 'ordering'=>'17', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'39', 'parent_id'=>null, 'name'=>'postanowienie o umorzeniu dochodzenia', 'ordering'=>'61', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'40', 'parent_id'=>null, 'name'=>'protokół odbioru', 'ordering'=>'106', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'41', 'parent_id'=>null, 'name'=>'decyzja IL ', 'ordering'=>'107', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'42', 'parent_id'=>null, 'name'=>'deklaracja LB', 'ordering'=>'170', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'43', 'parent_id'=>'42', 'name'=>'korzysta z prawa pierwokupu', 'ordering'=>'171', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'44', 'parent_id'=>'42', 'name'=>'rezygnuje z prawa pierwokupu', 'ordering'=>'172', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'45', 'parent_id'=>null, 'name'=>'zgłoszenie z EAP ', 'ordering'=>'200', 'hidden'=>'1', ]);
        $this->insertAction($model, ['id'=>'46', 'parent_id'=>null, 'name'=>'zgoda na odstępstwo od opłaty za UP', 'ordering'=>'201', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'47', 'parent_id'=>null, 'name'=>'Zgłoszenie asysty', 'ordering'=>'202', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'48', 'parent_id'=>null, 'name'=>'decyzja wypłaty - Handlowy Ubytek Wartości', 'ordering'=>'220', 'hidden'=>null, ]);
        $this->insertAction($model, ['id'=>'49', 'parent_id'=>null, 'name'=>'decyzja odmowy - Handlowy Ubytek Wartości', 'ordering'=>'230', 'hidden'=>null, ]);
    }

    private function seed_leasing_agreement_insurance_types() {
        $model = new LeasingAgreementInsuranceType();

        $this->insertAction($model, ['id'=>'1', 'name'=>'wieloletnia', 'months'=>null, ]);
        $this->insertAction($model, ['id'=>'2', 'name'=>'roczna', 'months'=>'12', ]);
        $this->insertAction($model, ['id'=>'7', 'name'=>'Umowa 2 miesięczna', 'months'=>'2', ]);
        $this->insertAction($model, ['id'=>'8', 'name'=>'Umowa miesięczna', 'months'=>'1', ]);
        $this->insertAction($model, ['id'=>'9', 'name'=>'6 msc', 'months'=>'6', ]);
        $this->insertAction($model, ['id'=>'10', 'name'=>'7 msc', 'months'=>'7', ]);
        $this->insertAction($model, ['id'=>'11', 'name'=>'8 msc', 'months'=>'8', ]);
        $this->insertAction($model, ['id'=>'12', 'name'=>'9 msc', 'months'=>'9', ]);
        $this->insertAction($model, ['id'=>'13', 'name'=>'10 msc', 'months'=>'10', ]);
        $this->insertAction($model, ['id'=>'14', 'name'=>'wielomiesięczna', 'months'=>null, ]);
    }
}