<?php

namespace App\Http\Middleware;

use GrahamCampbell\SecurityCore\Security;
use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class Sanitizer extends Middleware
{
    protected Security $sanitizer;

    public function __construct(Security $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function transform($key, $value): mixed
    {
        $value = parent::transform($key, $value);
        if (! is_string($value)) {
            return $value;
        }

        return $this->sanitizer->clean($value);
    }
}
