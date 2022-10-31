<?php

namespace Hiraeth\Auth;

use Hiraeth;
use Auth;

/**
 *
 */
class GuardProvider implements Hiraeth\Provider
{
	/**
	 * {@inheritDoc}
	 */
	static public function getInterfaces(): array
	{
		return [
			Auth\Guard::class
		];
	}


	/**
	 * {@inheritDoc}
	 *
	 * @param Auth\Guard $instance
	 */
	public function __invoke(object $instance, Hiraeth\Application $app): object
	{
		$defaults = [
			'accept' => array(),
			'reject' => array()
		];

		$instance->setDefaultRule($app->getConfig('packages/auth', 'auth.guard.default', 'accept'));
		$instance->setUserRole($app->getConfig('packages/auth', 'auth.guard.role', 'User'));

		foreach ($app->getConfig('*', 'auth.guard', $defaults) as $path => $config) {
			$instance->addAcceptRules($config['accept']);
			$instance->addRejectRules($config['reject']);
		}

		return $app->share($instance);
	}
}
