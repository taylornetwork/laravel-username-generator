<?php

namespace TaylorNetwork\UsernameGenerator\Support;

use Exception;

trait LoadsConfig
{
    /**
     * Loaded config.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Has the config been loaded?
     *
     * @var bool
     */
    protected $configLoaded = false;

    /**
     * Access an instance of the unique to model.
     *
     * @return object|null
     */
    public function model(): ?object
    {
        try {
            $model = $this->getConfig('model');

            return new $model();
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Get a config value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getConfig(string $key, $default = null)
    {
        if (!$this->configLoaded) {
            $this->loadConfig();
        }

        $strippedKey = str_replace('username_generator.', '', $key);

        if (array_key_exists($strippedKey, $this->config)) {
            return $this->config[$strippedKey];
        }

        return self::laravelConfig($key, $default);
    }

    /**
     * Set config.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return LoadsConfig
     */
    public function setConfig($key, $value = null): self
    {
        if (!$this->configLoaded) {
            $this->loadConfig();
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                if (array_key_exists($k, $this->config())) {
                    $this->config[$k] = $v;
                }
            }
        } else {
            if (array_key_exists($key, $this->config())) {
                $this->config[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Load config from Laravel config file.
     */
    public function loadConfig(): void
    {
        try {
            $this->config = config('username_generator');
        } catch (Exception $exception) {
            $this->config = include __DIR__.'/../config/username_generator.php';
        }

        $this->configLoaded = true;
        $this->checkForDictionary();
    }

    /**
     * Get Laravel config.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function laravelConfig(string $key, $default = null)
    {
        try {
            if (!preg_match('/^username_generator\.', $key)) {
                $key = 'username_generator.'.$key;
            }

            return config($key, $default);
        } catch (Exception $exception) {
            return $default;
        }
    }

    /**
     * All the loaded config.
     *
     * @return array
     */
    public function config(): array
    {
        if (!$this->configLoaded) {
            $this->loadConfig();
        }

        return $this->config;
    }

    /**
     * Import existing config.
     *
     * @param array $config
     *
     * @return LoadsConfig
     */
    public function withConfig(array $config): self
    {
        $this->config = $config;
        $this->configLoaded = true;

        return $this;
    }

    /**
     * Adds the the default dictionary words to the config if not set.
     */
    private function checkForDictionary(): void
    {
        if ($this->configLoaded) {
            $dictionary = include __DIR__.'/../config/dictionary.php';

            if (empty($this->config['dictionary']['adjectives'])) {
                $this->config['dictionary']['adjectives'] = $dictionary['adjectives'];
            }

            if (empty($this->config['dictionary']['nouns'])) {
                $this->config['dictionary']['nouns'] = $dictionary['nouns'];
            }
        }
    }
}
