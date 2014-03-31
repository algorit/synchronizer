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
	 * Call Monolog with the given method and parameters.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	protected function callMonolog($method, $parameters)
	{
		if (is_array($parameters[0]))
		{
			$parameters[0] = json_encode($parameters[0]);
		}

		return call_user_func_array(array($this->logger, $method), $parameters);
	}

	/**
	 * Write to a monolog instance.
	 *
	 * @param  string  $message
	 * @param  string  $level
	 * @return mixed
	 */
	public function write($message, $method = 'info')
	{
		if($this->logger instanceof LoggerInterface)
		{
			$method = 'add' . ucfirst($method);

			return $this->callMonolog($method, array($message));
		}

		return false;
	}

}