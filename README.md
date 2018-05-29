# Laravel Username Generator

Easily generate unique usernames for a Laravel User Model

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

*Note: This has replaced, the old `makeUsername` method which is deprecated but still currently has support*

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
use TaylornNetwork\UsernameGenerator\FindSimilarUsernames;

class User 
{
	use FindSimilarUsernames, GeneratesUsernames;
}

```

You can also add custom config to call before the username is generated.

Override the `generatorConfig` method in your model

```php
use TaylorNetwork\UsernameGenerator\GeneratesUsernames;
use TaylornNetwork\UsernameGenerator\FindSimilarUsernames;

class User 
{
	use FindSimilarUsernames, GeneratesUsernames;
	
	public function generatorConfig(&$generator) 
	{
		$generator->setConfig([ 'separator' => '_' ]);
	}
}

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

## License

MIT
