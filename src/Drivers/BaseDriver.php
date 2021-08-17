<?php

namespace TaylorNetwork\UsernameGenerator\Drivers;

use Illuminate\Support\Str;
use TaylorNetwork\UsernameGenerator\Support\Exceptions\GeneratorException;
use TaylorNetwork\UsernameGenerator\Support\Exceptions\UsernameTooLongException;
use TaylorNetwork\UsernameGenerator\Support\Exceptions\UsernameTooShortException;
use TaylorNetwork\UsernameGenerator\Support\LoadsConfig;

abstract class BaseDriver
{
    use LoadsConfig;

    /**
     * Field to access on the model.
     *
     * @var string
     */
    public $field;

    /**
     * The original text before conversion.
     *
     * @var string
     */
    protected $original;

    /**
     * Order of operations.
     *
     * Can add a before or after hook to each of these in a driver
     *
     * @var array
     */
    protected $order = [
        'toAscii',
        'stripUnwantedCharacters',
        'convertCase',
        'collapseWhitespace',
        'addSeparator',
        'makeUnique',
        'checkMinLength',
        'checkMaxLength',
    ];

    /**
     * BaseDriver constructor.
     */
    public function __construct()
    {
        $this->loadConfig();
    }

    public function getWord(string $type = 'noun'): string
    {
        $type = Str::plural(strtolower($type));
        $max = count($this->getConfig('dictionary')[$type]) - 1;

        return $this->getConfig('dictionary')[$type][rand(0, $max)];
    }

    /**
     * Generate the username.
     *
     * @param string|null $text
     *
     * @return string
     */
    public function generate(?string $text = null): string
    {
        if ($text === null) {
            $text = $this->getWord('adjective').' '.$this->getWord('noun');
        }

        $this->original = $text;

        if (method_exists($this, 'first')) {
            $text = $this->first($text);
        }

        foreach ($this->order as $method) {
            $text = $this->checkForHook($text, $method);
        }

        if (method_exists($this, 'last')) {
            $text = $this->last($text);
        }

        return $text;
    }

    /**
     * Check maximum length.
     *
     * @param string $text
     *
     * @throws GeneratorException
     * @throws UsernameTooLongException
     *
     * @return string
     */
    public function checkMaxLength(string $text): string
    {
        if ($this->getConfig('max_length', 0) > 0 && $this->getConfig('max_length', 0) > $this->getConfig('min_length')) {
            if ($this->length($text) > $this->getConfig('max_length', 0)) {
                $text = $this->tooLongAction($text);
            }
        }

        return $text;
    }

    /**
     * Check minimum length.
     *
     * @param string $text
     *
     * @throws UsernameTooShortException
     *
     * @return string
     */
    public function checkMinLength(string $text): string
    {
        if ($this->getConfig('min_length', 0) > 0) {
            if ($this->length($text) < $this->getConfig('min_length')) {
                $text = $this->tooShortAction($text);
            }
        }

        return $text;
    }

    /**
     * Action on username too short.
     *
     * @param string $text
     *
     * @throws UsernameTooShortException
     *
     * @return string
     */
    public function tooShortAction(string $text): string
    {
        if ($this->getConfig('throw_exception_on_too_short')) {
            throw new UsernameTooShortException('Generated username does not meet minimum length of '.$this->getConfig('min_length'));
        }

        while ($this->length($text) < $this->getConfig('min_length')) {
            $text .= rand(0, 9);
        }

        $text = $this->makeUnique($text);

        return $text;
    }

    /**
     * Action when username is too long.
     *
     * @param string $text
     *
     * @throws UsernameTooLongException|GeneratorException
     *
     * @return string
     */
    public function tooLongAction(string $text): string
    {
        if ($this->getConfig('throw_exception_on_too_long')) {
            throw new UsernameTooLongException('Generated username exceeds maximum length of '.$this->getConfig('max_length'));
        }

        $lengthValue = $this->getConfig('max_length') + 1;

        while ($this->length($text) > $this->getConfig('max_length')) {
            $lengthValue--;

            if ($lengthValue === 0) {
                throw new GeneratorException('Could not reduce the username to a valid length.');
            }

            $text = mb_substr($text, 0, $lengthValue, $this->getConfig('encoding'));
            $text = $this->makeUnique($text);
        }

        return $text;
    }

    /**
     * Convert the case of the username.
     *
     * @param string $text
     *
     * @return string
     */
    public function convertCase(string $text): string
    {
        $case = strtolower($this->getConfig('case'));

        try {
            return Str::$case($text);
        } catch (\BadMethodCallException $e) {
            return $text;
        }
    }

    /**
     * Remove unwanted characters.
     *
     * @param string $text
     *
     * @return string
     */
    public function stripUnwantedCharacters(string $text): string
    {
        if ($this->getConfig('validate_characters')) {
            return preg_replace('/[^'.$this->getConfig('allowed_characters').']/u', '', $text);
        }

        return $text;
    }

    /**
     * Trim spaces down.
     *
     * @param string $text
     *
     * @return string
     */
    public function collapseWhitespace(string $text): string
    {
        return preg_replace('/\s+/', ' ', trim($text));
    }

    /**
     * Replaces spaces with a separator.
     *
     * @param string $text
     *
     * @return string
     */
    public function addSeparator(string $text): string
    {
        return preg_replace('/ /', $this->getConfig('separator'), $text);
    }

    /**
     * Make the username unique.
     *
     * @param string $text
     *
     * @return string
     */
    public function makeUnique(string $text): string
    {
        if ($this->getConfig('unique') && $this->model() && method_exists($this->model(), 'findSimilarUsernames')) {
            if (method_exists($this->model(), 'isUsernameUnique') && $this->model()->isUsernameUnique($text)) {
                return $text;
            }

            if (($similar = count($this->model()->findSimilarUsernames($text))) > 0) {
                return $text.$this->getConfig('separator').$similar;
            }
        }

        return $text;
    }

    /**
     * Get the original unconverted text.
     *
     * @return string
     */
    public function getOriginal(): string
    {
        return $this->original;
    }

    /**
     * Check for a before/after hook before each step.
     *
     * @param string $text
     * @param string $next
     *
     * @return string
     */
    public function checkForHook(string $text, string $next): string
    {
        if (method_exists($this, 'before'.ucwords($next))) {
            $hook = 'before'.ucwords($next);
            $text = $this->$hook($text);
        }

        $text = $this->$next($text);

        if (method_exists($this, 'after'.ucwords($next))) {
            $hook = 'after'.ucwords($next);
            $text = $this->$hook($text);
        }

        return $text;
    }

    public function toAscii(string $text): string
    {
        if ($this->getConfig('convert_to_ascii')) {
            return Str::ascii($text, $this->getConfig('language'));
        }

        return $text;
    }

    /**
     * Check length.
     *
     * @param string $text
     *
     * @return int
     */
    protected function length(string $text): int
    {
        return mb_strlen($text, $this->getConfig('encoding'));
    }
}
