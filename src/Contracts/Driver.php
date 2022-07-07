<?php

namespace TaylorNetwork\UsernameGenerator\Contracts;

interface Driver
{
    /**
     * Generate a username given text.
     *
     * @param string|null $text
     *
     * @return string
     */
    public function generate(?string $text = null): string;

    /**
     * Get the original text pre-generate.
     *
     * @return string
     */
    public function getOriginal(): string;

    /**
     * Get the field name.
     *
     * @return string
     */
    public function getField(): string;
}
