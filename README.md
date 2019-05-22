# Laravel Username Generator

Easily generate unique usernames for a Laravel User Model

1. [Changes](#changes)
2. [Install](#install)
3. [Set Up](#set-up)
4. [Config](#config)
5. [Basic Usage](#basic-usage)
    - [generate($name)](#generatename)
    - [generateFor($model)](#generateformodel)
    - [GeneratesUsernames Trait](#generatesusernames-trait)
    - [UsernameGenerator Facade](#usernamegenerator-facade)
6. [Other Examples](#other-examples)
    - [With a Separator](#with-a-separator)
    - [Upper Case](#upper-case)
    - [Mixed Case](#mixed-case)
7. [Drivers](#drivers)
8. [License](#license)

## Changes

As of v2.1

- Switched to a driver based conversion
- Added email support

*Note: Nothing should break with this update but let me know if you have any issues.*

As of v2.0 

- Removed support for deprecated `makeUsername` method
- `Generator` will now only accept an array of config as the optional constructing arguments
- Added `UsernameGenerator` facade


## Install

Via Composer

```bash
$ composer require taylornetwork/laravel-username-generator
```

## Set Up

Add the `FindSimilarUsernames` trait on your user model (or whichever model you want to use).

```php

// app/User.php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;

class User extends Authenticatable
{
    use Notifiable, FindSimilarUsernames;

    // --

}    
```

### Use username to login

To use the username to login instead of the email you need to add the following to your `LoginController`

```php
public function username()
{
    return 'username';
}
```

See Username Customization in [Laravel Authentication Docs](https://laravel.com/docs/5.8/authentication#included-authenticating)


## Config

By default the `Generator` class has the following configuration:

| Config | Value | Type |
|:------:|:-----:|:----:|
| Unique Username | `true` | boolean |
| Separator | `''` | string (should be single character) |
| Case | `'lower'` | string (one of lower, upper, or mixed) |
| Username DB Column | `'username'` | string |
| Class | `'\App\User'` | string |

The config is stored in `config/username_generator.php`

You can override config on a new instance by `new Generator([ 'unique' => false ]);` etc.

## Basic Usage

#### generate($name)
Create a new instance and call `generate($name)`

*Note: This has replaced, the old `makeUsername` method which ~~is deprecated but still currently has support~~ no longer has support (as of v2.0)*

```php
use TaylorNetwork\UsernameGenerator\Generator;

$generator = new Generator();

$username = $generator->generate('Test User');

```

Returns

```php

'testuser'

```

#### generateFor($model)
Create a new instance and call `generateFor($model)`

This will access the model's `name` property and convert it to a username.

```php
use TaylorNetwork\UsernameGenerator\Generator;

class User
{
	public $name = 'Some Other User';
	
	public function getUsername()
	{
		$generator = new Generator();
		return $generator->generateFor($this);
	}
}

```

Returns

```php

'someotheruser'

```


## GeneratesUsernames Trait

This package also comes with a `GeneratesUsernames` trait that you can add to your model and it will automatically call the username generator when the model is saving without the specified username column.

*Note: you will also need to include the `FindSimilarUsernames` trait either way*

```php
use TaylorNetwork\UsernameGenerator\GeneratesUsernames;
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;

class User 
{
	use FindSimilarUsernames, GeneratesUsernames;
}

```

You can also add custom config to call before the username is generated.

Override the `generatorConfig` method in your model

```php
use TaylorNetwork\UsernameGenerator\GeneratesUsernames;
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;

class User 
{
	use FindSimilarUsernames, GeneratesUsernames;
	
	public function generatorConfig(&$generator) 
	{
		$generator->setConfig([ 'separator' => '_' ]);
	}
}

```

## UsernameGenerator Facade

This package includes a `UsernameGenerator` facade for easy access

```php
UsernameGenerator::generate('Test User');

UsernameGenerator::generateFor($user);

UsernameGenerator::setConfig([ 'separator' => '_' ])->generate('Test User');
```

## Other Examples

### With a Separator

```php
$generator = new Generator([ 'separator' => '_' ]);
$generator->generate('Some User');

```

Returns 

```
some_user
```

### Upper Case

```php
$generator = new Generator([ 'case' => 'upper' ]);
$generator->generate('Some User');

```

Returns 

```
SOMEUSER
```

### Mixed Case

```php
$generator = new Generator([ 'case' => 'mixed' ]);
$generator->generate('Some User');

```

Returns 

```
SomeUser
```

---

Note: Mixed case will just ignore changing case altogether

```php
$generator = new Generator([ 'case' => 'mixed' ]);
$generator->generate('SoMe WeIrD CapitaliZation');

```

Returns 

```
SoMeWeIrDCapitaliZation
```

*Note: if you pass an invalid value for the `case` option, mixed case will be used.*

## Drivers

2 drivers are included, `NameDriver` (default) and `EmailDriver`

To use a specific driver, if none is specified the default is used.

```php
UsernameGenerator::usingEmail()->generate('testuser@example.com');

// Returns

'testuser'
```
OR
```php
$generator = new Generator();
$generator->setDriver('email');
$generator->generate('test.user77@example.com');

// Returns

'testuser'
```

## License

MIT
