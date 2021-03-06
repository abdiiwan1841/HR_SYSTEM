<?php namespace App\Http\Controllers;
use Input, Session, App, Paginator, Redirect, DB, Config;
use App\Http\Controllers\BaseController;
use Illuminate\Support\MessageBag;
use App\Console\Commands\Checking;
use App\Console\Commands\Deleting;
use App\Console\Commands\Saving;
use App\Console\Commands\Getting;
use App\Console\Commands\Restoring;
use App\Models\Organisation;
use App\Models\Person;
use App\Models\Queue;

class QueueController extends BaseController
{
	protected $controller_name = 'queue';

	public function index($page = 1)
	{
		// $filter 									= [];

		// if(Input::has('q'))
		// {
		// 	$filter['search']['ondate']				= Input::get('q');
		// 	$filter['active']['q']					= 'Cari Tanggal "'.Input::get('q').'"';
		// }
		// if(Input::has('filter'))
		// {
		// 	$dirty_filter 							= Input::get('key');
		// 	$dirty_filter_value 					= Input::get('value');

		// 	foreach ($dirty_filter as $key => $value) 
		// 	{
		// 		if (str_is('search_*', strtolower($value)))
		// 		{
		// 			$filter_search 						= str_replace('search_', '', $value);
		// 			$filter['search'][$filter_search]	= $dirty_filter_value[$key];
		// 			$filter['active'][$filter_search]	= $dirty_filter_value[$key];
		// 			switch (strtolower($filter_search)) 
		// 			{
		// 				case 'ondate':
		// 					$active = 'Cari Tanggal ';
		// 					break;

		// 				default:
		// 					$active = 'Cari Tanggal ';
		// 					break;
		// 			}

		// 			switch (strtolower($dirty_filter_value[$key])) 
		// 			{
		// 				case 'asc':
		// 					$active = $active.'"'.$dirty_filter_value[$key].'"';
		// 					break;
						
		// 				default:
		// 					$active = $active.'"'.$dirty_filter_value[$key].'"';
		// 					break;
		// 			}

		// 			$filter['active'][$filter_search]	= $active;

		// 		}
		// 		if (str_is('sort_*', strtolower($value)))
		// 		{
		// 			$filter_sort 						= str_replace('sort_', '', $value);
		// 			$filter['sort'][$filter_sort]		= $dirty_filter_value[$key];
		// 			switch (strtolower($filter_sort)) 
		// 			{
		// 				case 'start':
		// 					$active = 'Urutkan Tanggal Aktif ';
		// 					break;
						
		// 				default:
		// 					$active = 'Urutkan Tanggal Aktif ';
		// 					break;
		// 			}

		// 			switch (strtolower($dirty_filter_value[$key])) 
		// 			{
		// 				case 'asc':
		// 					$active = $active.' (Z-A)';
		// 					break;
						
		// 				default:
		// 					$active = $active.' (A-Z)';
		// 					break;
		// 			}

		// 			$filter['active'][$filter_sort]		= $active;
		// 		}
		// 	}
		// }

		$this->layout->page 					= view('pages.queue.index');
		$this->layout->page->controller_name 	= $this->controller_name;
		// $this->layout->page->filter 			= 	[
		// 												['prefix' => 'sort', 'key' => 'start', 'value' => 'Urutkan Tanggal Aktif', 'values' => [['key' => 'asc', 'value' => 'A-Z'], ['key' => 'desc', 'value' => 'Z-A']]]
		// 											];
														
		// $this->layout->page->filtered 			= $filter;

		return $this->layout;
	}
	
	public function create($id = null)
	{
		//		
	}
	
	public function store($id = null)
	{
		if(Input::has('id'))
		{
			$id 								= Input::get('id');
		}
		else
		{
			App::abort(404);
		}

		$search['id'] 							= $id;
		$search['level'] 						= Session::get('user.menuid');
		$search['organisationid'] 				= Session::get('user.organisationids');
		$sort 									= [];
		$results 								= $this->dispatch(new Getting(new Queue, $search, $sort , 1, 1));
		$contents 								= json_decode($results);

		if(!$contents->meta->success)
		{
			App::abort(404);
		}

		$errors 								= new MessageBag();

		DB::beginTransaction();

		$queue 								= json_decode(json_encode($contents->data), true);

		if(!$errors->count())
		{
			$attributes 					= $queue;
			$attributes['action']			= '';
			$content 						= $this->dispatch(new Saving(new Queue, $attributes, $id));
			$is_success 					= json_decode($content);

			if(!$is_success->meta->success)
			{
				foreach ($is_success->meta->errors as $key => $value) 
				{
					if(is_array($value))
					{
						foreach ($value as $key2 => $value2) 
						{
							$errors->add('Queue', $value2);
						}
					}
					else
					{
						$errors->add('Queue', $value);
					}
				}
			}
		}

		if(!$errors->count())
		{
			DB::commit();
			return Redirect::route('hr.recordlogs.index')->with('alert_success', 'Notifikasi sudah ditindaklanjuti');
		}
		
		DB::rollback();
		return Redirect::back()->withErrors($errors)->withInput();
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		//
	}

	public function destroy($id)
	{
		//
	}
}