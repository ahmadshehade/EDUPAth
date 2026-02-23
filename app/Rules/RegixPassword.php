<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class RegixPassword implements Rule
{
    /**
     * The regular expression pattern for password validation.
     *
     * @var string
     */
    protected $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,22}$/';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        return is_string($value) && preg_match($this->pattern, $value);
        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be between 8 and 22 characters and include at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).';
    }
}
