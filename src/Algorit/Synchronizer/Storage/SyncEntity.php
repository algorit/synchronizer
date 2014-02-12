<?php namespace Algorit\Synchronizer\Storage;

use Algorit\Veloquent\Veloquent;

class SyncEntity extends Veloquent {

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/			
	protected $fillable = ['erp_id', 'company_id', 'representative_id', 'entity', 'type', 'class', 'status', 'response'];

	/**
	* The validation rules.
	*
	* @var array
	*/
	protected $rules = [];

}