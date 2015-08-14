<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Models\Person;
use App\Models\PersonSchedule;
use App\Models\Queue;
use App\Models\QueueMorph;
use \Illuminate\Support\MessageBag as MessageBag;
use DateTime, DateInterval, DatePeriod;

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
		$id 			= $this->option('queueid');

		$result 		= $this->batchpersonchedule($id);

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
			['argument', InputArgument::REQUIRED, 'An example argument.'],
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
            array('queueid', null, InputOption::VALUE_OPTIONAL, 'Queue ID', null),
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

		$parameters 				= (array)json_decode($pending->parameter);

		$errors 					= new MessageBag;

		//check work active on that day, please consider if that queue were written days
		$begin 						= new DateTime( $parameters['onstart'] );
		$ended 						= new DateTime( $parameters['onend'] );

		$interval 					= DateInterval::createFromDateString('1 day');
		$periods 					= new DatePeriod($begin, $interval, $ended);

		foreach ( $periods as $key => $period )
		{
			if(floor($key/$pending->task_per_process) < $pending->total_process)
			{
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
				elseif(($key+1)%$pending->task_per_process==0)
				{
					$pending->fill(['process_number' => ($key+1)/$pending->task_per_process, 'message' => 'Processing']);
					$pending->save();
				}
				else
				{
					$morphed 						= new QueueMorph;

					$morphed->fill([
						'queue_id'					=> $id,
						'queue_morph_id'			=> $is_success->data->id,
						'queue_morph_type'			=> get_class(new PersonSchedule),
					]);

					$morphed->save();
				}
			}
		}

		if(!$errors->count())
		{
			$pending->fill(['process_number' => $pending->total_process, 'message' => 'Success']);
		}
		else
		{
			$pending->fill(['message' => json_encode($errors)]);
		}

		$pending->save();

		return true;

	}

}
