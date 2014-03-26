<?php namespace Algorit\Synchronizer\Storage;

use Algorit\Veloquent\Veloquent;

class Sync extends Veloquent {

	/**
	* The table name.
	*
	* @var string
	*/	
	protected $table = 'syncs';

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/			
	protected $fillable = ['morph_id', 'entity', 'type', 'class', 'status', 'response'];

	/**
	* The validation rules.
	*
	* @var array
	*/
	protected $rules = [];

}