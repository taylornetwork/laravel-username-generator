<?php

namespace TaylorNetwork\UsernameGenerator;

use Illuminate\Support\Arr;
use TaylorNetwork\UsernameGenerator\Drivers\BaseDriver;
use TaylorNetwork\UsernameGenerator\Support\GeneratorException;
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
     * @param string|null $text
     *
     * @return string
     */
    public function generate(?string $text = null): string
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
     * @throws GeneratorException
     *
     * @return string
     */
    public function generateFor($model): string
    {
        $drivers = $this->getConfig('drivers');

        if (!isset($this->driver)) {
            foreach ($drivers as $driver) {
                $driverInstance = new $driver();
                $field = $driverInstance->field;

                if (!empty($model->$field)) {
                    return $driverInstance->withConfig($this->config())->generate($model->$field);
                }
            }

            throw new GeneratorException('Could not find driver to use for \'generateFor\' method. Set one by using \'setDriver\' method.');
        }

        $driverInstance = new $this->driver();
        $field = $driverInstance->field;

        return $driverInstance->withConfig($this->config())->generate($model->$field);
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
        if (class_exists($driverKey)) {
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
    private function caller(string $name, $arguments)
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
