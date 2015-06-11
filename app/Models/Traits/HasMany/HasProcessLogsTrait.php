<?php namespace App\Models\Traits\HasMany;

use DB;

trait HasProcessLogsTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasProcessLogsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN SCHEDULE PACKAGE -------------------------------------------------------------------*/

	public function ProcessLogs()
	{
		return $this->hasMany('App\Models\ProcessLog');
	}

	public function ScopeGlobalAttendanceReport($query, $variable)
	{
		return $query->with(['processlogs' => function($q)use($variable){$q->ondate($variable['on'])->global(true);}]);
	}
}