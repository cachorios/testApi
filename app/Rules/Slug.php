<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Slug implements Rule
{
    protected string $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        if($this->hasUnderscore($value)){
            $this->message = 'validation.no_underscore';
            return false;
        }
        if($this->startingDashes($value)){
            $this->message = 'validation.no_starting_dashes';
            return false;
        }
        if($this->endingDashes($value)){
            $this->message = 'validation.no_ending_dashes';
            return false;
        }

        return true;
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans($this->message,['attribute'=>'data.attributes.slug']);

    }

    /**
     * @param mixed $value
     * @return false|int
     */
    public function hasUnderscore(mixed $value): int|false
    {
        return preg_match('/_/', $value);
    }

    /**
     * @param mixed $value
     * @return false|int
     */
    public function startingDashes(mixed $value): int|false
    {
        return preg_match('/^-/', $value);
    }

    /**
     * @param mixed $value
     * @return false|int
     */
    public function endingDashes(mixed $value): int|false
    {
        return preg_match('/-$/', $value);
    }
}
