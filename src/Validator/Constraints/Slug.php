<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/** @Annotation */
class Slug extends Constraint
{
    public $message = 'The string "{{ string }}" contains an illegal character: it can only contain letters or numbers.';

    public function getTargets()
    {
        //return self::CLASS_CONSTRAINT;
        return [
            self::PROPERTY_CONSTRAINT,
            self::CLASS_CONSTRAINT
        ];
    }

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}