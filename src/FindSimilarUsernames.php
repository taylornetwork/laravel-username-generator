<?php

namespace TaylorNetwork\UsernameGenerator;

use Illuminate\Database\QueryException;

trait FindSimilarUsernames
{
    /**
     * Find similar usernames.
     *
     * This assumes you are using Eloquent with Laravel, if not, override this
     * function in your class.
     *
     * @param string $username
     *
     * @return mixed
     */
    public function findSimilarUsernames(string $username)
    {
        $preferRegexp = $this->preferRegexp ?? $this->getModelGeneratorConfig()->getConfig('prefer_regexp', false);

        if (!$preferRegexp) {
            return $this->searchUsingLike($username);
        }

        try {
            return $this->searchUsingRegexp($username);
        } catch (QueryException $exception) {
            return $this->searchUsingLike($username);
        }
    }

    /**
     * Check if the username is unique as is.
     *
     * @param string $username
     *
     * @return bool
     */
    public function isUsernameUnique(string $username): bool
    {
        return static::where($this->getUsernameColumnName(), $username)->get()->count() === 0;
    }

    /**
     * Search for similar usernames using LIKE.
     *
     * @param string $username
     *
     * @return mixed
     */
    private function searchUsingLike(string $username)
    {
        $exactMatches = static::where($this->getUsernameColumnName(), $username)->get();

        if ($exactMatches) {
            return static::where($this->getUsernameColumnName(), 'LIKE', $username.'%')->get();
        }

        return $exactMatches;
    }

    /**
     * Search for similar usernames using REGEXP.
     *
     * This will fail on some databases, so like should be used as a backup.
     *
     * @param string $username
     *
     * @return mixed
     */
    private function searchUsingRegexp(string $username)
    {
        return static::where($this->getUsernameColumnName(), 'REGEXP', $username.'('.$this->getSeparator().')?([0-9]*)?$')->get();
    }

    /**
     * Alias for getUsernameColumnName for backwards compatibility.
     *
     * @return string
     *
     * @deprecated use getUsernameColumnName()
     */
    private function getColumn(): string
    {
        return $this->getUsernameColumnName();
    }

    /**
     * Get the username column name.
     *
     * @return string
     */
    public function getUsernameColumnName(): string
    {
        return $this->usernameColumn ?? $this->getModelGeneratorConfig()->getConfig('column', 'username');
    }

    /**
     * Get the username separator.
     *
     * @return string
     */
    private function getSeparator(): string
    {
        return $this->getModelGeneratorConfig()->getConfig('separator', '');
    }

    /**
     * Get the model specific generator config.
     *
     * Since a model could extend the GeneratesUsernames trait, we need to check if
     * it has any specific config that would change the behaviour of this trait.
     *
     * Eventually will deprecate the generatorConfig and change it to simply return an
     * array that will then be passed to the generator.
     *
     * @return Generator
     */
    private function getModelGeneratorConfig(): Generator
    {
        $generator = new Generator();

        if (method_exists($this, 'generatorConfig')) {
            $this->generatorConfig($generator);
        }

        return $generator;
    }
}
