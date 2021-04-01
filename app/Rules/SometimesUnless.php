<?php

namespace App\Rules;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\RequiredIf;

class SometimesUnless extends RequiredIf
{

    /**
     * Create a new rule instance.
     *
     * @param bool $condition
     * @param array $data
     * @param string $attribute
     * @return void
     */
    public function __construct($condition, $data, $attribute) {
        $this->condition = $condition || Arr::exists($data, $attribute);
    }

}
