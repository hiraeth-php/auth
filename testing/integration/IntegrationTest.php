<?php

use PHPUnit\Framework\TestCase;

final class IntegrationTest extends TestCase
{
	protected $app = NULL;

	/**
	 *
	 */
	public function setUp(): void
	{
		$this->app = new Hiraeth\Application(realpath(__DIR__ . '/..'));
		$this->app->exec();

		$this->auth = $this->app->get(Auth\Manager::class);
		$this->auth->setEntity(new User());
	}

	/**
	 *
	 */
	public function testBasicPermissions(): void
	{
		$this->assertEquals($this->auth->can('create', 'User'), TRUE);
		$this->assertEquals($this->auth->is('anonymous'), FALSE);
	}
}
