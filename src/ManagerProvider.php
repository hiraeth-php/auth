<?php

namespace Hiraeth\Auth;

use Hiraeth;
use Auth;

/**
 *
 */
class ManagerProvider implements Hiraeth\Provider
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
			Auth\Manager::class
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
		$signal = $app->get(Hiraeth\Utils\Signal::class);
		$defaults = [
			'acls'     => array(),
			'aliases'  => array(),
			'services' => array(),
		];

		foreach ($app->getConfig('*', 'auth', $defaults) as $path => $config) {
			$acl = $app->get(Auth\ACL::class);

			foreach ($config['aliases'] as $action => $actions) {
				$acl->alias($action, $actions);
			}

			foreach ($config['acls'] as $role => $permissions) {
				foreach ($permissions as $type => $actions) {
					$acl->allow($role, $type, $actions);
				}
			}

			foreach ($config['services'] as $target => $service) {
				$instance->register($target, $signal->create($service));
			}

			$instance->add($acl);
		}

		return $app->share($instance);
	}
}
