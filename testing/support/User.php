<?php

/**
 *
 */
class User implements Auth\EntityInterface
{
	/**
	 *
	 */
	public function getRoles(): array
	{
		return ['admin'];
	}


	/**
	 *
	 */
	public function getPermissions(): array
	{
		return [];
	}
}
