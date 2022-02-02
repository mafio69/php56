<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Carbon\Carbon;

class generateSeedCodeByModel extends Command {

 /**
  * The console command name.
  *
  * @var string
  */
	protected $name = 'system:generate-seed-code';

 /**
  * The console command description.
  *
  * @var string
  */
	protected $description = 'Generate seeder file with seed ready code from models table data.';

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
  try {
   mkdir(app_path('/database/seeds/generated_seeder_files'), 0700, true);
  } catch (ErrorException $e) {
   $this->info("Folder istnieje");
  }
		if ($this->option('table')) $this->generateByPivotTable();
		else $this->generateByModel();
 }

 /**
  * Get the console command arguments.
  *
  * @return array
  */
	protected function getArguments()
 {
  return array();
 }

 /**
  * Get the console command options.
  *
  * @return array
  */
	protected function getOptions()
 {
  return array(
			array('table', 't', InputOption::VALUE_NONE, 'If table (pivot only)')
  );
 }

	private function generateByModel() {
  $model = $this->ask('Klasa modelu:');
  $fp    = fopen('app/database/seeds/generated_seeder_files/seeder_' . $model . '_' . Carbon::now()->format('Y_m_d_H_i_s') . '.txt', 'w');

  fwrite($fp, '$model = new ' . $model . ';' . "\n\n");
  try {
      $rows = $model::withTrashed()->get();
  } catch (BadMethodCallException $e) {
      $rows = $model::all();
  }
  foreach ($rows as $row) {
   $row->setAppends([]);
   $row_arr = $row->attributesToArray();
   $new_row = '[';
   foreach ($row_arr as $k => $val) {
    if ($k != 'created_at' && $k != 'updated_at') {
     $val = is_null($row[$k]) ? "null" : '\'' . str_replace('\'', '\\\'', $val) . '\'';
     $new_row = $new_row . '\'' . $k . '\'' . '=>' . $val . ', ';
    }
   }
   $new_row = $new_row . ']';
   $data    = print_r($new_row, true);
   fwrite($fp, '$this->insertAction($model, ' . $data . ');' . "\n");
  }
  $this->info("Plik wygenerowany");
  fclose($fp);
 }

	private function generateByPivotTable() {
  $model = $this->ask('Nazwa tabeli pivota:');
  $fp    = fopen('app/database/seeds/generated_seeder_files/seeder_pivot_' . $model . '_' . Carbon::now()->format('Y_m_d_H_i_s') . '.txt', 'w');

  fwrite($fp, '$table = \'' . $model . '\';' . "\n\n");
  $rows = DB::select('select * from ' . $model);
  if ($rows) {
   foreach ($rows as $row) {
    $new_row = '[';
    foreach ($row as $k => $val) {
     $val     = '\'' . $val . '\'';
     $new_row = $new_row . '\'' . $k . '\'' . '=>' . $val . ', ';
    }
    $new_row = $new_row . ']';
    $data    = print_r($new_row, true);
    fwrite($fp, '		$this->insertPivotAction($table, ' . $data . ');' . "\n");
   }
   $this->info("Plik wygenerowany");
  }
  fclose($fp);
 }

}
