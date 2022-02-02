<?php
return array(
    'allowedIP' => array(
        '79.96.196.145'
    ),

    'accountancy_email' => array(
       
    ),

	'imageCategory' => array(
			'1' => 'przyjęcie',
			'2' => 'w trakcie',
			'3'	=> 'po naprawie'
		),
	'fileCategory' => array(
			'1' => 'wniosek o upoważnienie',
			'2' => 'kosztorys',
			'3'	=> 'faktura VAT',
			'4'	=> 'faktura VAT korekta',
			'5' => 'pismo ZU',
			'6'	=> 'decyzja ZU',
			'7' => 'odmowa ZU',
			'8'	=> 'notatka policyjna',
			'9' => 'oświadczenie sprawcy',
			'11' => 'prawo jazdy',
			'12' =>	'dowód rej.',
			'13' =>	'karta pojazdu',
			'14' => 'zaświadczenie o wyrejestrowaniu pojazdu',
			'15' => 'cesja praw na ZU',
			'17' => 'druk zgłoszenia szkody',
			'18' => 'kwalifikacja szkody',
            '19' => 'rozrachunek',
            '20' => 'OBIEGÓWKA',
            '21' => 'WYCENA',
            '23' => 'rozrachunek - zaległ. do 30 dni',
            '22' => 'Polisa',
            '24' => 'podpisane zlecenie',
			'16' => 'inne'
		),

	'insurancesFileCategory' => array(
			'1' => 'polisa',
			'2' => 'aneks',
			'3' => 'wniosek o ubezpieczenie',
			'4' => 'zgłoszenie do ubezpieczenia',
			'5' => 'zwrot składki',
			'6' => 'inne'
	),


	//dok
	'dokFileCategory' => array(
			'1' => 'inne'
		),
	'dokDocumentCategory' => array(

		),
	'dokDocumentCategoryRoute' => array(

		),
	'dokDocumentCategoryFolders' => array(

		),

    'injuriesStepOptionsIncludes' => array(
        '-10'   => 'canceled',
        '-7'    => 'total-finished',
        '-5'    => 'total',
        '-3'    => 'theft',
        '0'     => 'new',
        '10'    => 'inprogress',
        '11'    => 'inprogress',
		'13'	=> 'inprogress',
        '14'    => 'inprogress',
        '15'    => 'completed',
		'16'    => 'completed',
        '17'    => 'completed',
		'18'    => 'completed',
        '19'    => 'completed',
        '20'    => 'inprogress',
        '21'    => 'completed',
        '22'    => 'inprogress',
        '23'    => 'completed',
        '24'    => 'completed',
        '25'    => 'completed',
        '26'    => 'completed',
        '30'    => 'total',
        '31'    => 'total',
        '32'    => 'total',
        '33'    => 'total',
        '34'    => 'completed',
        '35'    => 'completed',
        '36'    => 'completed',
        '37'    => 'completed',
        '38'    => 'completed',
        '40'    => 'theft',
        '41'    => 'theft',
        '42'    => 'theft',
        '43'    => 'theft',
        '44'    => 'completed',
        '45'    => 'completed',
        '46'    => 'theft',
        '47'    => 'total-finished',
    ),

    'dosInjuriesStepOptionsIncludes' => [
            '-10'   => 'canceled',

            '0'     => 'new',

            '10'    => 'inprogress',

            '15'    => 'completed',
            '17'    => 'completed',
            '19'    => 'completed',
            '20'    => 'completed',
            '21'    => 'completed',

            '25'    => 'total',
            '26'    => 'total',
            '27'    => 'total',

            '28'    => 'total-finished',
            '29'    => 'total-finished',

            '30'    => 'theft-finished',
            '31'    => 'theft-finished',
            '32'    => 'theft-finished',

            '33'    => 'total-finished',
            '34'    => 'total-finished',
            '41'    => 'completed',
            '42'    => 'total-finished',
            '43'    => 'total-finished',
            '44'    => 'total-finished',
            '45'    => 'total-finished',
            '46'    => 'completed',
    ],


    'wreck_buyers'  =>  array(
        '1' =>  'Leasingobiorca',
        '2' =>  'Oferent aukcyjny',
        '3' =>  'Aukcja wewnętrzna',
        '4' => 'Sprzedaż realizowana przez DSP',
        '5' => 'Brak sprzedaży',
        '6' => 'Kompensata',
        '7' => 'Oferent aukcyjny WB'
    ),

    'insurance_options_definition' => array(
        '0' =>  'niesprawdzono',
        '1' =>  'tak',
        '2' =>  'nie'
    ),

    'broker_data' => array(
        'name' => 'Europejski Dom Brokerski sp. z o.o.',
        'street' => 'ul. Gwiaździsta 66',
        'post' => '53-413',
        'city' => ' Wrocław'
    ),

    'injury_steps_translate' => array(
        '-10' => 'szkoda anulowana',
        '-7' => 'zakończona totalnie',
        '-5' => 'szkoda całkowita',
        '-3' => 'kradzież',
        '0' => 'nowe',
        '5' => 'w obsłudze',
        '10' => 'w trakcie naprawy',
        '15' => 'zakończone',
        '17' => 'zakończone bez likwidacji',
        '19' => 'zakończone bez naprawy',
        '20' => 'odmowa zakładu ubezpieczeń'
    ),

    'compensationsNetGross' => array(
        '0' => null,
        '1' => 'netto',
        '2' => 'brutto',
        '3' => 'netto +50%'
    ),

    'withdrawReasons' => array(
        '1' => 'Ubezpieczenie obce',
        '2' => 'Włączenie do ubezpieczenia komunikacyjnego',
        '3' => 'Inne: (podaj jakie)'
    ),

    'currencies' => array(
        '1' => 'PLN',
        '2' => 'EUR'
    ),

    'net_gross' => array(
        '1' => 'netto',
        '2' => 'brutto',
        '3' => '50% VAT'
    ),

	'gap_descriptions' => [
		'0' => '--- opis ---',
		'1'	=> 'wypłata',
		'2' => 'odmowa',
		'3' => 'odwołanie'
	],

	'theft_acceptation_satutes' => [
		'0' => '--- wybierz status ---',
		'1' => 'brak',
		'2' => 'spalony',
		'3' => 'zagubiony',
		'4' => 'skradziony poj.',
		'5' => 'w aktach'
	],

	'insurances_creating_way' => [
		'1' => 'ręcznie',
		'2' => 'wgranie nowych umów majątku',
		'3' => 'wgranie umów jachtów',
		'4'	=> 'wgranie raportu ubezpieczyciela'
	],

  'estimate_gross'=> 23,

    'sap_rodzszk' => [
        '0' => '--- wybierz status ---',
        'CZ' => 'CZ',
        'CZA' => 'CZA',
        'TOT' => 'TOT',
        'KRA' => 'KRA'
    ]
);
