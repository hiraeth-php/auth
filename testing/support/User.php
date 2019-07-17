<?php

/**
 *
 */
class User implements iMarc\Auth\EntityInterface
{
	/**
	 *
	 */
	public function getRoles()
	{
		return ['admin'];
	}


	/**
	 *
	 */
	public function getPermissions()
	{
		return [];
	}
}
