<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BanWord extends Constraint
{
    public function __construct(
        public string $message = 'This contains a banned word "{{ banWord }}".', 
        public array $banWords = ['spam', 'virus'],
        ?array $groups = null,
        mixed $payLoad = null)
    {
        parent::__construct(null, $groups, $payLoad);
    }

}
