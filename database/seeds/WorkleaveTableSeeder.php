<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Workleave;
use App\Models\Organisation;
use \Faker\Factory, Illuminate\Support\Facades\DB;

class WorkleaveTableSeeder extends Seeder
{
	function run()
	{

		DB::table('tmp_workleaves')->truncate();
		$faker 										= Factory::create();
		$organisation 								= Organisation::find(1);
		$workleaves 								= ['Cuti Tahunan', 'Cuti Melahirkan', 'Cuti Menikah'];
		$status 									= ['CN', 'CI', 'CI'];
		$quota 										= ['12', '15', '18', '14', '21', '6'];
		try
		{
			foreach(range(0, count($workleaves)-1) as $index)
			{
				$data 								= new Workleave;
				$data->fill([
					'name'							=> $workleaves[$index],
					'quota'							=> $quota[rand(0, count($quota)-1)],
					'status'						=> $status[$index],
					'is_active'						=> true,
				]);

				$data->Organisation()->associate($organisation);

				if (!$data->save())
				{
					print_r($data->getError());
					exit;
				}
			} 
		}
		catch (Exception $e) 
		{
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
		}	
	}
}