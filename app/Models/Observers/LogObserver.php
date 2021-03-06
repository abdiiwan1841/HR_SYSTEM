<?php namespace App\Models\Observers;

use DB, Validator;
use App\Models\ProcessLog;
use App\Models\Person;
use App\Models\Log;

/* ----------------------------------------------------------------------
 * Event:
 * 	Saving						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class LogObserver 
{
	public function saving($model)
	{
		$validator 				= Validator::make($model['attributes'], $model['rules']);

		if ($validator->passes())
		{
			return true;
		}
		else
		{
			$model['errors'] 	= $validator->errors();

			return false;
		}
	}

	public function deleting($model)
	{
		if(date('Y-m-d',strtotime($model['attributes']['on'])) <= date('Y-m-d'))
		{
			$model['errors'] 	= ['Tidak dapat menghapus log yang sudah lewat dari tanggal hari ini.'];

			return false;
		}

		$logs 								= Log::personid($model['attributes']['person_id'])->ondate([date('Y-m-d',strtotime($model['attributes']['on'])), date('Y-m-d',strtotime($model['attributes']['on'].' + 1 Day'))])->get();

		if($logs->count() && $logs->count() <= 1)
		{
			$processes 							= ProcessLog::personid($model['attributes']['person_id'])->ondate([date('Y-m-d',strtotime($model['attributes']['on'])), date('Y-m-d',strtotime($model['attributes']['on'].' + 1 Day'))])->get();

			foreach ($processes as $key => $value) 
			{
				$process 						= ProcessLog::find($value->id);

				if(!$process->delete())
				{
					$model['errors'] 			= $process->getError();
					
					return false;
				}
			}
		}
	}
}
