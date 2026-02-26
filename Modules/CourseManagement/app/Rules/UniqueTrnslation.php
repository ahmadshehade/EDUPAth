<?php

use Illuminate\Contracts\Validation\Rule;
use Modules\CourseManagement\Models\Category;

class UniqueTrnslation implements Rule
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function passes($attribute, $value): bool
    {
        return !Category::whereRaw("JSON_UNQUOTE(name->'$.{$this->locale}') = ?", [$value])->exists();
    }

    public function message(): string
    {
        return $this->locale === 'en' ? 'The English name must be unique.' : 'The Arabic name must be unique.';
    }
}