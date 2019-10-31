<?php

namespace TaylorNetwork\UsernameGenerator;

use Illuminate\Support\Arr;
use TaylorNetwork\UsernameGenerator\Drivers\BaseDriver;
use TaylorNetwork\UsernameGenerator\Support\LoadsConfig;

class Generator
{
    use LoadsConfig;

    /**
     * The driver to use to convert.
     *
     * @var BaseDriver
     */
    protected $driver;

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
     * Generate a username.
     *
     * @param string $text
     *
     * @return string
     */
    public function generate(string $text = null): string
    {
        if (!isset($this->driver)) {
            $this->driver = Arr::first($this->getConfig('drivers'));
        }

        return (new $this->driver())->withConfig($this->config())->generate($text);
    }

    /**
     * Generate a username from a given model.
     *
     * @param object $model
     *
     * @return string
     */
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

    /**
     * Set the driver to use.
     *
     * @param string $driverKey
     *
     * @return Generator
     */
    public function setDriver(string $driverKey): self
    {
        if(class_exists($driverKey)) {
            $this->driver = $driverKey;
        } else {
            $this->driver = $this->getConfig('drivers')[$driverKey];
        }

        return $this;
    }

    /**
     * __call.
     *
     * @param string $name
     * @param mixed  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->caller($name, $arguments);
    }

    /**
     * __callStatic.
     *
     * @param string $name
     * @param mixed  $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return (new static())->caller($name, $arguments);
    }

    /**
     * Handle __call and __callStatic.
     *
     * @param string $name
     * @param mixed  $arguments
     *
     * @return mixed
     */
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
