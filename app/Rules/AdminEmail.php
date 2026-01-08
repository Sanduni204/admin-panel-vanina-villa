<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AdminEmail implements ValidationRule
{

    protected string $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->role === 'admin' && ! str_ends_with($value, '@vaninavilla.com')) {
            $fail('Admin email must be a @vaninavilla.com address.');
        }
    }
}
