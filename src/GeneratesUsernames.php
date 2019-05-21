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
            if (!$model->getAttribute(config('username_generator.column', 'username'))) {
                $model->generateUsername();
            }
        });
    }

    /**
     * Generate the username and save to model.
     */
    public function generateUsername(): void
    {
        $generator = new Generator();
        $this->generatorConfig($generator);

        try {
            $this->attributes[config('username_generator.column', 'username')] = $generator->generate($this->getField());
        } catch (Exception $e) {
            // Failed but don't halt saving the model
        }
    }

    /**
     * Get the field attribute to convert to username.
     *
     * Override this method in your model to customize logic.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->getAttribute($this->generatorFieldName());
    }

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
