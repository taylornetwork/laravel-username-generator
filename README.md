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
    - [Minimum Length](#minimum-length)
7. [Drivers](#drivers)
    - [Extending](#extending)
8. [License](#license)

## Changes

**v2.2.1**

- Fixed bug where if a custom column name was used and set using `generatorConfig` it was not being passed through.
- Fixed support for overriding the `getName` method from `GeneratesUsernames`

**v2.2**

- Added support for minimum length

**v2.1**

- Switched to a driver based conversion
- Added email support

*Note: Nothing should break with this update but let me know if you have any issues.*

**v2.0**

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

If you need to modify the data before handing it off to the generator, override the `getField` method on your model

```php
class User 
{
	...
	
	public function getField(): string
	{	
		return $this->formatted_name;
	}
	
	...
}
```

*Note: if your code still uses a custom `getName`, it will still work, however it was replaced with `getField` in v2.1 when driver support was added.*

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

### Minimum Length

If you want to enforce a minimum length for usernames generated change the `min_length` option in `config/username_generator.php` 

```php
'min_length' => 6,
```

By default if the generator generates a username less than the minimum length it will pad the end of it with a random digit between 0 and 9.

For example

```php

UsernameGenerator::generate('test');

// Would return the following where 0 is a random digit

'test00' 

```

**Alternatively you can throw an exception when the minimum length has not been reached**

In `config/username_generator.php` set

```php
'throw_exception_on_too_short' => true,
```

```php
UsernameGenerator::generate('test');
```

Would throw a `UsernameTooShortException`

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

### Extending

You can make your own custom drivers that extend `TaylorNetwork\UsernameGenerator\Drivers\BaseDriver` or override an existing one.

For example if you wanted to append `-auto` to all automatically generated usernames, you could make a new driver in `App\Drivers\AppendDriver`

```php
namespace App\Drivers;

use TaylorNetwork\UsernameGenerator\Drivers\BaseDriver;

class AppendDriver extends BaseDriver
{	
    public $field = 'name';
    
    public function afterMakeUnique(string $text): string
    {
    	return $text . '-auto';
    }
}
```

And then in `config/username_generator.php` add the driver to the top of the drivers array to use it as default.

```php
'drivers' => [
	'append' => \App\Drivers\AppendDriver::class,
        ...
    ],
```

## License

MIT
