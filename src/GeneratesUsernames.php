<?php

namespace TaylorNetwork\UsernameGenerator;

use Exception;

trait GeneratesUsernames
{
    /**
     * Generate a username on save if one was not set.
     */
    public static function bootGeneratesUsernames(): void
    {
        static::saving(function ($model) {
            $model->generateUsername();
        });
    }

    /**
     * Generate the username and save to model.
     */
    public function generateUsername(): void
    {
        $generator = new Generator();
        $this->generatorConfig($generator);

        $column = $generator->getConfig('column', 'username');

        if (empty($this->getAttribute($column))) {
            try {
                $this->attributes[$column] = $generator->generate($this->getField());
            } catch (Exception $e) {
                // Failed but don't halt saving the model
            }
        }
    }

    /**
     * Get the field attribute to convert to username.
     *
     * Override this method in your model to customize logic.
     *
     * @return string|null
     */
    public function getField()
    {
        // Support pre-v2 getName method overrides
        if (method_exists($this, 'getName')) {
            return $this->getName();
        }

        return $this->getAttribute($this->generatorFieldName());
    }

    /**
     * Get the name of the field to use as a name.
     *
     * @return string
     */
    public function generatorFieldName(): string
    {
        return 'name';
    }

    /**
     * Override config for the Generator instance.
     *
     * @param Generator $generator
     */
    public function generatorConfig(&$generator): void
    {
        // $generator->setConfig('separator', '_');
    }
}
