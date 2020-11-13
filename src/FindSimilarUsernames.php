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
     * @param $username
     *
     * @return mixed
     */
    public function findSimilarUsernames($username)
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

    private function searchUsingLike($username)
    {
        $exactMatches = static::where($this->getColumn(), $username)->get();

        if ($exactMatches) {
            return static::where($this->getColumn(), 'LIKE', $username.'%')->get();
        }

        return $exactMatches;
    }

    private function searchUsingRegexp($username)
    {
        $column = $this->getColumn();

        return static::whereRaw("$column REGEXP '{$username}([0-9]*)?$'")->get();
    }

    private function getColumn()
    {
        return $this->usernameColumn ?? config('username_generator.column', 'username');
    }
}
