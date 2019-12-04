<?php

namespace IIIRxs\ValidationErrorNormalizerBundle\Serializer;

use Symfony\Component\Validator\ConstraintViolation;

Trait ContraintViolationPathTrait {

    protected function getViolationPath(ConstraintViolation $constraintViolation)
    {
        preg_match_all("/\[([^\]]*)\]/", $constraintViolation->getPropertyPath(), $fields);

        return $fields[1][0] ?? $constraintViolation->getPropertyPath();
    }
}