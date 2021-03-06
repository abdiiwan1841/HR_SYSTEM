<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Models\Person;
use App\Models\PersonSchedule;
use App\Models\Queue;
use App\Models\QueueMorph;
use \Illuminate\Support\MessageBag as MessageBag;
use DateTime, DateInterval, DatePeriod, DB;

class PersonScheduleBatchCommand extends Command {

	use \Illuminate\Foundation\Bus\DispatchesCommands;
	use \Illuminate\Foundation\Validation\ValidatesRequests;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'hr:personschedulebatch';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Batch Person Schedule Command.';

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
		$id 			= $this->argument()['queueid'];

		if($this->option('queuefunc'))
		{
			$queuefunc 	= $this->option('queuefunc');
			switch ($queuefunc) 
			{
				case 'delete':
					$result = $this->batchdeletepersonschedule($id);
					break;
			
				default:
					$result = $this->batchpersonchedule($id);
					break;
			}
		}
		else
		{
			$result 		= $this->batchpersonchedule($id);
		}
		
		return $result;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['queueid', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
            array('queuefunc', null, InputOption::VALUE_OPTIONAL, 'Queue Function', null),
        );
	}

	/**
	 * update 1st version
	 *
	 * @return void
	 * @author 
	 **/
	public function batchpersonchedule($id)
	{
		$queue 						= new Queue;
		$pending 					= $queue->find($id);

		$parameters 				= json_decode($pending->parameter, true);

		$errors 					= new MessageBag;

		//check work active on that day, please consider if that queue were written days
		$begin 						= new DateTime( $parameters['onstart'] );
		$ended 						= new DateTime( $parameters['onend'] );

		$interval 					= DateInterval::createFromDateString('1 day');
		$periods 					= new DatePeriod($begin, $interval, $ended);

		foreach ( $periods as $key => $period )
		{
			if(($pending->process_number-1) < $key)
			{
				DB::beginTransaction();

				$psch 				= PersonSchedule::personid($parameters['associate_person_id'])->ondate([$period->format('Y-m-d'), $period->format('Y-m-d')])->status(strtoupper($parameters['status']))->first();
				if(!$psch)
				{
					$psid 			= null;
				}
				else
				{
					$psid 			= $psch->id;
				}

				$parameters['on'] 					= $period->format('Y-m-d');
				$content 							= $this->dispatch(new Saving(new PersonSchedule, $parameters, $psid, new Person, $parameters['associate_person_id']));
				$is_success 						= json_decode($content);

				if(!$is_success->meta->success)
				{
					foreach ($is_success->meta->errors as $key => $value) 
					{
						if(is_array($value))
						{
							foreach ($value as $key2 => $value2) 
							{
								$errors->add('Batch', $value2);
							}
						}
						else
						{
							$errors->add('Batch', $value);
						}
					}
				}
				
				if(!$errors->count())
				{
					DB::commit();

					$pnumber 						= $pending->process_number+1;
					$messages[] 					= 'Sukses Menyimpan Jadwal '.(isset($parameters['name']) ? $parameters['name'] : '');
					$pending->fill(['process_number' => $pnumber, 'message' => json_encode($messages)]);

					$morphed 						= new QueueMorph;

					$morphed->fill([
						'queue_id'					=> $id,
						'queue_morph_id'			=> $is_success->data->id,
						'queue_morph_type'			=> get_class(new PersonSchedule),
					]);

					$morphed->save();
				}
				else
				{
					DB::rollback();
					
					$pnumber 						= $pending->process_number+1;
					$messages[] 					= ['Gagal Menyimpan Jadwal '.(isset($parameters['name']) ? $parameters['name'] : ''), 'errors' => $errors];
					$pending->fill(['process_number' => $pnumber, 'message' => json_encode($messages)]);
				}

				$pending->save();
			}

		}

		if($errors->count())
		{
			$messages[] 						= ['Gagal Menyimpan Jadwal '.(isset($parameters['name']) ? $parameters['name'] : ''), 'errors' => $errors];
			$pending->fill(['message' => json_encode($messages)]);
		}
		else
		{
			$messages[] 						= 'Sukses Menyimpan Jadwal '.(isset($parameters['name']) ? $parameters['name'] : '');
			$pending->fill(['process_number' => $pending->total_process, 'message' => json_encode($messages)]);
		}

		$pending->save();
		
		return true;
	}

	/**
	 * update 1st version
	 *
	 * @return void
	 * @author 
	 **/
	public function batchdeletepersonschedule($id)
	{
		$queue 						= new Queue;
		$pending 					= $queue->find($id);

		$parameters 				= json_decode($pending->parameter, true);
		$messages 					= json_decode($pending->message, true);

		$errors 					= new MessageBag;

		//check work active on that day, please consider if that queue were written days
		$data 						= PersonSchedule::find($parameters['id']);

		$psched 					= $data;
		
		DB::beginTransaction();

		if(!$data->delete())
		{
			$errors->add('Batch', $data->getError());
		}

		if(!$errors->count())
		{
			DB::commit();
		
			$pnumber 						= $pending->process_number+1;
			$messages['message'][$pnumber] 	= 'Sukses Menghapus Jadwal '.(isset($psched['name']) ? $psched['name'] : '');
			$pending->fill(['process_number' => $pnumber, 'message' => json_encode($messages)]);
		}
		else
		{
			DB::rollback();
		
			$pnumber 						= $pending->process_number+1;
			$messages['message'][$pnumber] 	= 'Gagal Menghapus Jadwal '.(isset($psched['name']) ? $psched['name'] : '');
			$messages['errors'][$pnumber] 	= $errors;

			$pending->fill(['process_number' => $pnumber, 'message' => json_encode($messages)]);
		}

		$pending->save();

		return true;
	}
}
