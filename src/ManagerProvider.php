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
	 * {@inheritDoc}
	 */
	static public function getInterfaces(): array
	{
		return [
			Auth\Manager::class
		];
	}


	/**
	 * {@inheritDoc}
	 *
	 * @param Auth\Manager $instance
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
					$controls = array();

					foreach ($actions as $key => $value) {
						if (is_numeric($key)) {
							$controls[] = $value;

							continue;
						}

						if ($value) {
							$controls[] = $key;

							continue;
						}
					}

					$acl->allow($role, $type, $controls);
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
