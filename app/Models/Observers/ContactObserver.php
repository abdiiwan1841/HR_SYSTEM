<?php namespace App\Models\Observers;

use DB, Validator, Event;
use App\Models\Contact;
use Illuminate\Support\MessageBag;
use App\Events\CreateRecordOnTable;

/* ----------------------------------------------------------------------
 * Event:
 * 	Saving						
 * 	Updating						
 * 	Saved						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class ContactObserver 
{
	public function saving($model)
	{
		$validator 						= Validator::make($model['attributes'], $model['rules'], ['value.required' => $model['attributes']['item'].' Tidak boleh kosong']);

		if ($validator->passes())
		{
			if ($model['attributes']['item']!=strtolower($model['attributes']['item']))
			{
				$errors 				= new MessageBag;
				
				$errors->add('default', 'Item harus dalam format huruf kecil');
			
				$model['errors'] 		= $errors;

				return false;
			}
			if ($model['attributes']['item']=='email')
			{
				$validator 				= Validator::make($model['attributes'], ['value' => 'email|unique:contacts,value,'.(isset($model['attributes']['id']) ? $model['attributes']['id'] : '')], ['value.unique' => 'Email sudah terpakai', 'value.email' => 'Format Email Tidak Sah']);

				if ($validator->passes())
				{
					return true;
				}
				
				$model['errors'] 		= $validator->errors();

				return false;
			}

			return true;
		}
		else
		{
			$model['errors'] 			= $validator->errors();

			return false;
		}
	}

	public function updating($model)
	{
		//
		if(isset($model->getDirty()['item']) && $model->getOriginal()['is_default']==true)
		{
			$errors 				= new MessageBag;

			$errors->add('default', 'Tidak dapat mengubah kontak aktif');
			$model['errors'] 		= $errors;

			return false;
		}
		return true;
	}

	public function saved($model)
	{
		if (isset($model['attributes']['is_default']) && $model['attributes']['is_default']==1)
		{
			if (isset($model['attributes']['branch_id']) && $model['attributes']['branch_id']!=0)
			{
				$updates 				= Contact::where('item',$model['attributes']['item'])->where('branch_id',$model['attributes']['branch_id'])->where('id','<>',$model['attributes']['id'])->update(['is_default' => 0]);
			}
			elseif (isset($model['attributes']['person_id']) && $model['attributes']['person_id']!=0)
			{
				$updates 				= Contact::where('item',$model['attributes']['item'])->where('person_id',$model['attributes']['person_id'])->where('id','<>',$model['attributes']['id'])->update(['is_default' => 0]);
			}

			if(isset($updates) && !count($updates))
			{
					$errors 			= new MessageBag;
					$errors->add('default', 'Tidak dapat Menyimpan Kontak Utama (Primary).');
				
					$model['errors'] 	= $errors;

				return false;
			}
		}

		return true;
	}

	public function deleting($model)
	{
		if($model['attributes']['is_default'])
		{
			$model['errors'] 			= ['Tidak dapat menghapus Kontak Utama (Primary)'];

			return false;
		}
	}

	public function created($model)
	{
		$attributes['record_log_id'] 		= $model->id;
		$attributes['record_log_type'] 		= get_class($model);
		$attributes['name'] 				= 'Menambah Informasi Kontak';
		$attributes['notes'] 				= 'Menambah Informasi Kontak'.' pada '.date('d-m-Y');
		$attributes['action'] 				= 'delete';

		Event::fire(new CreateRecordOnTable($attributes));
	}

	public function updated($model)
	{
		$attributes['record_log_id'] 		= $model->id;
		$attributes['record_log_type'] 		= get_class($model);
		$attributes['name'] 				= 'Mengubah Informasi Kontak ';
		$attributes['notes'] 				= 'Mengubah Informasi Kontak '.' pada '.date('d-m-Y');
		$attributes['old_attribute'] 		= json_encode($model->getOriginal());
		$attributes['new_attribute'] 		= json_encode($model->getAttributes());
		$attributes['action'] 				= 'save';

		Event::fire(new CreateRecordOnTable($attributes));
	}

	public function deleted($model)
	{
		$attributes['record_log_id'] 		= $model->id;
		$attributes['record_log_type'] 		= get_class($model);
		$attributes['name'] 				= 'Menghapus Informasi Kontak';
		$attributes['notes'] 				= 'Menghapus Informasi Kontak'.' pada '.date('d-m-Y');
		$attributes['action'] 				= 'restore';

		Event::fire(new CreateRecordOnTable($attributes));
	}
}
