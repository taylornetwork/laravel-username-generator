<?php

namespace TaylorNetwork\UsernameGenerator\Contracts;

interface HandlesConfig
{
    /**
     * Use a custom, already loaded, config.
     *
     * @param array $config
     *
     * @return $this
     */
    public function withConfig(array $config): self;

    /**
     * Get the config.
     *
     * @return array
     */
    public function config(): array;

    /**
     * Load config from file.
     *
     * @return void
     */
    public function loadConfig(): void;

    /**
     * Set a config value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setConfig(string $key, $value = null): self;

    /**
     * Get a config value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getConfig(string $key, $default = null);

    /**
     * Get an instance of the model set for unique in the config.
     *
     * @return object|null
     */
    public function model(): ?object;

    /**
     * Has the config loaded?
     *
     * @return bool
     */
    public function hasLoaded(): bool;

    /**
     * Get a Laravel config item.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function laravelConfig(string $key, $default);
}
