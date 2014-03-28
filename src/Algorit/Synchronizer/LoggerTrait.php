<?php namespace Algorit\Synchronizer;

use Psr\Log\LoggerInterface;

Trait LoggerTrait {

	protected $logger;

	/**
	 * Set the logger instance
	 *
	 * @param  \Psr\Log\LoggerInterface $logger
	 * @return void
	 */
	public function setLogger(LoggerInterface $logger)
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

	/**
	 * Log a message
	 *
	 * @param  string $message
	 * @param  string $level
	 * @return logger
	 */
	public function log($message, $level = 'info')
	{
		return $this->logger->$level($message);
	}

}