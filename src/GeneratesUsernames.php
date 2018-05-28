<?php

namespace TaylorNetwork\UsernameGenerator;


trait GeneratesUsernames
{
    /**
     * Generate a username on save if one was not set
     */
    public static function bootGeneratesUsernames()
    {
        static::saving(function ($model) {
            if(!$model->attributes[config('username_generator.column', 'username')]) {
                $model->generateUsername();
            }
        });
    }

    /**
     * Generate the username and save to model
     */
    public function generateUsername()
    {
        $generator = new Generator();
        $this->generatorConfig($generator);
        try {
            $this->attributes[config('username_generator.column', 'username')] = $generator->makeUsername($this->getName());
        } catch (\Exception $e) {
            // Failed but don't halt saving the model
        }
    }

    /**
     * Get the name attribute to convert to username
     *
     * Override this method in your model to customize logic.
     *
     * @return string
     */
    public function getName()
    {
        return $this->attributes['name'];
    }

    /**
     * Override config for the Generator instance
     *
     * @param Generator $generator
     */
    public function generatorConfig(&$generator)
    {
        // $generator->setConfig('separator', '_');
    }
}