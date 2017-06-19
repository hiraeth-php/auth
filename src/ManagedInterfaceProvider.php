<?php

namespace Hiraeth\Auth;

use Hiraeth;

/**
 *
 */
class ManagedInterfaceProvider implements Hiraeth\Provider
{
	/**
	 * Get the interfaces for which the provider operates.
	 *
	 * @access public
	 * @return array A list of interfaces for which the provider operates
	 */
	static public function getInterfaces()
	{
		return [
			'iMarc\Auth\ManagedInterface'
		];
	}


	/**
	 * Prepare the instance.
	 *
	 * @access public
	 * @return Checkpoint\Validation The prepared instance
	 */
	public function __invoke($instance, Hiraeth\Broker $broker)
	{
		$instance->setAuthManager($broker->make('iMarc\Auth\Manager'));

		return $instance;
	}
}
