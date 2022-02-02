<?php
ini_set("soap.wsdl_cache_enabled", 0);
libxml_use_internal_errors(true);

/**
 * Obliczenie odbiorców wiadomości chata
 * @param array $receivers - odbiorcy wiadomości
 * @return liczba dziesiętna odpowiadającą liczbie binarnej odbiorców( DOS|WARSZTAT|INFOLINIA )
 */
function count_receivers($receivers)
{
	if(isset($receivers['check_dos']))
		$dos = 1;
	else
		$dos = 0;

	if(isset($receivers['check_info']))
		$info = 1;
	else
		$info = 0;

	if(isset($receivers['check_branch']))
		$branch = 1;
	else
		$branch = 0;

	$count = (string)$dos.(string)$branch.(string)$info;

	return bindec($count);
}

/**
 * Pobranie odbiorców do tablicy binarnej
 * @param string $receivers - liczba binarna reprezentująca odbiorców ( DOS|WARSZTAT|INFOLINIA )
 * @return tablica odbiorców wiadomości
 */
function get_receivers($receivers)
{
	$count = decbin($receivers);
	$count = str_pad($count, 3,"0", STR_PAD_LEFT);
	$count = str_split($count);

	return $count;
}

/**
 * Wysłanie wiadomości sms
 * @param string $phone - telefon odbiorcy
 * @param string msg - treść wiadomości
 * @return mixed
 */
function send_sms($phone, $msg){
    if( Config::get('webconfig.WEBCONFIG_SETTINGS_sms') == 0){
        return;
    }
    $kodSerwisu = 'idealeasing'; //login
	$kodDoradca = "Automat"; //podpis

    $phone = str_replace([' ','-',':'], '', $phone);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"http://webhost.home.pl/system/SMSsenderAPI/sendSMS_idea.php");
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS,
	            "receiver=".$phone."&kodSerwisu=".$kodSerwisu."&kodDoradca=".$kodDoradca."&smsText=".$msg);

	$result = curl_exec ($ch);
	curl_close ($ch);

    return $result;
}

/**
 * Zwraca do jakiej grupy należy zalogowany użytkownik (1 - pracownik Idea, 2 - pracownik Warsztatu, 3 - Infolinia )
 * @return int group
 */
//todo nowe uprawnienia
function get_chat_group()
{
    return '1';

    return '3';

    return '2';
}

/**
 * Zwraca do jakiej grupy należy zalogowany użytkownik w postaci słownej (nazwy kolumn w tabeli injury_chat_messages)
 * @return string group
 */
function get_chat_role()
{
	if( get_chat_group() == 1 )
		return 'dos_read';

	if( get_chat_group() == 3 )
		return 'info_read';

	if( get_chat_group() == 2 )
		return 'branch_read';
}

/**
 * Dodanie wpisu do wybranego logu
 * @param string $folder - nazwa folderu z logiem
 * @param string $file - nazwa pliku z logiem
 * @param string $content - treść wpisu
 */
function custom_log($folder, $file, $content)
{
	$dateNow = explode('-',date("Y-m-d-H-i-s"));
	$dateNow = array(
		'year' => $dateNow[0],
		'month' => $dateNow[1],
		'day' => $dateNow[2],
		'hour' => $dateNow[3],
		'minute' => $dateNow[4],
		'second' => $dateNow[5]
	);
	$logDir = $dateNow['year'].'-'.$dateNow['month'].'/';

	if(!is_dir($folder.'/'.$logDir)){mkdir($folder.'/'.$logDir,0777,true);}

	fwrite(fopen($folder."/".$logDir."/".$file.".log","a"),$content);

}

/**
 * Obliczenie ile czasu roboczego upłynęło od czasu zgłoszenia
 * @param datetime $date_begin - data zgłoszenia
 * @param datetime $date_end - data do której ma być obliczana ilość minut
 * @return int $worked_time - czas roboczy wyrażony w minutach
 */
function get_working_hours($date_begin,$date_end)
{
    $from_timestamp = strtotime($date_begin);
    $to_timestamp = strtotime($date_end);

    $days_hours = WorkHours::whereFree(0)->get();
    $days_hoursA = array();
    foreach($days_hours as $day_hour)
    {
        $days_hoursA[$day_hour->id]['from'] = $day_hour->work_from;
        $days_hoursA[$day_hour->id]['to']   = $day_hour->work_to;
    }

    // work days beetwen dates
    $from_date = date('Y-m-d',$from_timestamp);
    $to_date = date('Y-m-d',$to_timestamp);

    $workdays = get_workdays($from_date,$to_date);

    // keep the initial dates for later use
    $d1 = new DateTime($date_begin);
    $d2 = new DateTime($date_end);

    $worked_time = 0;
    // for every worked day, add the hours you want
    foreach($workdays as $date){
        $date = new DateTime($date);
        $week_day = $date->format('N'); // 1 (for Monday) through 7 (for Sunday)
        if (!in_array($week_day,$days_hoursA))
        {
            $end_of_day_format = $date->format('Y-m-d '.$days_hoursA[$week_day]['to']);
            $end_of_day = new DateTime($end_of_day_format);

            $start_of_day_format = $date->format('Y-m-d '.$days_hoursA[$week_day]['from']);
            $start_of_day = new DateTime($start_of_day_format);

            // if this is the first day or the last dy, you have to count only the worked hours
            if ($date->format('Y-m-d') == $d1->format('Y-m-d'))
            {
                $diff = $end_of_day->diff($d1)->format("%H:%I:%S");
                $diff = explode(':', $diff);
                $diff = $diff[0]*3600 + $diff[1]*60 + $diff[0];

                $worked_time += $diff;
            }
            else if ($date->format('Y-m-d') == $d2->format('Y-m-d'))
            {
                $diff = $start_of_day->diff($d2)->format('%H:%I:%S');
                $diff = explode(':', $diff);
                $diff = $diff[0]*3600 + $diff[1]*60 + $diff[0];

                $worked_time += $diff;
            }
            else
            {
                $diff = $start_of_day->diff($end_of_day)->format('%H:%I:%S');
                $diff = explode(':', $diff);
                $diff = $diff[0]*3600 + $diff[1]*60 + $diff[0];

                $worked_time += $diff;
            }
        }

    }

    $worked_time = $worked_time / 60;

    return $worked_time;
}

/**
 * Wyznaczenie dni roboczych w zadanym przedziale czasu
 * @param datetime $from - data początkowa przedziału
 * @param datetime $to - data końcowa przedziału
 * @return array $days_array - tablica zawierająca dni robocze ( elementy w formacie Y-m-d)
 */
function get_workdays($from,$to)
{
    $days_array = array();

    $freeDays = WorkHours::whereFree('1')->get();
    $skipdays = array();
    foreach($freeDays as $freeDay)
        array_push($skipdays, $freeDay->id );

    $skipdates = get_holidays();

    $i = 0;
    $current = $from;

    if($current == $to) // same dates
    {
        $timestamp = strtotime($from);
        if (!in_array(date("N", $timestamp), $skipdays)&&!in_array(date("Y-m-d", $timestamp), $skipdates)) {
            $days_array[] = date("Y-m-d",$timestamp);
        }
    }
    elseif($current < $to) // different dates
    {
        while ($current < $to) {
            $timestamp = strtotime($from." +".$i." day");
            if (!in_array(date("N", $timestamp), $skipdays)&&!in_array(date("Y-m-d", $timestamp), $skipdates)) {
                $days_array[] = date("Y-m-d",$timestamp);
            }
            $current = date("Y-m-d",$timestamp);
            $i++;
        }
    }

    return $days_array;
}

/**
 * Wyznaczenie świąt
 * @return array $days_array - tablica zawierająca święta ( elementy w formacie Y-m-d)
 */
function get_holidays()
{
    $holidays = WorkHolidays::all();

    $days_array = array();

    foreach($holidays as $holiday){
        array_push($days_array, $holiday->day);
    }

    return $days_array;
}

/**
 * Konwersja zadanej ilości minut do formatu godzinowego
 * @param int $total_minutes - ilość minut
 * @return string  - ilość godzin w formacie (-)H:i
 */
function convertToHoursMins($total_minutes) {

    $hours = intval($total_minutes / 60);
    if($hours < 10 && $hours > 0) $hours = '0'.$hours;

    $mins = $total_minutes % 60;
    if($mins < 0 ) $mins = abs($mins);
    if($mins < 10 && $mins > 0) $mins = '0'.$mins;

    return $hours.':'.$mins;
}

/**
 * Sprawdzenie czy zadana wartość jest pustym stringiem
 * @param string $value - sprawdzana wartość
 * @param null $array
 * @param string $returner
 * @return string
 */
function checkIfEmpty($value, $array = null, $returner = '---')
{
    if(is_null($array)) {
        if (isset($value) && trim($value) != '' && $value != '0000-00-00' && $value != '0000-00-00 00:00:00')
            return $value;
    }else{
        if( $array instanceof \Illuminate\Database\Eloquent\Collection && ! $array->isEmpty() ){
            return $array->first()->{$value};
        }else if( array_key_exists($value, $array) && (isset($value) && trim($value) != '' && $value != '0000-00-00' && $value != '0000-00-00 00:00:00') )
            return $array[$value];
    }
    return $returner;
}


/**
 * @param $wayCompare
 * @param $expire
 * @param $expire_confirm
 * @return bool
 */
function dateCompareAlert($wayCompare, $expire, $expire_confirm)
{
    if ($expire != '0000-00-00' && $expire_confirm == '0000-00-00' && dateCompare($wayCompare, $expire))
        return true;
    else
        return false;
}


/**
 * @param $wayCompare
 * @param $to_confirm
 * @return bool
 */
function dateCompare($wayCompare, $to_confirm)
{
    $today = (new DateTime())->format('Y-m-d');

    if($wayCompare == '>')
        return strtotime($today) > strtotime((new DateTime($to_confirm))->format('Y-m-d'));
    if($wayCompare == '==')
        return strtotime($today) == strtotime((new DateTime($to_confirm))->format('Y-m-d'));
}


/**
 * Zwraca wartość jeśli nie jest pusta, w przeciwnym wypadku zadany string
 * @param $value
 * @param string $nullReturner
 * @return string
 */
function valueIfNotNull($value, $nullReturner = '---')
{
    if($value != null && $value != '')
        return $value;
    else
        return $nullReturner;
}

/**
 * Zwraca wartość jeśli nie jest pusta, w przeciwnym wypadku zadany string
 * @param $object
 * @param $value
 * @param string $returner
 * @return string
 */
function checkObjectIfNotNull($object, $value, $returner = '---')
{
    if(is_object($object) && !is_null($object))
        return $object->$value;
    else
        return $returner;
}

/**
 * @param \Illuminate\Database\Eloquent\Collection $collection
 * @param string $key
 * @param string $value
 * @param bool $entry_selector
 * @return array
 */
function generateSelectOptions(\Illuminate\Database\Eloquent\Collection $collection, $value = 'name', $key = 'id',  $entry_selector = true)
{
    $value = explode(',', $value);
    $result = array();
    if( $entry_selector )
        $result[0] = '--- wybierz ---';

    foreach($collection as $collection_element)
    {
        $result[$collection_element->$key] = '';
        foreach($value as $tmp_value)
            $result[$collection_element->$key] .= $collection_element->$tmp_value.' ';
    }

    return $result;
}

function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', '', $string);
}

function str_putcsv($input, $delimiter = ',', $enclosure = '"')
{
    // Open a memory "file" for read/write...
    $fp = fopen('php://temp', 'r+');
    // ... write the $input array to the "file" using fputcsv()...
    fputcsv($fp, $input, $delimiter, $enclosure);
    // ... rewind the "file" so we can read what we just wrote...
    rewind($fp);
    // ... read the entire line into a variable...
    $data = fread($fp, 1048576);
    // ... close the "file"...
    fclose($fp);
    // ... and return the $data to the caller, with the trailing newline from fgets() removed.
    return rtrim($data, "\n");
}

/**
 * Skraca nazwę firmy
 * @param $name
 * @return string
 */
function shortenName($name)
{
    $shortNameA = array();
    $nameExploded = explode(' ', trim($name));
    foreach($nameExploded as $word)
    {
        if (strpos($word, '.') !== false || strlen($word) == 1)
            $shortNameA[] = $word;
        else{
            $shortNameA[] = mb_strtoupper($word[0]).'.';
        }
    }
    $shortName = '';
    foreach ($shortNameA as $subWord)
    {
        if(strlen($subWord) == 2 && strpos($subWord, '.') !== false)
        {
            $shortName .= str_replace('.', '', $subWord);
        }else{
            $shortName .= ' '.$subWord;
        }
    }

    return trim($shortName);
}

/**
 * Get the path to a versioned Elixir file.
 *
 * @param  string  $file
 * @return string
 */
function elixir($file)
{
    static $manifest = null;

    if (is_null($manifest))
    {
        $manifest = json_decode(file_get_contents(public_path().'/build/rev-manifest.json'), true);
    }

    if (isset($manifest[$file]))
    {
        return '/build/'.$manifest[$file];
    }

    throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
}

function myFilter($var){
    return ($var !== NULL && $var !== FALSE && $var !== '');
}

function array_trim($arr)
{
    return array_filter($arr, "myFilter");
}

function stripNonNumeric($string)
{
    $result = preg_replace('/\D/', '', $string);
    return $result;
}

function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}

function isCasActive(){
	$settings = json_decode(file_get_contents(base_path('app/config/constants.json')), true);
	$cas_date =  \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $settings['cas']);

	if( \Carbon\Carbon::now()->gte($cas_date)) {
		return true;
	}

	return false;
}

function sumByKeys($array, $keys)
{
    $sum = 0;

    foreach($array as $key => $value)
    {
        if( in_array($key, $keys, true) )
        {
            $sum += $value;
        }
    }

    return $sum;
}

function contractStatus($status)
{
    $is_active = 0;
    
    if($status && $status != '') {
        $contract_status = ContractStatus::where('name', 'like', $status)->first();

        if ($contract_status) {
            return $contract_status->is_active;
        }

        $is_active = 0;
        if (str_contains(mb_strtoupper($status, 'UTF-8'), 'AKTYWNA')) {
            $is_active = 1;
        }

        ContractStatus::create([
            'name' => $status,
            'is_active' => $is_active
        ]);
    }

    return $is_active;
}

function make_excerpt ($rawHtml) {
    // Detect the string encoding
    $encoding = mb_detect_encoding($rawHtml);

    // pass it to the DOMDocument constructor
    $doc = new DOMDocument('', $encoding);

    // Must include the content-type/charset meta tag with $encoding
    // Bad HTML will trigger warnings, suppress those
    @$doc->loadHTML($rawHtml);

    for ( $list = $doc->getElementsByTagName('script'), $i = $list->length; --$i >=0; ) {
        $node = $list->item($i);
        $node->parentNode->removeChild($node);
    }

    for ( $list = $doc->getElementsByTagName('style'), $i = $list->length; --$i >=0; ) {
        $node = $list->item($i);
        $node->parentNode->removeChild($node);
    }

    // extract the components we want
    $nodes = $doc->getElementsByTagName('body')->item(0)->childNodes;
    $html = '';
    $len = $nodes->length;
    for ($i = 0; $i < $len; $i++) {
        $html .= $doc->saveHTML($nodes->item($i));
    }
    return $html;
}

function slug($title, $separator = '-', $language = 'en')
{
    // Convert all dashes/underscores into separator
    $flip = $separator == '-' ? '_' : '-';
    $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);
    // Replace @ with the word 'at'
    $title = str_replace('@', $separator.'at'.$separator, $title);
    // Remove all characters that are not the separator, letters, numbers, or whitespace.

    // With lower case: $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));
    $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', $title);

    // Replace all separator characters and whitespace by a single separator
    $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);
    return trim($title, $separator);
}

function getDataURI($imagePath) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->file($imagePath);
    return 'data:'.$type.';base64,'.base64_encode(file_get_contents($imagePath));
}

function stripAccents($str) {
    return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

if ( ! function_exists('money_format')) {
    function money_format($format, $number)
    {
        $regex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' .
            '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar' => preg_match('/\=(.)/', $fmatch[1], $match) ?
                    $match[1] : ' ',
                'nogroup' => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                    $match[0] : '+',
                'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft' => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
            $left = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
            $right = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];
            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value *= -1;
            }
            $letter = $positive ? 'p' : 'n';
            $prefix = $suffix = $cprefix = $csuffix = $signal = '';
            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                    ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                    $csuffix;
            } else {
                $currency = '';
            }
            $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';
            $value = number_format($value, $right, $locale['mon_decimal_point'],
                $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
            $value = @explode($locale['mon_decimal_point'], $value);
            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                    STR_PAD_RIGHT : STR_PAD_LEFT);
            }
            $format = str_replace($fmatch[0], $value, $format);
        }
        return $format;
    }
}

function closeHtmlTags($content){
    if($content && $content != '') {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        $images = $dom->getElementsByTagName('img');

        $img_to_remove = [];
        foreach ($images as $image) {

            $src = $image->getAttribute('src');

            if(!preg_match('!^data\:!',$src) && !checkRemoteFile($src)) {
                $img_to_remove[] = $image;
            }

            preg_match('/^data:image\/(\w+);base64,/', $src, $type);
            $encoded_base64_image = substr($src, strpos($src, ',') + 1);
            if(isset($type[1])) {
                $decoded_image = base64_decode($encoded_base64_image);

                try{
                    $img = Image::make($decoded_image);
                    if ($img->width > 1200) {
                        $img->widen(1200);
                    }
                    if ($img->height > 1200) {
                        $img->heighten(1200);
                    }
                    $image->setAttribute('src', $img->encode('data-url'));
                }catch(Intervention\Image\Exception\InvalidImageDataStringException $e){

                }
            }
        }
        foreach($img_to_remove as $img){
            $img->parentNode->removeChild($img);
        }

        // Strip wrapping <html> and <body> tags
        $mock = new \DOMDocument('1.0', 'UTF-8');
        $mock->encoding = 'utf-8';
        $body = $dom->getElementsByTagName('body')->item(0);
        if(! $body || ! property_exists($body, 'childNodes')){
            return trim($dom->saveHTML());
        }
        foreach ($body->childNodes as $child) {
            $mock->appendChild($mock->importNode($child, true));
        }

        $newContent = trim($mock->saveHTML());
        if($newContent != '') {
            return trim($mock->saveHTML());
        }

        return trim($dom->saveHTML());
    }

    return $content;
}

function checkRemoteFile($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    curl_close($ch);
    if($result !== FALSE)
    {
        return true;
    }
    else
    {
        return false;
    }
}