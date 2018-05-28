# Laravel Username Generator

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

| Config | Value |
|:------:|:-----:|
| Unique Username | `true` |
| Separator | `''` |
| Case | `'lower'` |
| Username DB Column | `'username'` | 
| Class | `'\App\User'` |

The config is stored in `config/username_generator.php`

You can override any config by calling `$generator->setConfig($config, $newValue);` for the single instance

## Basic Usage

Create a new instance and call `makeUsername($name)`

```php
use TaylorNetwork\UsernameGenerator\Generator;

$generator = new Generator();

$username = $generator->makeUsername('Test User');

```

Returns

```php

'testuser'

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

## License

MIT
