<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FillInjuryStepHistory extends Command {

	private $historyTypesDictionary = [
		// '27' => null,
		// '28' => null,
		// '32' => 'Telefon do assistance',
		// '34' => 'Dodatkowe oględziny',
		// '35' => 'Akceptacja techniczna',
		// '36' => 'Części zamówione',
		// '37' => 'Części na miejscu',
		// '83' => 'Akceptacja faktury',
		// '84' => 'Zgoda warsztatu',
		// '85' => 'Weryfikacja',
		// '86' => 'Akceptacja faktury',
		// '100' => 'Części Polcar',
		// '110' => 'Samochód odebrany przez klienta',
		// '111' => 'Upoważnienie',
		'178' => 45, //'kradzież zakończona odmową',
		'179' => 44, //'kradzież zakończona wypłatą',
		'180' => 34, //'szkoda całkowita zakończona wypłatą',
		'181' => 35, //'szkoda całkowita zakończona odmową',
		'182' => 36, //'szkoda całkowita brak roszczeń',
		'183' => 37, //'szkoda całkowita umowa rozliczona',
		'191' => 31, //'szkoda całkowita w trakcie rozliczenia',
		'194' => 46, //'kradzież umowa rozliczona',
		'200' => 15, //'zakończone wypłatą',
		'201' => 35, //'zakończone odmową',
		'202' => 32, //'całkowita wypłata',
		'203' => 33, //'całkowita odmowa',
		'204' => 42, //'kradzież wypłata',
		'205' => 43, //'kradzież odmowa',
		'213' => '',
		'218' => '',
	];

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'fill:injury-step-history';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$histories = InjuryHistory::whereIn('history_type_id', array_keys($this->historyTypesDictionary))->get();
		foreach($histories as $history) {
			$injuryStepHistory = InjuryStepHistory::create([
				'user_id' => $history->user_id,
				'injury_id' => $history->injury->id,
				'prev_step_id' => null,
				'next_step_id' => $this->getInjuryStep($history),
				'injury_step_stage_id' => null,
				'created_at' => $history->created_at,
			]);
			$injuryStepHistory->update([
			]);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
		);
	}

	private function getInjuryStep($history) {
		if ($history->history_type_id == 213 ||  $history->history_type_id == 218 ) {
			$injuryStepName = trim($history->value, " - ");
			$injuryStep = InjurySteps::where('name', $injuryStepName)->first();
			return $injuryStep ? $injuryStep->id: null;
		} else {
			return $this->historyTypesDictionary[$history->history_type_id];
		}

	}

}
