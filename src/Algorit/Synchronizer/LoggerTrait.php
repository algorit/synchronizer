<?php namespace Algorit\Synchronizer;

Trait LoggerTrait {

	protected $logger;

	/**
	 * Set the logger instance
	 *
	 * @param  \Psr\Log\LoggerInterface $logger
	 * @return void
	 */
	public function setLogger($logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Get the logger instance
	 *
	 * @param  void
	 * @return \Psr\Log\LoggerInterface
	 */
	public function getLogger()
	{
		return $this->logger;
	}

}