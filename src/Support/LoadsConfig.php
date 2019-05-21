<?php

namespace TaylorNetwork\UsernameGenerator\Support;

use Exception;

trait LoadsConfig
{
    protected $config = [];

    protected $configLoaded = false;

    public function model()
    {
        try {
            $model = $this->getConfig('model');

            return new $model();
        } catch (Exception $exception) {
            return false;
        }
    }

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

    public function loadConfig()
    {
        try {
            $this->config = config('username_generator');
        } catch (Exception $exception) {
            $this->config = include __DIR__.'/../config/username_generator.php';
        }

        $this->configLoaded = true;
    }

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

    public function config()
    {
        if (!$this->configLoaded) {
            $this->loadConfig();
        }

        return $this->config;
    }

    public function withConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }
}
