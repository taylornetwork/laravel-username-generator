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

## License

MIT
