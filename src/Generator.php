<?php

namespace TaylorNetwork\UsernameGenerator;

use Illuminate\Support\Arr;
use TaylorNetwork\UsernameGenerator\Drivers\BaseDriver;
use TaylorNetwork\UsernameGenerator\Support\Exceptions\GeneratorException;
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
    public function generateFor(object $model): string
    {
        $drivers = $this->getConfig('drivers');

        if (!isset($this->driver)) {
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

        $driverInstance = new $this->driver();
        $field = $driverInstance->field;

        return $this->forwardCallToDriver($driverInstance, $model->$field);
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
     * @param string|BaseDriver $driver
     * @param string|null       $text
     *
     * @return string
     */
    protected function forwardCallToDriver($driver, ?string $text): string
    {
        if (gettype($driver) === 'string') {
            $driver = new $driver();
        }

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
