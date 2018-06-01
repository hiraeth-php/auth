<?php

namespace Hiraeth\Auth;

use iMarc\Auth;
use Hiraeth;

/**
 *
 */
class Proxy implements Auth\AuthServiceInterface
{
	/**
	 *
	 */
	protected $services = array();


	/**
	 *
	 */
	public function __construct(Hiraeth\Broker $broker)
	{
		$this->broker = $broker;
	}


	/**
	 *
	 */
	public function register($context, $service)
	{
		$this->services[strtolower($context)] = $service;
	}


	/**
	 *
	 */
	public function __invoke(Auth\Manager $manager, $context, $permission)
	{
		$target = $manager->resolve($context);

		if (isset($this->services[$target])) {
			if (is_string($this->services[$target])) {
				$this->services[$target] = $this->broker->make($this->services[$target]);
			}

			if ($this->services[$target] instanceof Auth\AuthServiceInterface) {
				return $this->services[$target]($manager, $context, $permission);
			}
		}

		return NULL;
	}
}
