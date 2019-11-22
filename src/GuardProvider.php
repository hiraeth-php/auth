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
	 * Get the interfaces for which the provider operates.
	 *
	 * @access public
	 * @return array A list of interfaces for which the provider operates
	 */
	static public function getInterfaces(): array
	{
		return [
			Auth\Guard::class
		];
	}


	/**
	 * Prepare the instance.
	 *
	 * @access public
	 * @var object $instance The unprepared instance of the object
	 * @param Hiraeth\Application $app The application instance for which the provider operates
	 * @return object The prepared instance
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
