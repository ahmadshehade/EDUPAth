<?php

namespace Modules\CourseManagement\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;


class AtLeastOneLocale implements Rule {
    /**
     * Summary of passes
     * @param mixed $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        return !empty($value['en'] ?? null)
            || !empty($value['ar'] ?? null);
    }

    /**
     * Summary of message
     * @return string
     */
    public function message(): string {
        return 'At least one language value must be provided.';
    }
}
