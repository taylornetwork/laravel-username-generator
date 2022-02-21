# Laravel Username Generator

![Stable](https://poser.pugx.org/taylornetwork/laravel-username-generator/v/stable)
![Downloads](https://poser.pugx.org/taylornetwork/laravel-username-generator/downloads)
![License](https://poser.pugx.org/taylornetwork/laravel-username-generator/license)
![Tests](https://github.com/taylornetwork/laravel-username-generator/workflows/Tests/badge.svg)
![StyleCI](https://github.styleci.io/repos/104370109/shield?branch=master)

Easily generate unique usernames for a Laravel User Model.

Works for Laravel versions above 5.5 including Laravel 9.

1. [Most Recent Update](#most-recent-update)
2. [Install](#install)
3. [Set Up](#set-up)
4. [Config](#config)
      - [Allowed Characters](#allowed-characters)
5. [Basic Usage](#basic-usage)
    - [generate($name)](#generatename)
    - [generateFor($model)](#generateformodel)
    - [GeneratesUsernames Trait](#generatesusernames-trait)
    - [UsernameGenerator Facade](#usernamegenerator-facade)
6. [Other Examples](#other-examples)
    - [With a Separator](#with-a-separator)
    - [Upper Case](#upper-case)
    - [Additional Casing Options](#additional-casing-options)
    - [Mixed Case](#mixed-case)
    - [Minimum Length](#minimum-length)
    - [Maximum Length](#maximum-length)
    - [Other Character Sets](#other-character-sets)
7. [Drivers](#drivers)
    - [Extending](#extending)
8. [License](#license)
9. [Change Log](#change-log)


## Most Recent Update

**v2.6.2**

- Fixed potential SQL injection issue when using REGEXP function.
- Fixed issue where REGEXP function was not returning correct number of similar usernames only when using a separator.
- Changed default config option `prefer_regexp` from `true` to `false`

*Updated Feb 21, 2022*



## Install

Via Composer

```bash
$ composer require taylornetwork/laravel-username-generator
```

### Publish Config

This will add the config to `config/username_generator.php`


```bash
$ php artisan vendor:publish --provider="TaylorNetwork\UsernameGenerator\ServiceProvider"
```

## Quickstart 

This section will help you get up and running fast.

The following steps will be the same for all Laravel versions and assumes you're adding the package to a new installation.

**User Model**

In `App\Models\User` (or `App\User` for Laravel 7) add the `FindSimilarUsernames` and `GeneratesUsernames` traits. 
Add `'username'` to the fillable property.

```php

// ...
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;
use TaylorNetwork\UsernameGenerator\GeneratesUsernames;

class User extends Authenticatable
{
	// ...
	use FindSimilarUsernames;
	use GeneratesUsernames;
	
	protected $fillable = [
		// ...
		'username',
	];
	
	// ...

}
```

**Database Migration**

In your `database/2014_10_12_000000_create_users_table` add a username column.

```php
class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // ...
            $table->string('username')->unique();
            // ...
        });
    }
}
```


### Laravel 8+

**Note: if you are not using Laravel Jetstream for your project, simply continue with the Laravel 7 guide below.**

Publish the Laravel Fortify config if you haven't already

```bash
$ php artisan vendor:publish --tag=fortify-config
```

In the `config/fortify.php` change the `'username' => 'email'` to `'username' => 'username'`

```php
// ...

'username' => 'username',

'email' => 'email',
    
// ... 
```

Update the login view in `resources/views/auth/login.blade.php` and replace Email with Username.

```html
<x-jet-label for="email" value="{{ __('Username') }}" />
<x-jet-input id="email" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus />
```


### Laravel 7 and below

In `config/username_generator.php` update the User model namespace to match your project.

**Using username to login**

To use the username to login instead of the email you need to add the following to your `LoginController`

```php
public function username()
{
    return 'username';
}
```


## Set Up

Add the `FindSimilarUsernames` trait on your user model (or whichever model you want to use). 

```php
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;

class User extends Authenticatable
{
    use FindSimilarUsernames;
}    
```

**Note: this is required in all cases if you want the username to be unique**


## Config

**This is in the process of being updated on the wiki**

See the [default config](https://github.com/taylornetwork/laravel-username-generator/blob/master/src/config/username_generator.php)

By default the `Generator` class has the following configuration:

| Config | Value | Type |
|:------:|:-----:|:----:|
| Unique Username | `true` | boolean |
| Separator | `''` | string (should be single character) |
| Case | `'lower'` | string (one of lower, upper, or mixed) |
| Username DB Column | `'username'` | string |
| Class | `'\App\Models\User'` | string |

The config is stored in `config/username_generator.php`

You can override config on a new instance by `new Generator([ 'unique' => false ]);` etc.

### Allowed Characters

If you need to include additional characters beyond just `'A-Za-z'` you'll need to update the `allowed_characters` config option.

You should also update `'convert_to_ascii'` to `false` if you want the result to be in the same set.

For example

```
   'allowed_characters' => 'А-Яа-яA-Za-z',   // Would also allow Cyrillic characters
   
   'allowed_characters' => 'А-Яа-яA-Za-z-_' // Includes Cyrillic, Latin characters as well as '-' and '_'
   
   'allowed_characters' => '\p{Cyrillic}\p{Greek}\p{Latin}\s ' // Includes cyrillic, greek and latin sets and all spaces
```

Please note that all characters not included in this list are removed before performing any operations. 
If you get an empty string returned double check that the characters used are included. 

## Basic Usage

#### generate($name)
Create a new instance and call `generate($name)`

```php
use TaylorNetwork\UsernameGenerator\Generator;

$generator = new Generator();

$username = $generator->generate('Test User');

```

Returns

```php
'testuser'
```

If you do not provide a name to the generate method an adjective and noun will be chosen as the name at random, using noun and adjective word lists from [alenoir/username-generator](https://github.com/alenoir/username-generator), which will then be converted to a username.

```php
use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;

$username = UsernameGenerator::generate();
```

Returns something similar to

```php
'monogamousswish'
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

If you need to modify the data before handing it off to the generator, override the `getField` method on your model. 
For example if you have a first and last name rather than a single name field, you'll need to add this to your model.

```php
class User 
{
	// ...
	
	public function getField(): string
	{	
		return $this->first_name . ' ' . $this->last_name;
	}
	
	// ...
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

### Additional Casing Options

To change the casing, we make use of the [Laravel String Helpers](https://laravel.com/docs/master/helpers#strings-method-list) so any value that changes the case will work.

**Studly (Pascal)**

```php
UsernameGenerator::setConfig([ 'case' => 'studly' ])->generate('test user');
// Returns 'TestUser'
```

When using studly case the laravel helper will remove the spaces between separate words so if a separator is used it will be overridden. 
You would need to use title case (seen below) in order to have the same effect.

```php
UsernameGenerator::setConfig([ 'case' => 'studly', 'separator' => '_' ])->generate('test user');
// Returns 'TestUser'
```

**Title**

This is the same as studly but the laravel helper will not remove spaces, so it can be used in conjunction with a separator

```php
UsernameGenerator::setConfig([ 'case' => 'title' ])->generate('test user');
// Returns 'TestUser'

UsernameGenerator::setConfig([ 'case' => 'title', 'separator' => '_' ])->generate('test user');
// Returns 'Test_User'
```

**Ucfirst**


```php
UsernameGenerator::setConfig([ 'case' => 'ucfirst' ])->generate('test user');
// Returns 'Testuser'
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

### Maximum Length

If you want to enforce a maximum length for usernames generated change the `max_length` option in `config/username_generator.php` 

```php
'max_length' => 6,
```

By default if the generator generates a username more than the maximum length it will cut it to the max length value and then try to make it unique again. 
If that becomes too long it will remove one character at a time until a unique username with the correct length has been generated.

For example

```php

UsernameGenerator::generate('test user');

'testus' 

```

**Alternatively you can throw an exception when the maximum length has been exceeded**

In `config/username_generator.php` set

```php
'throw_exception_on_too_long' => true,
```

```php
UsernameGenerator::generate('test user');
```

Would throw a `UsernameTooLongException`

### Other Character Sets

Any other character set can be used if it's encoded with UTF-8. You can either include by adding the set to the `'allowed_characters'` option.

Alternatively you can set `'validate_characters'` to `false` to not check.

**You will need to set `'convert_to_ascii'` to `false` either way**

```php
$generator = new Generator([
    'allowed_characters' => '\p{Greek}\p{Latin}\s ',
    'convert_to_ascii' => false,
]);

$generator->generate('Αυτό είναι ένα τεστ');

// Returns

'αυτόείναιένατεστ'
```

## Drivers

2 drivers are included, `NameDriver` (default) and `EmailDriver`

To use a specific driver

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

Custom drivers require a `public $field` property to be set which is the name of the field on the model to use to generate the username.

Drivers will perform the following operations in order:

```php
[
	'stripUnwantedCharacters',     // Removes all unwanted characters from the text
	'convertCase',                 // Converts the case of the field to the set value (upper, lower, mixed)
	'collapseWhitespace',          // Collapses any whitespace to a single space
	'addSeparator',                // Converts all spaces to separator
	'makeUnique',                  // Makes the username unique (if set)
]
``` 

In your custom driver you can add a method to perform an operation before or after any of the above operations. 

```php
public function beforeConvertCase(string $text): string 
{

	// --
	
}

public function afterStripUnwantedCharacters(string $text): string 
{

	// --
	
}
```

Additionally if there is any operation you want to do as the very first or last thing you can use the first and last hooks.

```php
public function first(string $text): string 
{
    // Happens first before doing anything else
}

public function last(string $text): string 
{
    // Happens last just before returning
}
```

#### Example

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

## Change Log

**v2.6.2**

- Fixed potential SQL injection issue when using REGEXP function.
- Fixed issue where REGEXP function was not returning correct number of similar usernames only when using a separator.
- Changed default config option `prefer_regexp` from `true` to `false`

**v2.6.1**

- Added support for PHP 8.1
- Set minimum PHP version to 7.4

**v2.6**

- Added first and last hook for custom drivers
- Moved the EmailDriver hook to first 
- Convert case now happens second rather than first 
- Generator now supports multibyte characters (Cyrillic, etc.)
- Text will automatically be converted to ASCII by default
- Added options for converting to ascii and validating the input string

**v2.5.1**

- Fixes issue where custom dictionary nouns and adjectives were not being used

**v2.5**

- Added maximum length check.
- Added ability for pre-filled usernames to go through generate process to allow for consistent username styles.
- Added checking for similar usernames using REGEXP or LIKE (LIKE is a fallback if REGEXP fails).
- Added a check if a username is unique as is before checking for similar ones.
- Updated `composer.json` to support PHP 7.2 and above
- Updated readme for better Laravel 8+ quickstart

**v2.4**

- This is a minor change but if you're using older versions of Laravel you may need to update your config file.
- Changed default User model from `App\User` to `App\Models\User` to mirror new Laravel versions (8.0+).
- Moved the adjective and noun word lists from the config file to a separate file, making the published config smaller and allowing you to create your own word lists if you wish.

**v2.3**

- Added support for random dictionary based usernames if a name is not provided. See the [generate](#generatename) method

**v2.2.2**

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


