<?php

use TaylorNetwork\UsernameGenerator\Drivers\EmailDriver;
use TaylorNetwork\UsernameGenerator\Drivers\NameDriver;

return [

    /*
     * Should the username generated be unique?
     */
    'unique' => true,

    /*
     * The minimum length of the username.
     *
     * Set to 0 to not enforce
     */
    'min_length' => 0,

    /*
     * Want to throw a UsernameTooShort exception when too short?
     */
    'throw_exception_on_too_short' => false,

    /*
     * The maximum length of the username.
     *
     * Set to 0 to not enforce
     */
    'max_length' => 0,

    /*
     * Want to throw a UsernameTooLong exception when too long?
     */
    'throw_exception_on_too_long' => false,

    /*
     * Convert the case of the generated username
     *
     * Any of the string case functions from Illuminate\Support\Str
     *
     * 'lower', 'upper', 'title', 'ucfirst' or 'mixed' to ignore
     */
    'case' => 'lower',

    /*
     * Convert spaces in username to a separator
     */
    'separator' => '',

    /*
     * Model to check if the username is unique to.
     *
     * This is only used if unique is true
     */
    'model' => \App\Models\User::class,

    /*
     * Database field to check and store username
     */
    'column' => 'username',

    /*
     * Validate characters using RegEx
     */
    'validate_characters' => true,

    /*
     * Allowed characters from the original unconverted text
     *
     * You can add additional character sets here or by property
     * '\p{Cryillic}\p{Latin}\s ' would allow cryillic and latin characters
     *
     */
    'allowed_characters' => 'a-zA-Z\s ',

    /*
     * Character encoding
     */
    'encoding' => 'UTF-8',

    /*
     * Convert all entered text to ASCII?
     */
    'convert_to_ascii' => true,

    /*
     * Language
     *
     * This is used when converting to ASCII, ignore if convert_to_ascii is false.
     */
    'language' => 'en',

    /*
     * Run the generator even if the username was provided by the user?
     * Only used with GeneratesUsernames Trait.
     * This would be useful to maintain congruency in usernames even
     * if someone enters their own. If set to false, when the username
     * field is not empty then the generator will not run.
     */
    'generate_entered_username' => true,

    /*
     * Prefer using database REGEXP function?
     *
     * LIKE function will be used as a backup on failure.
     */
    'prefer_regexp' => false,

    /*
     * Field Map
     *
     * This is really only used when using generateFor().
     *
     * If the default "name" field for your model is not "name" you can create a
     * field map here. When looking for the "name" field, if not found, will check
     * the "fullName" field. You can have the same effect by adding a "name" attribute
     * to the model in question (ie. getNameAttribute)
     *
     * 'field_map' => [
     *      'name' => 'fullName',
     * ],
     */
    'field_map' => [],

    /*
     * Max number of attempts to perform when trying to make a username unique when the
     * fallback method is used.
     */
    'increment_max_attempts' => 100,

    /*
     * Loaded drivers for converting to a username
     */
    'drivers' => [
        'name'  => NameDriver::class,
        'email' => EmailDriver::class,
    ],

    /*
     * Add your own adjective and nouns word lists here if don't want to use the default
     */
    'dictionary' => [
        'adjectives' => [],
        'nouns'      => [],
    ],

];
