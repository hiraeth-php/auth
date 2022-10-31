<?php

namespace Hiraeth\Auth;

use Hiraeth;
use Auth;

/**
 *
 */
class ManagedInterfaceProvider implements Hiraeth\Provider
{
	/**
	 * {@inheritDoc}
	 */
	static public function getInterfaces(): array
	{
		return [
			Auth\ManagedInterface::class
		];
	}


	/**
	 * {@inheritDoc}
	 *
	 * @param Auth\ManagedInterface $instance
	 */
	public function __invoke(object $instance, Hiraeth\Application $app): object
	{
		$instance->setAuthManager($app->get(Auth\Manager::class));

		return $instance;
	}
}
