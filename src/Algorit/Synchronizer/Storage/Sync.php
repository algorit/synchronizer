<?php namespace Algorit\Synchronizer\Storage;

use Illuminate\Database\Eloquent\Model;

class Sync extends Model {

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
	protected $fillable = ['morph_id', 'url', 'entity', 'type', 'class', 'status', 'response', 'started_at'];

}