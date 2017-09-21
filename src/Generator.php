<?php

namespace TaylorNetwork\UsernameGenerator;

use Exception;

class Generator
{
    /**
     * Make a unique username
     * 
     * @var boolean
     */
    protected $unique;

    /**
     * Character case to use
     * 
     * @var string
     */
    protected $case;

    /**
     * Word separator to use
     * 
     * @var string
     */
    protected $separator;

    /**
     * Instance of class
     * 
     * @var object
     */
    protected $class;

    /**
     * Name to convert to username
     * 
     * @var string
     */
    protected $name;

    /**
     * The finished product
     * 
     * @var string
     */
    protected $username;

    /**
     * UsernameGenerator constructor.
     * 
     * @param string|null $name
     */
    public function __construct ($name = null)
    {
        $this->setName($name);

        $this->unique = config('username_generator.unique', true);
        $this->case = strtolower(config('username_generator.case', 'lower'));
        $this->separator = config('username_generator.separator', '-');

        $class = config('username_generator.class');
        $this->class = new $class;
    }

    /**
     * Make the username!
     * 
     * @param string|null $name
     * @return string
     * @throws Exception
     */
    public function makeUsername ($name = null)
    {
        $this->setName($name);
        $this->checkParams();
        
        $this->username = $this->name;
        
        $this->separate();
        $this->convertCase();
        
        if ($this->unique)
        {
            $this->makeUnique();
        }

        return $this->username;
    }

    /**
     * Set the name property
     * 
     * @param string|null $name
     */
    public function setName ($name = null)
    {
        if ($name !== null)
        {
            $this->name = $name;
        }
    }

    /**
     * Make the username unique
     */
    protected function makeUnique ()
    {
        $similar = count($this->class->findSimilarUsernames($this->username));

        if ($similar > 0)
        {
            $this->username = $this->username . $this->separator . $similar;
        }
    }

    /**
     * Separate the words in the name
     */
    protected function separate ()
    {
        $this->username = str_replace(' ', $this->separator, $this->username);
    }

    /**
     * Convert the characters to upper/lower case
     */
    protected function convertCase ()
    {
        switch ($this->case)
        {
            case 'lower':
                $this->username = strtolower($this->username);
                break;

            case 'upper':
                $this->username = strtoupper($this->username);
                break;

            case 'mixed':
                break;
        }
    }

    /**
     * Check to make sure properties are valid
     * 
     * @throws Exception
     */
    protected function checkParams ()
    {
        $errors = [];

        if (!isset($this->name))
        {
            $errors[] = '- Name is not set on class!';
        }

        if (gettype($this->unique) !== 'boolean')
        {
            $errors[] = '- Unique must be a boolean value!';
        }

        switch ($this->case)
        {
            case 'lower':
            case 'upper':
            case 'mixed':
                break;
            default:
                $errors[] = '- ' . $this->case . ' is not a valid value for case.';
                break;
        }

        if (!empty($errors))
        {
            throw new Exception("UsernameGenerator failed with the following error(s):\n" . implode("\n", $errors));
        }
    }

    /**
     * Set a config value
     *
     * @param $config
     * @param $value
     */
    public function setConfig($config, $value = null)
    {
        if(gettype($config) === 'array') {
            foreach($config as $field => $value) {
                $this->$field = $value;
            }
        } else {
            $this->$config = $value;
        }
    }

    /**
     * Get a property
     * 
     * @param string $property
     * @return mixed
     */
    public function __get ($property)
    {
        return $this->$property;
    }
}