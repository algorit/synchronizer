<?php namespace Synchronizer\Facades;

use Illuminate\Support\Facades\Facade;

class Synchronizer extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'synchronizer'; }

}