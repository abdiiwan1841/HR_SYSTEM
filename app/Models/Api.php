<?php namespace App\Models;


/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	branch_id 						: Required, Integer, FK from Branch
 * 	client 			 				: Required, unique, max : 255
 * 	secret 			 				: Required, max : 255
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * ---------------------------------------------------------------------- */

/* ----------------------------------------------------------------------
 * Document Relationship :
	//other package
	1 Relationship belongsTo
	{
		Branch
	}

 * ---------------------------------------------------------------------- */

use Str, Validator, DateTime, Exception;

class Api extends BaseModel {

	use \App\Models\Traits\BelongsTo\HasBranchTrait;

	public 		$timestamps 		= true;

	protected 	$table 				= 'apis';

	protected 	$fillable			= 	[
											'client' 							,
											'secret' 							,
										];

	protected 	$rules				= 	[
											'client' 							=> 'required',
											'secret' 							=> 'required',
										];

	public $searchable 				= 	[
											'id' 								=> 'ID', 
											'branchid' 							=> 'BranchID', 
											
											'client' 							=> 'Client', 
											'secret' 							=> 'Secret', 
											'withattributes' 					=> 'WithAttributes',
										];

	public $sortable 				= 	['chart_id', 'created_at'];

	/* ---------------------------------------------------------------------------- CONSTRUCT ----------------------------------------------------------------------------*/
	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/
	static function boot()
	{
		parent::boot();

		Static::saving(function($data)
		{
			$validator = Validator::make($data->toArray(), $data->rules);

			if ($validator->passes())
			{
				return true;
			}
			else
			{
				$data->errors = $validator->errors();
				return false;
			}
		});
	}

	/* ---------------------------------------------------------------------------- ERRORS ----------------------------------------------------------------------------*/
	/**
	 * return errors
	 *
	 * @return MessageBag
	 * @author 
	 **/
	function getError()
	{
		return $this->errors;
	}

	/* ---------------------------------------------------------------------------- QUERY BUILDER ---------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ---------------------------------------------------------------------------------*/

	/* ---------------------------------------------------------------------------- ACCESSOR --------------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS -------------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- SCOPE -------------------------------------------------------------------------------*/

	public function scopeClient($query, $variable)
	{
		return $query->where('client', $variable);
	}

	public function scopeSecret($query, $variable)
	{
		return $query->where('secret', $variable);
	}
}