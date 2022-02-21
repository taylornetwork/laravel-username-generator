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
        $preferRegexp = $this->preferRegexp ?? config('username_generator.prefer_regexp', true);

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
        return static::where($this->getColumn(), $username)->get()->count() === 0;
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
        $exactMatches = static::where($this->getColumn(), $username)->get();

        if ($exactMatches) {
            return static::where($this->getColumn(), 'LIKE', $username.'%')->get();
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
        return static::where($this->getColumn(), 'REGEXP', $username.'('.$this->getSeparator().')?([0-9]*)?$')->get();
    }

    /**
     * Get the username column.
     *
     * @return string
     */
    private function getColumn(): string
    {
        return $this->usernameColumn ?? config('username_generator.column', 'username');
    }

    /**
     * Get the username separator.
     *
     * Check if the model has a custom separator in its class before checking config.
     *
     * @return string
     */
    private function getSeparator(): string
    {
        if (method_exists($this, 'generatorConfig')) {
            $generator = new Generator();
            $this->generatorConfig($generator);

            return $generator->getConfig('separator', '');
        }

        return config('username_generator.separator', '');
    }
}
