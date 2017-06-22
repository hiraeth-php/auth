[Imarc's RBAC/ACL implementation](https://github.com/imarc/auth) provides robust role-based authentication and configuration of permissions which can be checked dynamically on any entity as well as by providing configuration based access control lists.

## Installation

```
composer require hiraeth/auth
```

The `auth.jin` configuration will be automatically copied to your `config` directory via [opus](https://github.com/imarc/opus).

## Delegates

No delegates are included in this package.

## Providers

| Operative Interface            | Provides
|--------------------------------|-----------------------------------------------------------------------------
| `iMarc\Auth\ManagedInterface`  | `iMarc\Auth\Manager`
| `iMarc\Auth\Manager`           | Configuration of access control lists

## Configuration

```ini
[auth]

; Aliases enable to combination of multiple permitted actions into a single
; action.  The key is the alias for the actions and the value is an array of
; actions which it also permits.

aliases = {
;	"manage": ["create", "read", "update", "delete"]
}

; Access control lists.  Each entry in the acls list is keyed by the role.
; The value is then an object containing permitted actions (arrays of actions)
; for the keyed targets.

acls = {
;	"admin": {
;		"User" : ["manage"]
;	}
}
```

The `[auth]` section is globally recognized, so it can be added to any configuration file in the system to add additional roles and permissions.  Each `[auth]` section constitutes a distinct ACL, so aliases will only apply to the acls defined in the same section.

## Usage

See [the Auth documentation](https://github.com/imarc/auth) for more information on how to use the auth manager and check roles/permissions.
