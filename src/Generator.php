<?php

namespace TaylorNetwork\UsernameGenerator;

use Illuminate\Support\Arr;
use TaylorNetwork\UsernameGenerator\Contracts\HandlesConfig;
use TaylorNetwork\UsernameGenerator\Drivers\BaseDriver;
use TaylorNetwork\UsernameGenerator\Support\Exceptions\GeneratorException;
use TaylorNetwork\UsernameGenerator\Support\LoadsConfig;

class Generator implements HandlesConfig
{
    use LoadsConfig;

    /**
     * The driver to use to convert.
     *
     * @var BaseDriver
     */
    protected BaseDriver $driver;

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
        return $this->getDriver()->withConfig($this->config())->generate($text);
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
    public function generateFor(object $model): string
    {
        if (!isset($this->driver)) {
            $drivers = $this->getConfig('drivers');
            foreach ($drivers as $driver) {
                $driverInstance = new $driver();
                $field = $driverInstance->field;

                if (!empty($model->$field)) {
                    return $this->forwardCallToDriver($driverInstance, $model->$field);
                }

                if ($mappedField = $this->getMappedField($field, $model)) {
                    return $this->forwardCallToDriver($driverInstance, $model->$mappedField);
                }
            }

            throw new GeneratorException('Could not find driver to use for \'generateFor\' method. Set one by using \'setDriver\' method.');
        }

        $field = $this->getDriver()->getField();

        return $this->forwardCallToDriver($this->getDriver(), $model->$field);
    }

    /**
     * Get the usable field on the model from the field map.
     *
     * @param string $field
     * @param object $model
     *
     * @return string|null
     */
    protected function getMappedField(string $field, object $model): ?string
    {
        $map = $this->getConfig('field_map', []);

        if (array_key_exists($field, $map)) {
            if (is_array($map[$field])) {
                foreach ($map[$field] as $mappedField) {
                    if (!empty($model->$mappedField)) {
                        return $mappedField;
                    }
                }
            } else {
                $mappedField = $map[$field];

                return empty($model->$mappedField) ?: $mappedField;
            }
        }

        return null;
    }

    /**
     * Forward the generate call to the selected driver.
     *
     * @param BaseDriver  $driver
     * @param string|null $text
     *
     * @return string
     */
    protected function forwardCallToDriver(BaseDriver $driver, ?string $text): string
    {
        return $driver->withConfig($this->config())->generate($text);
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
        $driverClass = class_exists($driverKey) ? $driverKey : $this->getConfig('drivers')[$driverKey];
        $this->driver = new $driverClass();

        return $this;
    }

    /**
     * Get the current Driver or default.
     *
     * @return BaseDriver
     */
    public function getDriver(): BaseDriver
    {
        if (!isset($this->driver)) {
            $driverClass = Arr::first($this->getConfig('drivers'));
            $this->driver = new $driverClass();
        }

        return $this->driver;
    }

    /**
     * __call.
     *
     * @param string $name
     * @param mixed  $arguments
     *
     * @return mixed
     */
    public function __call(string $name, $arguments)
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
    public static function __callStatic(string $name, $arguments)
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
