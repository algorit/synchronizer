<?php

return array(

	'bindings' => [
    	'method'  => 'Algorit\Synchronizer\Request\Methods\Requests',
    ],

    'log' => [
    	'console' => true,
    	'path'	  => storage_path() . '/logs/' . 'log-' . php_sapi_name() . '.txt';
    ]

);