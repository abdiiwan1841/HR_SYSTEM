<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\AttendanceDetail;
use DB;

class HRResetTableCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'hr:resettable';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command Reset Table Log.';

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
		//
		$result 		= $this->emptytable();

		return true;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

	/**
	 * EMPTY TABLE
	 *
	 * @return void
	 * @author 
	 **/
	public function emptytable()
	{
		DB::table('process_logs')->truncate();
		$this->info("Truncate Process Logs");

		DB::table('idle_logs')->truncate();
		$this->info("Truncate Idle Logs");

		DB::table('attendance_logs')->truncate();
		$this->info("Truncate Attendance Logs");

		DB::table('logs')->truncate();
		$this->info("Truncate Logs");

		DB::table('error_logs')->truncate();
		$this->info("Truncate Error Logs");

		DB::table('record_logs')->truncate();
		$this->info("Truncate Record Logs");

		$attendance_details 			= AttendanceDetail::get();

		foreach ($attendance_details as $key => $value) 
		{
			if($value->PersonWorkleave->delete())
			{
				$this->info($value->errors);
			}
			elseif($value->PersonDocument->delete())
			{
				$this->info($value->errors);
			}
		}

		DB::table('attendance_details')->truncate();

		$this->info("Truncate Attendance Details");

		return true;
	}

}
