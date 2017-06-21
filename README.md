This package provides integration with [Imarc](https://www.imarc.com)'s RBAC/ACL implementation,  [`imarc/auth`](https://github.com/imarc/auth).  It provides robust role-based authentication and configuration of permissions which can be checked dynamically on any entity implementing the `iMarc\Auth\AuthInterface`.  For more documentation regaring this interface, please see [their README](https://github.com/imarc/auth#checking-entities-implementing-authinterface).

## Installation

```
composer require hiraeth/auth
```

## Configuration

The `auth.jin` file will be copied to your `config` directory at installation time.  This file contains an example of how to add permissions to the system:

```ini
[auth]

; Aliases enable to combination of multiple permitted actions into a single action.  The key is the alias for the
; actions and the value is an array of actions which it also permits.

aliases = {
;	"manage": ["create", "read", "update", "delete"]
}

; Access control lists.  Each entry in the acls list is keyed by the role.  The value is then an object containing
; permitted actions (arrays of actions) for the keyed targets.

acls = {
;	"admin": {
;		"User" : ["manage"]
;	}
}
```

In addition to this primary file, the `[auth]` section is globally recognize, so it can be added to any configuration file in the system to add additional permissions.  This is useful if you have some permissions that are specific to some plugin functionality like a CMS or blog feature that has it's own configuration.

## Creating an Authorized Entity

Although this package includes providers for the `iMarc\Auth\Manager` as well as classes implementing `iMarc\Auth\ManagedInterface`, the developer will need to impelement `iMarc\Auth\EntityInterface` on the "authorized entity."  Usually this is a `User` class or something similar.

There are only two methods to implement:

### getRoles() : array

The `getRoles()` method should return an array containing all the roles which the authorized entity has permissions to.  As a general rule, you will probably want to return `['Anonymous']` or something similar by default, then provide an new entity to the manager if a user is not logged in.

```php
public function getRoles()
{
	if ($this->getId()) {
		return $this->roles;  // return our actual roles if we're a real/saved user
	}

	return ['Anonymous'];
}
```

If the user is logged in, these roles will need to be populated from your database or however your entities/models normally get hydrated.

By returning a default array with an "anonymous" role, developers can distinguish from an authorized user with no roles vs. someone not being logged into the site at all.

```php
$manager->is('anonymous');
```

### getPermissions() : array

In the event you need to overload more granular permissions, the `getPermissions()` method can be used to return an array of the specific permissions allowed for that user.  These will be combined with other permissions granted via their roles.  If you are only using role based authentication and don't need granular per-user permissions, this can usually return an empty array.

Otherwise, the key of the array is the target class/string, and the value is the permissions:

```php
public function getRoles()
{
	return [
		'User' => ['read', 'update'] // This user can read and update other Users
	];
}
```

## Loading the Authorized Entity

Wherever your application is responsible for loading the currently logged in user, you will want to provide the authorized entity (or an empty entity with the "anonymous" role) to the `iMarc\Auth\Manager`.  This is commonly performed in middleware or during some low-level application subsystem.  The following example from a recent website build performs this action in a relay middleware:

```php
<?php

namespace Middleware;

use iMarc;
use Fortress;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Auth
{
	/**
	 *
	 */
	protected $account = NULL;


	/**
	 *
	 */
	protected $gateway = NULL;


	/**
	 *
	 */
	public function __construct(Fortress\SessionGateway $gateway, iMarc\Auth\Manager $auth)
	{
		$this->gateway = $gateway;
		$this->auth    = $auth;
	}


	/**
	 *
	 */
	public function __invoke(Request $request, Response $response, callable $next)
	{
		$response = $this->gateway->login($request, $response);
		$account  = $this->gateway->getUser();

		$this->auth->setEntity($account);

		return $next($request, $response);
	}
}
```

In the example above, our gateway is responsible for resolving the user (wether an actual user or an empty user with an "anonymous" role), and we simply pass it off to the auth manager:

```php
$this->auth->setEntity($account);
```

In the above example the middleware is constructor injected, however, it would also be possible for the middleware to implement the `iMarc\Auth\ManagedInterface` which allows for setter injection.  Since the `hiraeth/auth` package includes a provider for this interface, the setter injection is automatically taken care of for you:

```php
public function setAuthManager(iMarc\Auth\Manager $auth)
{
	$this->auth = $auth;
}
```

Since it is possible to recover the authorized entity from the `iMarc\Auth\Manager`, we generally recommend using this in place of whatever provides your user in other dependency injected areas since this will allow you to check permissions simultaneously:

```php
$user = $this->auth->getEntity(); // will get the current authorized entity

if ($this->auth->can('edit', $user)) {
	//
	// Would execute if the user is authorized to edit themselves
	//
}
```
