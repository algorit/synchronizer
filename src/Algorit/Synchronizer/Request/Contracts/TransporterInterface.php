<?php namespace Algorit\Synchronizer\Request\Contracts;

interface TransporterInterface {

	public function getRequestUrl();

	public function execute();

}