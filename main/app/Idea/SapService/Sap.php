<?php


namespace Idea\SapService;


use Carbon\Carbon;
use Config;
use GuzzleHttp\Client;
use InjuryNote;
use RuntimeException;
use Exception;

class Sap
{
    private $url;
    private $api_token;
    private $client;
    /**
     * @var array
     */
    private $zwarsz;
    /**
     * @var array
     */
    private $zalikwid_edb;

    /**
     * Sap constructor.
     */
    public function __construct()
    {
        $this->url = Config::get('webconfig.SAP_URL');
        $this->api_token = Config::get('webconfig.SAP_API_TOKEN');
        $this->client = new Client();
    }

    public function version()
    {
        try {
            $response = $this->client->get($this->url . '/version');
            dd($response->getBody()->getContents(), $response->getStatusCode(), json_decode($response->getBody(), true));

            return json_decode($response->getBody(), true);
        } catch (RuntimeException $ex) {
            return json_encode(['error' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    public function umowaSzkody($rokum, $nrum)
    {
        try {
            $url = $this->url . '/rfc/zrfcUmowaSzkody';
            $json_struct = [
                'fsSzukajPo' => [
                    'rokum' => $rokum,
                    'nrum' => $nrum
                ]
            ];

            $response = $this->client->put($url, [
                'headers' => [
                    'Authorization' => $this->api_token,
                ],
                'json' => $json_struct
            ]);
            return  json_decode($response->getBody(), true);
        } catch (RuntimeException $ex) {
            \Log::error('sap', [$json_struct, $ex]);

            return ['status' => 400, 'msg' => $ex->getMessage().' - '.$ex->getCode()];
        }
    }

    private function prepareWarsztat($branch)
    {
        $company = $branch->company()->first();
        $zwarsz = [];

        $nip = $branch->nip ? $branch->nip : $company->nip;
        $nip = preg_replace('~\D~', '', $nip);

        $zwarsz['nip'] = $nip;
        $zwarsz['nazwa1'] = $branch->short_name;
//        $zwarsz['nazwa2'] = $branch->short_name;
//        $zwarsz['nazwisko'] = ;
        $zwarsz['kod'] = $branch->code;
        $zwarsz['msc'] = $branch->city;
        $zwarsz['adrs'] = $branch->street;
        $zwarsz['regon'] = $company->regon;
        $zwarsz['telefon'] = $branch->phone;
//        $zwarsz['fax'] = ;
//        $zwarsz['bank'] = ;
        $zwarsz['konto'] = $company->account_nr;

        if ($branch->trashed()) {
            $zwarsz['usuniety'] = 1;
        } else {
            $zwarsz['aktywny'] = 1;
        }
        if ($company->groups->count() > 0) {
            $zwarsz['wspolprac'] = 1;
        }

        if ($company->groups()->whereIn('name', ['EDB', 'Idea Fleet S.A.'])->count() > 0) {
            $zwarsz['asystaSzkod'] = 'X';
        }

        $this->zwarsz = $zwarsz;
        return $zwarsz;
    }

    public function warszatUtworz($branch)
    {
        $zwarsz = $this->prepareWarsztat($branch);
        try {
            $url = $this->url . '/rfc/zrfcWarsztatUtworz';
            $response = $this->client->put($url, [
                'headers' => [
                    'Authorization' => $this->api_token,
                ],

                'json' => [
                    'fsWarszIn' => $zwarsz
                ]
            ]);

            \File::append(app_path() . '/storage/logs/sap-warsztat-utworz-'.date('Y-m').'.txt', PHP_EOL . '---' . date('Y-m-d H:i') . '---' . PHP_EOL);
            \File::append(app_path() . '/storage/logs/sap-warsztat-utworz-'.date('Y-m').'.txt', json_encode($zwarsz));
            $content = $response->getBody()->getContents();
            \File::append(app_path() . '/storage/logs/sap-warsztat-utworz-'.date('Y-m').'.txt', $content);
            $content = preg_replace('~[\r\n]+~', '', $content);
            \File::append(app_path() . '/storage/logs/sap-warsztat-utworz-'.date('Y-m').'.txt', $content);
            \File::append(app_path() . '/storage/logs/sap-warsztat-utworz-'.date('Y-m').'.txt', PHP_EOL . '------------' . PHP_EOL);

            $response_encoded = json_decode($content, true);
            \Log::info('warsztat', [$response_encoded]);

            if (isset($response_encoded['ftReturn'])) {
                foreach ($response_encoded['ftReturn'] as $message) {
                    if ($message['typ'] == 'E') {
                        $ftReturn = isset($response_encoded['ftReturn']) ? $response_encoded['ftReturn'] : null;

                        return ['idWarsz' => null, 'ftReturn' => $ftReturn];
                    }
                }
            }

            $branch->update(['sap_id' => $response_encoded['fsWarszOut']['idWarsz']]);

            $idWarsz = $response_encoded['fsWarszOut']['idWarsz'];
            $ftReturn = isset($response_encoded['ftReturn']) ? $response_encoded['ftReturn'] : null;

            return ['idWarsz' => $idWarsz, 'ftReturn' => $ftReturn];
        } catch (RuntimeException $ex) {
            \Log::error('sap', [$zwarsz, $ex]);
            return false;
        }
    }

    private function parseContractNumber($contract_number)
    {
        if ($contract_number && $contract_number != '') {
            $partials = explode('/', $contract_number);

            $contract_number = array_shift($partials);
        }

        return $contract_number;
    }

    private function parseRokNum($contract_number)
    {
        if ($contract_number && $contract_number != '') {
            $partials = explode('/', $contract_number);

            if(count($partials ) > 0) return $partials[1];
        }

        return null;
    }

    public function likwidatorUtworz($insurance_company)
    {
        if ($insurance_company->sap_id) return $insurance_company->sap_id;

        $zalikwid_edb = [];

        $zalikwid_edb['nazwa1'] = $insurance_company->name;
        $zalikwid_edb['kod'] = $insurance_company->code;
        $zalikwid_edb['msc'] = $insurance_company->city;
        $zalikwid_edb['adres'] = $insurance_company->street;

        $this->zalikwid_edb = $zalikwid_edb;
        try {
            $url = $this->url . '/rfc/zrfcLikwidatorUtworz';
            $response = $this->client->put($url, [
                'headers' => [
                    'Authorization' => $this->api_token,
                ],

                'json' => [
                    'fsLikwidIn' => $zalikwid_edb
                ]
            ]);

            $content = $response->getBody()->getContents();
            \File::append(app_path() . '/storage/logs/sap-likwidator-utworz-'.date('Y-m').'.txt', $content);
            $content = preg_replace('~[\r\n]+~', '', $content);

            if (substr($content, -3) == '},}') {
                $content = substr($content, 0, -3) . '}}';
            }

            $response_encoded = json_decode($content, true);
            \Log::info('likwidator', [$response_encoded]);

            if (isset($response_encoded['ftReturn'])) {
                return null;
            }
            $symbol = $response_encoded['fsLikwidOut']['symbol'];
            $insurance_company->update(['sap_id' => $symbol]);
            return $symbol;
        } catch (RuntimeException $ex) {
            dd($ex->getMessage(), $ex->getCode(), $ex->getTraceAsString());
        }
    }

    private function parseTowUb($injury)
    {
        if ($injury->vehicle->insurance_company_id != 0) {
            if (mb_strpos(mb_strtoupper($injury->vehicle->insurance_company()->first()->name), 'PZU') !== false) {
                return '006';
            }
        }

        return '003';
    }

    public function szkodaNotUtworz($injury, $notes)
    {
        if (!is_array($notes) || count($notes) == 0) return [];

        $ftNotatkaN = [];
        foreach ($notes as $k => $note) {
            $ftNotatkaN['item' . $k] = [
                'szkoda_id' => $injury->sap->szkodaId,
                'roknotatki' => date('Y'),
                'nrnotatki' => rand(0, 1000000000),
                'obiekt' => 'S',
                'temat' => $note,
                'data' => date('Y-m-d'),
                'uzeit' => date('H:i:s')
            ];
        }

        $json_struct = [];
        $json_struct['ftNotatkaN'] = $ftNotatkaN;

        try {
            $url = $this->url . '/rfc/zrfcSzkodaNotUtworz';
            $response = $this->client->put($url, [
                'headers' => [
                    'Authorization' => $this->api_token
                ],

                'json' => $json_struct
            ]);

            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', PHP_EOL . '---' . date('Y-m-d H:i') . '---' . PHP_EOL);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', PHP_EOL . json_encode($json_struct) . PHP_EOL);
            $contents = $response->getBody()->getContents();
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', $contents);
            $contents = preg_replace('~[\r\n]+~', '', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', PHP_EOL . '------' . PHP_EOL);

            $response_decoded = preg_replace('/\s+/', ' ', $contents);
            $response_decoded = preg_replace('/(,\ )+/', ', ', $response_decoded);
            $response_decoded = json_decode($response_decoded, true);

            \Log::info('szkodaNotUtworz', [$json_struct, $response_decoded]);

            return $response_decoded;
        } catch (RuntimeException $ex) {
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', PHP_EOL . '---ERROR---' . date('Y-m-d H:i') . '---' . PHP_EOL);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', PHP_EOL . json_encode($json_struct) . PHP_EOL);
            $contents = $ex->getMessage() . PHP_EOL . $ex->getCode();
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', PHP_EOL . '------' . PHP_EOL);

            echo '<pre>';
            dd($ex->getMessage(), $ex->getCode(), ['fsSzkodaIn' => $ftNotatkaN]);
        }
    }

    public function szkodaNotPobierz($injury)
    {
        $ftNotatkaP = [];
        $ftNotatkaP['szkoda_id'] = $injury->sap->szkodaId;

        $json_struct = [];
        $json_struct['ftNotatkaP'] = $ftNotatkaP;

        try {
            $url = $this->url . '/rfc/zrfcSzkodaNotPobierz';
            $response = $this->client->put($url, [
                'headers' => [
                    'Authorization' => $this->api_token
                ],

                'json' => $json_struct
            ]);

            \File::append(app_path() . '/storage/logs/sap-szkoda-not-pobierz-'.date('Y-m').'.txt', PHP_EOL . '---' . date('Y-m-d H:i') . '---' . PHP_EOL);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-pobierz-'.date('Y-m').'.txt', PHP_EOL . json_encode($json_struct) . PHP_EOL);
            $contents = $response->getBody()->getContents();
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-pobierz-'.date('Y-m').'.txt', $contents);
            $contents = preg_replace('~[\r\n]+~', '', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-pobierz-'.date('Y-m').'.txt', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-pobierz-'.date('Y-m').'.txt', PHP_EOL . '------' . PHP_EOL);

            $response_decoded = preg_replace('/\s+/', ' ', $contents);
            $response_decoded = preg_replace('/(,\ )+/', ', ', $response_decoded);
            $response_decoded = json_decode($response_decoded, true);

            \Log::info('szkodaNotPobierz', [$json_struct, $response_decoded]);

            return [$response_decoded, $json_struct, $contents];
        } catch (RuntimeException $ex) {
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', PHP_EOL . '---ERROR---' . date('Y-m-d H:i') . '---' . PHP_EOL);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', PHP_EOL . json_encode($json_struct) . PHP_EOL);
            $contents = $ex->getMessage() . PHP_EOL . $ex->getCode();
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-utworz-'.date('Y-m').'.txt', PHP_EOL . '------' . PHP_EOL);

            echo '<pre>';
            dd($ex->getMessage(), $ex->getCode(), ['ftNotatkaP' => $ftNotatkaP]);
        }
    }

    public function szkodaPobierz($sap_id)
    {
        $fsSzkodaIn = [];
        $fsSzkodaIn['szkoda_id'] = $sap_id;

        $json_struct = [];
        $json_struct['fsSzkodaIn'] = $fsSzkodaIn;

        $json_struct = $fsSzkodaIn;

        try {
            $url = $this->url . '/rfc/zrfcSzkodaPobierz';
            $response = $this->client->put($url, [
                'headers' => [
                    'Authorization' => $this->api_token
                ],

                'json' => $json_struct
            ]);

            \File::append(app_path() . '/storage/logs/sap-szkoda-pobierz-'.date('Y-m').'.txt', PHP_EOL . '---' . date('Y-m-d H:i') . '---' . PHP_EOL);
            \File::append(app_path() . '/storage/logs/sap-szkoda-pobierz-'.date('Y-m').'.txt', PHP_EOL . json_encode($json_struct) . PHP_EOL);
            $contents = $response->getBody()->getContents();
            \File::append(app_path() . '/storage/logs/sap-szkoda-pobierz-'.date('Y-m').'.txt', $contents);
            $contents = preg_replace('~[\r\n]+~', '', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-pobierz-'.date('Y-m').'.txt', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-pobierz-'.date('Y-m').'.txt', PHP_EOL . '------' . PHP_EOL);

            $response_decoded = preg_replace('/\s+/', ' ', $contents);
            $response_decoded = preg_replace('/(,\ )+/', ', ', $response_decoded);
            $response_decoded = json_decode($response_decoded, true);

            \Log::info('szkodaPobierz', [$json_struct, $response_decoded]);

            return $response_decoded;
        } catch (RuntimeException $ex) {
            \Log::error('sap', [$fsSzkodaIn, $ex, json_encode(['fsSzkodaIn' => [$fsSzkodaIn]])]);
            echo '<pre>';
            dd($ex->getMessage(), $ex->getCode(), ['fsSzkodaIn' => $fsSzkodaIn]);
        }
    }

    public function szkodaUtworzNew($injury)
    {
        $vehicle = $injury->vehicle()->first();

        $fsSzkodaIn = $this->getCreateStruct($injury, $vehicle);

        $existingSapInjury = $this->getExistingSapInjury($fsSzkodaIn, $injury);
        if($existingSapInjury){
            $injury = \Injury::find($injury->id);
            $this->szkoda($injury);

            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', PHP_EOL . '---' . date('Y-m-d H:i') . '---' . PHP_EOL);
            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', PHP_EOL . json_encode($existingSapInjury->toArray()) . PHP_EOL);
            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', PHP_EOL . '------' . PHP_EOL);

            $response = '<h1>Wskazana szkoda istnieje już w SAP</h1> sparowano ze szkodą: '.$existingSapInjury->szkodaId;

            return ['status' => 300, 'msg' => $response];
        }

        if ($injury->branch_id > 0) {
            $branch = $injury->branch()->first();
            $idWarsz = $branch->sap_id;
            if (!$idWarsz) {
                $return = $this->warszatUtworz($branch);
                $idWarsz = $return['idWarsz'];
                if ($idWarsz) $fsWarszIn = $this->prepareWarsztat($branch);
                else{
                    $ftReturn = $return['ftReturn'];
                    $msg = '';
                    if($ftReturn){
                        foreach ($ftReturn as $item) {
                            if($item['typ'] == 'E'){
                                $msg.='<br/>'.$item['message'];
                            }
                        }
                    }

                    return ['status' => 400, 'msg' => 'Wystąpił błąd przy tworzeniu warsztatu: '. $msg];
                }
            }
            if ($idWarsz) {
                $fsSzkodaIn['idWarsz'] = $idWarsz;
            }
        } else {
            //$fsSzkodaIn['idWarsz'] = '0000000000';
        }

        $json_struct = [];
        $json_struct['fsSzkodaIn'] = $fsSzkodaIn;
        if (isset($fsSzkodaIn['idWarsz']) && !$fsSzkodaIn['idWarsz']) {
            $json_struct['fsWarszIn'] = $fsWarszIn;
        }

//        $json_struct['ftDoplaty'] = $this->utworzDoplaty($injury);

        try {
            $url = $this->url . '/rfc/zrfcSzkoda';
            $response = $this->client->put($url, [
                'headers' => [
                    'Authorization' => $this->api_token
                ],

                'json' => $json_struct
            ]);

            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', PHP_EOL . '---' . date('Y-m-d H:i') . '---' . PHP_EOL);
            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', PHP_EOL . json_encode($json_struct) . PHP_EOL);
            $contents = $response->getBody()->getContents();
            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', $contents);
            $contents = preg_replace('~[\r\n]+~', '', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', PHP_EOL . '------' . PHP_EOL);

            $response_decoded = preg_replace('/\s+/', ' ', $contents);
            $response_decoded = preg_replace('/(,\ )+/', ', ', $response_decoded);
            $response_decoded = json_decode($response_decoded, true);

            \Log::info('szkoda', [$json_struct, $response_decoded]);

            if(isset($response_decoded['fsSzkodaOut']) && isset($response_decoded['fsSzkodaOut']['szkodaId']) && intval($response_decoded['fsSzkodaOut']['szkodaId']) > 0){
                $sapEntity = \InjurySapEntity::create(
                    $response_decoded['fsSzkodaOut']
                );
                $sapEntity->update(['injury_id' => $injury->id]);

                if($injury->compensations()->where('mode', '!=', 1)->whereNotNull('date_decision')->count() > 0){
                    $injury = \Injury::find($injury->id);
                    return $this->szkoda($injury);
                }

                if(isset($response_decoded['ftReturn'])){
                    $ftReturn = $response_decoded['ftReturn'];
                    $response = '<h4>Utworzenie w SAP przebiegło pomyślnie</h4>zwrócone komunikaty:';
                    $response .= '<ul class="list-group">';
                    foreach($ftReturn as $item)
                    {
                        \InjurySapResponse::create([
                            'injury_sap_entity_id' => $sapEntity->id,
                            'szkoda_id' =>  $response_decoded['fsSzkodaOut']['szkodaId'],
                            'typ' => $item['typ'],
                            'kod' => $item['kod'],
                            'message' => $item['message']
                        ]);
                        switch ($item['typ']){
                            case "W":
                                $class = 'list-group-item-warning';
                                break;
                            case "I":
                                $class = 'list-group-item-info';
                                break;
                            case "E":
                                $class = 'list-group-item-danger';
                                break;
                            default:
                                $class = '';
                                break;
                        }
                        $response .= '<li class="list-group-item '.$class.'">';
                        $response .= $item['message'].$item['messageV1'].$item['messageV2'];
                        $response .= '<span class="badge">'.$item['kod'].'</span>';
                        $response .= '</li>';
                    }
                    $response .= '</ul>';

                    return ['status' => 300, 'msg' => $response];
                }
                return ['status' => 200, 'sap_id' => $sapEntity->szkodaId];
            }elseif(isset($response_decoded['ftReturn'])){
                $ftReturn = $response_decoded['ftReturn'];
                $response = '';
                foreach($ftReturn as $item)
                {
                    if($item['typ'] == 'E' && $item['kod'] == '008'){
                        $messageV1 =  explode(':', $item['messageV1']);
                        $szkodaId = $messageV1[1];
                        \InjurySapEntity::create(
                            [
                                'injury_id' => $injury->id,
                                'szkodaId' => $szkodaId
                            ]
                        );
                        $injury = \Injury::find($injury->id);
                        return $this->szkoda($injury);
                    }else{
                        $response .= '<ul class="list-group">';
                        foreach($ftReturn as $item)
                        {
                            switch ($item['typ']){
                                case "W":
                                    $class = 'list-group-item-warning';
                                    break;
                                case "I":
                                    $class = 'list-group-item-info';
                                    break;
                                case "E":
                                    $class = 'list-group-item-danger';
                                    break;
                                default:
                                    $class = '';
                                    break;
                            }
                            $response .= '<li class="list-group-item '.$class.'">';
                            $response .= $item['message'].$item['messageV1'].$item['messageV2'];
                            $response .= '<span class="badge">'.$item['kod'].'</span>';
                            $response .= '</li>';
                        }
                        $response .= '</ul>';
                    }
                }

                return ['status' => 400, 'msg' => $response];
            }
            return ['status' => 400, 'msg' => $response_decoded];
        } catch (RuntimeException $ex) {
            \Log::error('sap', [$json_struct, $ex]);

            return ['status' => 400, 'msg' => $ex->getMessage().' - '.$ex->getCode()];
        }
    }

    public function szkoda($injury)
    {
        $json_struct = [];
        $vehicle = $injury->vehicle()->first();

        $sap_remote_data = $this->szkodaPobierz($injury->sap->szkodaId);
        if(isset($sap_remote_data['fsSzkodaOut']['dataszkody'])) unset($sap_remote_data['fsSzkodaOut']['dataszkody']);
        $injury->sap->update($sap_remote_data['fsSzkodaOut']);

        $fsSzkodaIn = [];
        $fsSzkodaIn['szkoda_id'] = $injury->sap->szkodaId;

        if ($vehicle->syjon_contract_id) {
            $syjonService = new \Idea\SyjonService\SyjonService();
            $contract = json_decode($syjonService->loadContract($vehicle->syjon_contract_id));
            $rokum = Carbon::createFromFormat('Y-m-d', $contract->data->contract_activation_date);
            $fsSzkodaIn['rokum'] = $rokum->year;
        }
        $fsSzkodaIn['nrum'] = $this->parseContractNumber($vehicle->nr_contract);
        if (!isset($fsSzkodaIn['rokum'])) $fsSzkodaIn['rokum'] = $this->parseRokNum($vehicle->nr_contract);
        $fsSzkodaIn['symbol'] = $injury->injury_nr;
        $fsSzkodaIn['nrrej'] = $vehicle->registration;
        $fsSzkodaIn['nrpolisy'] = $injury->injuryPolicy->nr_policy;

        $fsSzkodaIn['nrpolisyZew'] = $injury->sap->nrpolisyZew;

        $fsSzkodaIn['dataszkody'] = Carbon::createFromFormat('Y-m-d', $injury->date_event)->format('Y-m-d');
        $fsSzkodaIn['rodzub'] = $injury->injuries_type()->first()->sap_name;

        $fsSzkodaIn['rodzszk'] = $injury->sap_rodzszk;
        if (in_array($injury->sap_rodzszk, ['TOT', 'KRA'])) {
            $fsSzkodaIn['stanszk'] = floatval($injury->sap->stanszk);
        }else {
            $fsSzkodaIn['stanszk'] = floatval($injury->sap_stanszk);
        }

        $fsSzkodaIn['kwota'] = $injury->sap->kwota;

        $doplaty = $this->utworzDoplaty($injury);
        $injury = \Injury::find($injury->id);

        if (in_array($injury->sap_rodzszk, ['TOT', 'KRA'])) {
            $fsSzkodaIn['kwotaOdsz'] = floatval($injury->sap->kwotaOdsz);
            $fsSzkodaIn['kwotawypl'] = floatval($injury->sap->kwotawypl);
        }else {
            $compensation = $injury->compensations()->where('mode', 1)->first();
            if ($compensation && $compensation->created_at != $compensation->updated_at) {
                if ($compensation->injury_compensation_decision_type_id == 7) {
                    $compensation_value = abs($compensation->compensation) * -1;
                } else {
                    $compensation_value = $compensation->compensation;
                }
                $fsSzkodaIn['kwotaOdsz'] = floatval($compensation_value);
            } else {
                $fsSzkodaIn['kwotaOdsz'] = floatval($injury->sap->kwotaOdsz);
            }
//            $fsSzkodaIn['kwotawypl'] = $injury->sap->kwotawypl;
            $fsSzkodaIn['kwotawypl'] = floatval( $fsSzkodaIn['kwotaOdsz'] + (($injury->sapPremiums->count() > 0) ? $injury->sapPremiums->sum('kwDpl') : 0) );
        }
        $fsSzkodaIn['datawypl'] = $injury->sap->datawypl;


        $fsSzkodaIn['odmowa'] = $injury->sap->odmowa;
        $fsSzkodaIn['uwagi'] = $injury->sap->uwagi;

        $fsSzkodaIn['odbWarsz'] = $injury->sap->odbWarsz;
        $fsSzkodaIn['odbLb'] = $injury->sap->odbLb;
        $fsSzkodaIn['odbGl'] = $injury->sap->odbGl;
        $fsSzkodaIn['odbInny'] = $injury->sap->odbInny;

        $fsSzkodaIn['inne'] = $injury->sap->inne;
        $fsSzkodaIn['kosztH'] = floatval($injury->sap->kosztH);
        $fsSzkodaIn['kosztP'] = floatval($injury->sap->kosztP);
        $fsSzkodaIn['kosztI'] = floatval($injury->sap->kosztI);
        $fsSzkodaIn['kwPotrRat'] = floatval($injury->sap->kwPotrRat);
        $fsSzkodaIn['kwPotrInn'] = floatval($injury->sap->kwPotrInn);
        $fsSzkodaIn['kwPozost'] = floatval($injury->sap->kwPozost);
        $fsSzkodaIn['mPostoju'] = $injury->sap->mPostoju;
        $fsSzkodaIn['datWazSprzWra'] = $injury->sap->datWazSprzWra;

        $fsSzkodaIn['towlikw'] = $this->likwidatorUtworz($vehicle->insurance_company()->first());

        if ($injury->branch_id > 0) {
            $branch = $injury->branch()->first();
            $idWarsz = $branch->sap_id;
            if (!$idWarsz) {
                $return = $this->warszatUtworz($branch);
                $idWarsz = $return['idWarsz'];
                if ($idWarsz) $fsWarszIn = $this->prepareWarsztat($branch);
                else{
                    $ftReturn = $return['ftReturn'];
                    $msg = '';
                    if($ftReturn){
                        foreach ($ftReturn as $item) {
                            if($item['typ'] == 'E'){
                                $msg.='<br/>'.$item['message'];
                            }
                        }
                    }

                    return ['status' => 400, 'msg' => 'Wystąpił błąd przy tworzeniu warsztatu: '. $msg];
                }
            }
            if ($idWarsz) {
                $fsSzkodaIn['idWarsz'] = $idWarsz;
            }
        }else{
            $fsSzkodaIn['idWarsz'] = '';
        }

        $fsSzkodaIn['towub'] = $this->parseTowUb($injury);

        $json_struct['fsSzkodaIn'] = $fsSzkodaIn;
        if (isset($fsSzkodaIn['idWarsz']) && isset($fsWarszIn) && !$fsSzkodaIn['idWarsz']) {
            $json_struct['fsWarszIn'] = $fsWarszIn;
        }

        $json_struct['ftDoplaty'] = $doplaty;

        try {
            $url = $this->url . '/rfc/zrfcSzkoda';
            $response = $this->client->put($url, [
                'headers' => [
                    'Authorization' => $this->api_token
                ],

                'json' => $json_struct
            ]);

            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', PHP_EOL . '---' . date('Y-m-d H:i') . '---' . PHP_EOL);
            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', PHP_EOL . json_encode($json_struct) . PHP_EOL);
            $contents = $response->getBody()->getContents();
            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', $contents);
            $contents = preg_replace('~[\r\n]+~', '', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-'.date('Y-m').'.txt', PHP_EOL . '------' . PHP_EOL);

            $response_decoded = preg_replace('/\s+/', ' ', $contents);
            $response_decoded = preg_replace('/(,\ )+/', ', ', $response_decoded);
            $response_decoded = json_decode($response_decoded, true);

            \Log::info('szkoda', [$json_struct, $response_decoded]);

            if(isset($response_decoded['fsSzkodaOut']) && isset($response_decoded['fsSzkodaOut']['szkodaId']) && intval($response_decoded['fsSzkodaOut']['szkodaId']) > 0){
                $injury->sap->update($response_decoded['fsSzkodaOut']);

                if(isset($response_decoded['ftReturn'])){
                    $ftReturn = $response_decoded['ftReturn'];
                    $response = '<h4 class="text-center">Aktualizacja w SAP przebiegła pomyślnie</h4><p>zwrócone komunikaty:</p>';
                    $response .= '<ul class="list-group">';
                    foreach($ftReturn as $item)
                    {
                        switch ($item['typ']){
                            case "W":
                                $class = 'list-group-item-warning';
                                break;
                            case "I":
                                $class = 'list-group-item-info';
                                break;
                            case "E":
                                $class = 'list-group-item-danger';
                                break;
                            default:
                                $class = '';
                                break;
                        }
                        $response .= '<li class="list-group-item '.$class.'">';
                        $response .= $item['message'].$item['messageV1'].$item['messageV2'];
                        $response .= '<span class="badge">'.$item['kod'].'</span>';
                        $response .= '</li>';
                    }
                    $response .= '</ul>';

                    return ['status' => 300, 'msg' => $response];
                }
                return ['status' => 200, 'sap_id' => $injury->sap->szkodaId];
            }elseif(isset($response_decoded['ftReturn'])){
                $ftReturn = $response_decoded['ftReturn'];
                $response = '<h4 class="text-center">Aktualizacja w SAP nie udała się</h4><p>zwrócone komunikaty:</p>';
                $response .= '<ul class="list-group">';
                foreach($ftReturn as $item)
                {
                    switch ($item['typ']){
                        case "W":
                            $class = 'list-group-item-warning';
                            break;
                        case "I":
                            $class = 'list-group-item-info';
                            break;
                        case "E":
                            $class = 'list-group-item-danger';
                            break;
                        default:
                            $class = '';
                            break;
                    }
                    $response .= '<li class="list-group-item '.$class.'">';
                    $response .= $item['message'].$item['messageV1'].$item['messageV2'];
                    $response .= '<span class="badge">'.$item['kod'].'</span>';
                    $response .= '</li>';
                }
                $response .= '</ul>';

                return ['status' => 400, 'msg' => $response];
            }
            return ['status' => 400, 'msg' => $response_decoded];
        } catch (RuntimeException $ex) {
            \Log::error('sap', [$json_struct, $ex]);

            return ['status' => 400, 'msg' => $ex->getMessage().' - '.$ex->getCode()];
        }
    }

    public function utworzDoplaty($injury)
    {
        if($injury->sap) {
            $ftExistingDoplaty = $this->getExistingSapDoplaty($injury->sap);
            if ($ftExistingDoplaty && count($ftExistingDoplaty) > 0) {
                $premiums = [];
                foreach ($ftExistingDoplaty as $ftDoplata) {
                    $premium = $injury->sapPremiums()->where('nrRaty', 'like', $ftDoplata['nrRaty'])
                        ->withTrashed()
                        ->where(function($query){
                            $query->whereNull('deleted_at')->orWhereBetween('deleted_at',[
                                Carbon::now()->subMinute()->format('Y-m-d H:i:s'),
                                Carbon::now()->format('Y-m-d H:i:s')
                            ]);
                        })
                        ->first();
                    if ($premium) {
                        if (!$premium->injuryCompensation) $premium->update($ftDoplata);
                    } else {
                        $premium = $injury->sapPremiums()->create($ftDoplata);
                    }
                    $premiums[] = $premium->id;
                }
                $injury->sapPremiums()->whereNotIn('id', $premiums)->delete();
            }
        }

        if (in_array($injury->sap_rodzszk, ['TOT', 'KRA'])) {
            $ftDoplaty = [];

            foreach ($injury->sapPremiums as $k => $premium)
            {
                $ftDoplaty['item'.$k] = [
                    'szkodaId' => $injury->sap->szkodaId,
                    'nrRaty' => $premium->nrRaty,
                    'kwDpl' => floatval($premium->kwDpl),
                    'unameRej' => $premium->unameRej,
                    'dataRej' => $premium->dataRej,
                    'dataDpl' => $premium->dataDpl
                ];
            }

            return $ftDoplaty;
        }

        $ftDoplaty = [];
        $k = 0;
        foreach ($injury->sapPremiums()->orderBy('nrRaty')->get() as $i => $premium)
        {
            if($premium->injuryCompensation) {
                $kwDpl = 0;
                if (!is_null($premium->injuryCompensation->compensation)) {
                    if ($premium->injuryCompensation->injury_compensation_decision_type_id == 7) {
                        $premium->injuryCompensation->compensation = abs($premium->injuryCompensation->compensation) * -1;
                    }
                    $kwDpl = $premium->injuryCompensation->compensation;
                }
            }else{
                $kwDpl = $premium->kwDpl;
            }

            $premium->update(['nrRaty'=>str_pad( ($k+1), 2, '0', STR_PAD_LEFT), 'kwDpl' => $kwDpl]);

            $ftDoplaty['item'.$k] = [
                'szkodaId' => $injury->sap->szkodaId,
                'nrRaty' => str_pad( ($k+1), 2, '0', STR_PAD_LEFT),
                'kwDpl' => floatval($premium->kwDpl),
                'unameRej' => $premium->unameRej,
                'dataRej' => $premium->dataRej,
                'dataDpl' => $premium->dataDpl
            ];
            $k++;
        }

        foreach($injury->compensations()->where('mode', '!=', 1)->whereNotNull('date_decision')->has('premium', '<', 1)->get() as $i => $compensation)
        {
            $kwDpl = 0;
            if(!is_null($compensation->compensation)) {
                if ($compensation->injury_compensation_decision_type_id == 7) {
                    $compensation->compensation = abs($compensation->compensation) * -1;
                }
                $kwDpl = $compensation->compensation;
            }
            if( ($kwDpl > 0 || $kwDpl < 0) && $compensation->is_premiumable == 1) {
                $item = [
                    'szkodaId' => $injury->sap->szkodaId,
                    'nrRaty' => str_pad( ($k+1), 2, '0', STR_PAD_LEFT),
                    'kwDpl' => floatval($kwDpl),
                    'unameRej' => \Auth::user() ? \Auth::user()->name : 'DLS',
                    'dataRej' => Carbon::now()->format('Y-m-d'),
                    'dataDpl' => $compensation->date_decision
                ];
                if ($compensation->date_decision) $item['dataDpl'] = $compensation->date_decision;

                $ftDoplaty['item' . $k] = $item;
                $item['injury_compensation_id'] = $compensation->id;

                $injury->sapPremiums()->create($item);
                $k++;
            }
        }

        return $ftDoplaty;
    }

    public function szkodaNotKasuj($injury, $notes)
    {
        $json_struct = [];
        $json_struct['fSzkodaId'] = $injury->sap->szkodaId;

        $ftNotatkaKeys = [];

        foreach($notes as $k => $note)
        {
            $ftNotatkaKeys['item'.$k] = [
                'szkodaId' => $injury->sap->szkodaId,
                'roknotatki' => $note->roknotatki,
                'nrnotatki' => $note->nrnotatki,
            ];
        }

        $json_struct['ftNotatkaKeys'] = $ftNotatkaKeys;

        try {
            $url = $this->url . '/rfc/zrfcSzkodaNotKasuj';
            $response = $this->client->put($url, [
                'headers' => [
                    'Authorization' => $this->api_token
                ],

                'json' => $json_struct
            ]);

            \File::append(app_path() . '/storage/logs/sap-szkoda-not-kasuj-'.date('Y-m').'.txt', PHP_EOL . '---' . date('Y-m-d H:i') . '---' . PHP_EOL);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-kasuj-'.date('Y-m').'.txt', PHP_EOL . json_encode($json_struct) . PHP_EOL);
            $contents = $response->getBody()->getContents();
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-kasuj-'.date('Y-m').'.txt', $contents);
            $contents = preg_replace('~[\r\n]+~', '', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-kasuj-'.date('Y-m').'.txt', $contents);
            \File::append(app_path() . '/storage/logs/sap-szkoda-not-kasuj-'.date('Y-m').'.txt', PHP_EOL . '------' . PHP_EOL);

            $response_decoded = preg_replace('/\s+/', ' ', $contents);
            $response_decoded = preg_replace('/(,\ )+/', ', ', $response_decoded);
            $response_decoded = json_decode($response_decoded, true);

            \Log::info('szkoda not kasuj', [$json_struct, $response_decoded]);

            return $response_decoded;
        } catch (RuntimeException $ex) {
            \Log::error('sap', [$json_struct, $ex]);

            return ['status' => 400, 'msg' => $ex->getMessage().' - '.$ex->getCode()];
        }
    }

    private function getCreateStruct($injury, $vehicle)
    {
        $fsSzkodaIn = [];
        if ($vehicle->syjon_contract_id) {
            $syjonService = new \Idea\SyjonService\SyjonService();
            $contract = json_decode($syjonService->loadContract($vehicle->syjon_contract_id));
            $rokum = Carbon::createFromFormat('Y-m-d', $contract->data->contract_activation_date);
            $fsSzkodaIn['rokum'] = $rokum->year;
        }
        $fsSzkodaIn['nrum'] = $this->parseContractNumber($vehicle->nr_contract);
        if (!isset($fsSzkodaIn['rokum'])) $fsSzkodaIn['rokum'] = $this->parseRokNum($vehicle->nr_contract);
        $fsSzkodaIn['symbol'] = $injury->injury_nr;
        $fsSzkodaIn['nrrej'] = $vehicle->registration;
        $fsSzkodaIn['nrpolisy'] = $injury->injuryPolicy->nr_policy;

        $fsSzkodaIn['nrpolisyZew'] = '';
        $fsSzkodaIn['dataszkody'] = Carbon::createFromFormat('Y-m-d', $injury->date_event)->format('Y-m-d');
        $fsSzkodaIn['rodzub'] = $injury->injuries_type()->first()->sap_name;
        $fsSzkodaIn['rodzszk'] = $injury->sap_rodzszk;
        $fsSzkodaIn['stanszk'] = floatval($injury->sap_stanszk);

        $fsSzkodaIn['kwota'] = 0.00;

        $fsSzkodaIn['kwotaOdsz'] = 0.00;

        $fsSzkodaIn['kwotawypl'] = 0.00;
        // $fsSzkodaIn['datawypl'] = '2019-07-02';
        $fsSzkodaIn['odmowa'] = '';
        $fsSzkodaIn['uwagi'] = '';

        $fsSzkodaIn['odbWarsz'] = '';
        $fsSzkodaIn['odbLb'] = '';
        $fsSzkodaIn['odbGl'] = '';
        $fsSzkodaIn['odbInny'] = '';
//        switch ($injury->receive_id){
//            case 1:
//                $fsSzkodaIn['odbWarsz'] = 'X';
//                break;
//            case 2:
//                $fsSzkodaIn['odbGl'] = 'X';
//                break;
//            case 3:
//                $fsSzkodaIn['odbLb'] = 'X';
//                break;
//            default:
//                break;
//        }

        $fsSzkodaIn['inne'] = '';
        $fsSzkodaIn['kosztH'] = 0.00;
        $fsSzkodaIn['kosztP'] = 0.00;
        $fsSzkodaIn['kosztI'] = 0.00;
        $fsSzkodaIn['kwPotrRat'] = 0.00;
        $fsSzkodaIn['kwPotrInn'] = 0.00;
        $fsSzkodaIn['kwPozost'] = 0.00;
        $fsSzkodaIn['mPostoju'] = '';
        $fsSzkodaIn['datWazSprzWra'] = '1900-01-01';
        $fsSzkodaIn['towlikw'] = $this->likwidatorUtworz($vehicle->insurance_company()->first());



        $fsSzkodaIn['towub'] = $this->parseTowUb($injury);

        return $fsSzkodaIn;
    }

    private function getExistingSapInjury($fsSzkodaIn, $injury)
    {
        $umowaSzkody = $this->umowaSzkody($fsSzkodaIn['rokum'], $fsSzkodaIn['nrum']);

        if(!isset($umowaSzkody['ftSzkody'])) return false;

        $symbol = $fsSzkodaIn['symbol'];
        foreach($umowaSzkody['ftSzkody'] as $szkoda)
        {
            if($szkoda['symbol'] == $symbol)
            {
                $szkodaId = $szkoda['szkodaId'];

                $sapEntity = \InjurySapEntity::create($szkoda);
                $sapEntity->update(['injury_id' => $injury->id]);

                $injury->update([
                    'sap_stanszk' => $sapEntity->stanszk,
                    'sap_rodzszk' => $sapEntity->rodzszk
                ]);

                if(isset($umowaSzkody['ftNotatkaN'])){
                    foreach($umowaSzkody['ftNotatkaN'] as $notatka)
                    {
                        if($notatka['szkodaId'] == $szkodaId)
                        {
                            InjuryNote::create([
                                'injury_id' => $injury->id,
                                'roknotatki' => $notatka['roknotatki'],
                                'nrnotatki'=> $notatka['nrnotatki'],
                                'obiekt'=> $notatka['obiekt'],
                                'temat'=> $notatka['temat'],
                                'data'=> $notatka['data'],
                                'uzeit'=> $notatka['uzeit'],
                            ]);
                        }
                    }
                }

                if(isset($umowaSzkody['ftDoplaty'])) {
                    foreach ($umowaSzkody['ftDoplaty'] as $doplata) {
                        if($doplata['szkodaId'] == $szkodaId) {
                            $injury->sapPremiums()->create($doplata);
                        }
                    }
                }

                return $sapEntity;
            }
        }

        return false;
    }

    public function getExistingSapDoplaty($injurySap)
    {
        $umowaSzkody = $this->umowaSzkody($injurySap->rokum, $injurySap->nrum);

        if(!isset($umowaSzkody['ftSzkody'])) return false;

        $ftDoplaty = [];
        foreach($umowaSzkody['ftSzkody'] as $szkoda) {
            if ($szkoda['szkodaId'] == $injurySap->szkodaId) {
                if(isset($umowaSzkody['ftDoplaty'])) {
                    foreach ($umowaSzkody['ftDoplaty'] as $doplata) {
                        if($doplata['szkodaId'] == $injurySap->szkodaId) {
                            $ftDoplaty[] = $doplata;
                        }
                    }
                    return $ftDoplaty;
                }
            }
        }

        return false;
    }
}
