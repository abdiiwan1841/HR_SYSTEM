<?php namespace App\Models\Observers;

use DB, Validator, Event;
use App\Models\Work;
use App\Models\Chart;
use App\Models\Follow;
use App\Models\Person;
use App\Models\Finger;
use App\Models\Organisation;
use App\Models\FollowWorkleave;
use App\Models\Workleave;
use App\Models\WorkAuthentication;

use Illuminate\Support\MessageBag;
use App\Events\CreateRecordOnTable;

/* ----------------------------------------------------------------------
 * Event:
 * 	Created						
 * 	Saving						
 * 	Saved						
 * 	Updating						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class WorkObserver 
{
	public function created($model)
	{
		$attributes['record_log_id'] 		= $model->id;
		$attributes['record_log_type'] 		= get_class($model);
		$attributes['name'] 				= 'Menambah Karir ';
		$attributes['notes'] 				= 'Menambah Karir '.' pada '.date('d-m-Y');
		$attributes['action'] 				= 'delete';

		Event::fire(new CreateRecordOnTable($attributes));

		if(isset($model['attributes']['chart_id']) && $model['attributes']['chart_id']!=0)
		{
			
			$defaultworkleave 					= Workleave::status('CN')->organisationid($model->chart->branch->organisation_id)->active(true)->quota(12)->first();
			if($defaultworkleave)
			{
				$follow 						= new FollowWorkleave;
				$follow->fill([
							'work_id'			=> $model->id,
				]);

				$follow->workleave()->associate($defaultworkleave);

				if(!$follow->save())
				{
					$model['errors'] 			= $follow->getError();

					return false;
				}
			}

			if(strtolower($model['status'])=='admin')
			{
				$auth 								= new WorkAuthentication;
				$auth->fill([
							'tmp_auth_group_id'		=> 1,
				]);
			}
			else
			{
				$auth 								= new WorkAuthentication;
				$auth->fill([
							'tmp_auth_group_id'		=> 5,
				]);
			}


			$auth->Work()->associate($model);

			$organisation 							= Organisation::find($model->chart->branch->organisation_id);

			$auth->Organisation()->associate($organisation);

			if(!$auth->save())
			{
				$model['errors'] 		= $auth->getError();

				return false;
			}
		}

		return true;
	}

	public function saving($model)
	{
		$validator 						= Validator::make($model['attributes'], $model['rules']);

		if ($validator->passes())
		{
			if(isset($model['attributes']['chart_id']) && !is_null($model['attributes']['chart_id']) && $model['attributes']['chart_id']!=0)
			{
				$validator 			= Validator::make($model['attributes'], ['calendar_id'				=> 'required_without:position|required_if:status,contract,trial,internship,permanent'], ['calendar_id.required_without' => 'Kalender kerja tidak boleh kosong. Pastikan posisi memiliki kalender kerja yang dimaksud.']);
				if (!$validator->passes())
				{
					$model['errors'] 	= $validator->errors();

					return false;
				}

				$validator 				= Validator::make($model['attributes'], ['chart_id' => 'exists:charts,id']);
				if ($validator->passes())
				{
					if((!isset($model['attributes']['end']) || is_null($model['attributes']['end'])) && isset($model['attributes']['person_id']) && $model['attributes']['person_id']!=0 && !count($model->person->finger))
					{
						$finger 		= new Finger;
						
						$person			= Person::find($model['attributes']['person_id']);

						$finger->person()->associate($person);

						$finger->save();

					}
					return true;
				}
				else
				{
					$model['errors'] 	= $validator->errors();

					return false;
				}
			}

			if(isset($model['attributes']['calendar_id']) && !is_null($model['attributes']['calendar_id']) && $model['attributes']['calendar_id']!=0)
			{
				$validator 				= Validator::make($model['attributes'], ['calendar_id' => 'exists:calendars,id']);
				if ($validator->passes())
				{
					$check 				= Follow::CalendarID($model['attributes']['calendar_id'])->ChartID($model['attributes']['chart_id'])->get();
					if(count($check))
					{
						return true;
					}

					$errors 			= new MessageBag;
					$errors->add('notmatch', 'Posisi tidak memiliki jadwal tersebut.');

					$model['errors'] 	= $errors;
					
					return false;
				}
				else
				{
					$model['errors'] 	= $validator->errors();

					return false;
				}
				
			}

			return true;
		}
		else
		{
			$model['errors'] 			= $validator->errors();

			return false;
		}
	}

	public function saved($model)
	{
		if(isset($model['attributes']['chart_id']) && $model['attributes']['chart_id']!=0)
		{
			$current_employee 			= Work::where('chart_id',$model['attributes']['chart_id'])->whereNull('end')->orwhere('end', '>=', date('Y-m-d'))->count();
			$updated 					= Chart::where('id', $model['attributes']['chart_id'])->update(['current_employee' => $current_employee]);
		}
		return true;
	}

	public function updating($model)
	{
		if(strtolower($model->getOriginal()['status'])=='admin' && (isset($model->getDirty()['status']) || isset($model->getDirty()['end'])))
		{
			$errors 			= new MessageBag;
			$errors->add('admin', 'Tidak dapat mengubah informasi terkait pekerjaan sebagai admin.');

			$model['errors'] 	= $errors;
					
			return false;
		}
		return true;
	}

	public function deleting($model)
	{
		if($model->chart && $model->chart->count() && $model['attributes']['status']!='admin')
		{
			$model['errors'] 	= ['Tidak dapat menghapus pekerjaan saat ini. Silahkan tandai sebagai pekerjaan yang sudah berakhir dengan catatan khusus.'];

			return false;
		}
	}

	public function updated($model)
	{
		$attributes['record_log_id'] 		= $model->id;
		$attributes['record_log_type'] 		= get_class($model);
		$attributes['name'] 				= 'Mengubah Karir ';
		$attributes['notes'] 				= 'Mengubah Karir '.' pada '.date('d-m-Y');
		$attributes['old_attribute'] 		= json_encode($model->getOriginal());
		$attributes['new_attribute'] 		= json_encode($model->getAttributes());
		$attributes['action'] 				= 'save';

		Event::fire(new CreateRecordOnTable($attributes));
	}

	public function deleted($model)
	{
		$attributes['record_log_id'] 		= $model->id;
		$attributes['record_log_type'] 		= get_class($model);
		$attributes['name'] 				= 'Menghapus Karir';
		$attributes['notes'] 				= 'Menghapus Karir'.' pada '.date('d-m-Y');
		$attributes['action'] 				= 'restore';

		Event::fire(new CreateRecordOnTable($attributes));
	}
}
