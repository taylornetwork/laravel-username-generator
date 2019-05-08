<?php

namespace TaylorNetwork\UsernameGenerator;

use Exception;

class Generator
{
    /**
     * Make a unique username.
     *
     * @var bool
     */
    protected $unique = true;

    /**
     * Character case to use.
     *
     * @var string
     */
    protected $case = 'lower';

    /**
     * Word separator to use.
     *
     * @var string
     */
    protected $separator = '';

    /**
     * Username database column.
     *
     * @var string
     */
    protected $column = 'username';

    /**
     * Instance of model.
     *
     * @var object
     */
    protected $model;

    /**
     * Name to convert to username.
     *
     * @var string
     */
    protected $name;

    /**
     * The finished product.
     *
     * @var string
     */
    protected $username;

    /**
     * Generator constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->loadConfig();
        $this->setConfig($config);
    }

    /**
     * Set config.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return $this
     */
    public function setConfig($key, $value = null)
    {
        if (gettype($key) === 'array') {
            foreach ($key as $k => $v) {
                if ($k === 'model' && gettype($v) === 'string') {
                    $v = new $v();
                }

                $this->$k = $v;
            }
        } else {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Generate a username from a name.
     *
     * @param string $name
     *
     * @return string
     */
    public function generate($name = null)
    {
        if ($name) {
            $this->name = $name;
        }

        // Defaults to mixed case if value is incorrect
        if (strtolower($this->case) === 'lower' || strtolower($this->case) === 'upper') {
            $case = 'strto'.strtolower($this->case);
            $this->name = $case($this->name);
        }

        // Remove all unwanted characters
        $this->username = preg_replace('/[^a-zA-Z ]/', '', $this->name);

        // Trim multiple spaces down to a single space
        $this->username = preg_replace('/\s+/', ' ', $this->username);

        // Trim any leading or trailing spaces
        $this->username = trim($this->username);

        // Replace spaces with separator
        $this->username = preg_replace('/ /', $this->separator, $this->username);

        if ($this->unique && $this->model && method_exists($this->model, 'findSimilarUsernames')) {
            if (($similar = count($this->model->findSimilarUsernames($this->username))) > 0) {
                $this->username .= $this->separator.$similar;
            }
        }

        return $this->username;
    }

    /**
     * Generate a username for a model.
     *
     * @param object $model
     *
     * @return string
     */
    public function generateFor($model)
    {
        if (gettype($model) === 'string') {
            $model = new $model();
        }

        return $this->generate($model->name);
    }

    /**
     * Returns a new instance.
     *
     * @param array $config
     *
     * @return Generator
     */
    public static function instance(array $config = [])
    {
        return new static($config);
    }

    /**
     * Load config if the config function exists.
     */
    protected function loadConfig()
    {
        if (function_exists('config')) {
            try {
                $this->unique = config('username_generator.unique');
                $this->case = config('username_generator.case');
                $this->separator = config('username_generator.separator');
                $this->column = config('username_generator.column');
                $model = config('username_generator.model');
                $this->model = new $model();
            } catch (Exception $e) {
                // Ignore config loading errors...
            }
        }
    }

    /**
     * __get.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }
}
