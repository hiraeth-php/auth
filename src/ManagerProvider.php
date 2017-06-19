<?php

namespace Hiraeth\Auth;

use Hiraeth;

/**
 *
 */
class ManagerProvider implements Hiraeth\Provider
{
	/**
	 * The Hiraeth configuration
	 *
	 * @access protected
	 * @var Hiraeth\Configuration
	 */
	protected $config = NULL;


	/**
	 * Get the interfaces for which the provider operates.
	 *
	 * @access public
	 * @return array A list of interfaces for which the provider operates
	 */
	static public function getInterfaces()
	{
		return [
			'iMarc\Auth\Manager'
		];
	}


	/**
	 * Instantiate a new provider
	 *
	 * @access public
	 * @param Hiraeth\Configuration $config The hiraeth configuration
	 * @return void
	 */
	public function __construct(Hiraeth\Configuration $config)
	{
		$this->config = $config;
	}


	/**
	 * Prepare the instance.
	 *
	 * @access public
	 * @return Checkpoint\Validation The prepared instance
	 */
	public function __invoke($instance, Hiraeth\Broker $broker)
	{
		foreach ($this->config->get('*', 'auth', array()) as $config => $acls) {
			$acl = $broker->make('iMarc\Auth\ACL');

			foreach ($this->config->get($config, 'auth.aliases', array()) as $action => $actions) {
				$acl->alias($action, $actions);
			}

			foreach ($this->config->get($config, 'auth.acls', array()) as $role => $permissions) {
				foreach ($permissions as $type => $actions) {
					$acl->allow($role, $type, $actions);
				}
			}

			$instance->add($acl);
		}

		$broker->share($instance);

		return $instance;
	}
}
