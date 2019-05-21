<?php

namespace TaylorNetwork\UsernameGenerator;

use Illuminate\Support\Arr;
use TaylorNetwork\UsernameGenerator\Support\LoadsConfig;

class Generator
{
    use LoadsConfig;

    protected $driver;

    public function __construct(array $config = [])
    {
        $this->loadConfig();
        $this->setConfig($config);
    }

    public function generate(string $text): string
    {
        if (!isset($this->driver)) {
            $this->driver = Arr::first($this->getConfig('drivers'));
        }

        return (new $this->driver())->withConfig($this->config())->generate($text);
    }

    public function generateFor($model): string
    {
        $drivers = $this->getConfig('drivers');

        if (!isset($this->driver)) {
            foreach ($drivers as $key => $driver) {
                if (!empty($model->$key)) {
                    $field = $key;
                    break;
                }
            }

            if (!isset($field)) {
                return false;
            }

            return (new $drivers[$field]())->withConfig($this->config())->generate($model->$field);
        }

        $field = array_search($this->driver, $drivers);

        return (new $this->driver())->withConfig($this->config())->generate($model->$field);
    }

    public function setDriver(string $driverKey): self
    {
        $this->driver = $this->getConfig('drivers')[$driverKey];

        return $this;
    }

    public function __call($name, $arguments)
    {
        return $this->caller($name, $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return (new static())->caller($name, $arguments);
    }

    private function caller($name, $arguments)
    {
        $drivers = $this->getConfig('drivers');

        if (substr($name, 0, 5) === 'using') {
            $driverName = strtolower(substr($name, 5));

            if (array_key_exists($driverName, $drivers)) {
                return (new $drivers[$driverName]())->withConfig($this->config());
            }
        }

        return false;
    }
}
